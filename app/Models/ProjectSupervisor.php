<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProjectSupervisor extends Model
{
    protected $table = 'project_supervisor';

    protected $fillable = [
        'project_id',
        'supervisor_type',
        'supervisor_id',
        'external_supervisor_name',
        'order_rank',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($projectSupervisor) {
            // Generate project number if project doesn't have one yet
            if ($projectSupervisor->project && empty($projectSupervisor->project->project_number)) {
                $projectSupervisor->project->generateProjectNumber();
            }
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function supervisor(): MorphTo
    {
        // Uses supervisor_type & supervisor_id automatically
        // Returns null for external supervisors (where supervisor_type is null)
        return $this->morphTo();
    }
    
    /**
     * Check if this is an external supervisor
     */
    public function isExternal(): bool
    {
        return empty($this->supervisor_type) && !empty($this->external_supervisor_name);
    }
    
    /**
     * Get the supervisor name (either from the model or external name)
     */
    public function getNameAttribute(): string
    {
        if ($this->isExternal()) {
            return $this->external_supervisor_name;
        }
        
        return $this->supervisor?->name ?? '';
    }
}