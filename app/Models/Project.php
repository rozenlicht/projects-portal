<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Project extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'student_name',
        'student_email',
        'featured_image',
        'short_description',
        'richtext_content',
        'project_owner_id',
        'organization_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProjectType::class,
        ];
    }

    // Section and Group accessors based on first supervisor (if available)
    public function getSectionAttribute(): ?Section
    {
        return $this->supervisors->first()?->group?->section;
    }

    public function getGroupAttribute(): ?Group
    {
        return $this->supervisors->first()?->group;
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

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_supervisor')
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
}

enum ProjectType: string
{
    case Internship = 'internship';
    case BachelorThesis = 'bachelor_thesis';
    case MasterThesis = 'master_thesis';
}
