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
        'order_rank',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function supervisor(): MorphTo
    {
        // Uses supervisor_type & supervisor_id automatically
        return $this->morphTo();
    }
}