<?php

namespace App\Models;

use App\Enums\PublicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Project extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'project_number',
        'student_name',
        'student_email',
        'featured_image',
        'short_description',
        'richtext_content',
        'project_owner_id',
        'organization_id',
        'publication_status',
    ];

    protected $casts = [
        'publication_status' => PublicationStatus::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($project) {
            if (empty($project->project_number)) {
                $project->generateProjectNumber();
            }
        });
    }

    // Section and Group accessors based on first supervisor (if available)
    public function getSectionAttribute(): ?Section
    {
        $firstSupervisorLink = $this->supervisorLinks->first();
        $firstSupervisor = $firstSupervisorLink ? $firstSupervisorLink->supervisor : null;
        return $firstSupervisor && method_exists($firstSupervisor, 'group') ? $firstSupervisor->group?->section : null;
    }

    public function getGroupAttribute(): ?Group
    {
        $firstSupervisorLink = $this->supervisorLinks->first();
        $firstSupervisor = $firstSupervisorLink ? $firstSupervisorLink->supervisor : null;
        return $firstSupervisor && method_exists($firstSupervisor, 'group') ? $firstSupervisor->group : null;
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_owner_id');
    }

    public function supervisorLinks(): HasMany
    {
        return $this->hasMany(ProjectSupervisor::class)->orderBy('order_rank');
    }

    public function supervisors(): MorphToMany
    {
        return $this->morphToMany(User::class, 'supervisor', 'project_supervisor', 'project_id', 'supervisor_id')
            ->withPivot('order_rank')
            ->orderByPivot('order_rank');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(ProjectType::class);
    }

    public function getIsTakenAttribute(): bool
    {
        return !empty($this->student_name) || !empty($this->student_email);
    }

    public function scopeAvailable($query)
    {
        return $query->whereNull('student_name')
            ->whereNull('student_email');
    }

    public function scopePast($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('student_name')
                ->orWhereNotNull('student_email');
        });
    }

    /**
     * Generate a unique project number based on year, section, and group.
     * Format: YY + Section abbrev_id + Group abbrev_id + 5-digit number
     * Example: 25MOMREM0035
     */
    public function generateProjectNumber(): ?string
    {
        // Reload supervisor links to ensure we have the latest data
        $this->load('supervisorLinks.supervisor.group.section');

        $firstSupervisorLink = $this->supervisorLinks->first();
        if (!$firstSupervisorLink) {
            return null;
        }

        $supervisor = $firstSupervisorLink->supervisor;
        if (!$supervisor) {
            return null;
        }

        // Only User supervisors have groups (external supervisors don't)
        if (!method_exists($supervisor, 'group') || !$supervisor->group) {
            return null;
        }

        $group = $supervisor->group;
        $section = $group->section;

        if (!$section || !$section->abbrev_id || !$group->abbrev_id) {
            return null;
        }

        // Get year from project creation date (2 digits), fallback to current year if not set
        $year = $this->created_at ? $this->created_at->format('y') : date('y');

        // Build prefix: YY + Section abbrev_id + Group abbrev_id
        $prefix = $year . $section->abbrev_id . $group->abbrev_id;

        // Find the highest existing project number with this prefix
        $lastProject = static::where('project_number', 'like', $prefix . '%')
            ->orderBy('project_number', 'desc')
            ->first();

        // Extract the number part and increment
        $nextNumber = 1;
        if ($lastProject && $lastProject->project_number) {
            // Extract the 5-digit number from the end
            $numberPart = substr($lastProject->project_number, strlen($prefix));
            if (is_numeric($numberPart)) {
                $nextNumber = (int) $numberPart + 1;
            }
        }

        // Format as 5-digit number with leading zeros
        $projectNumber = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Update the project number
        $this->updateQuietly(['project_number' => $projectNumber]);

        return $projectNumber;
    }
}
