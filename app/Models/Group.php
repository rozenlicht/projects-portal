<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory, Cachable;

    protected $fillable = [
        'name',
        'section_id',
        'abbrev_id',
        'external_url',
        'group_leader_id',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'group_leader_id');
    }
}
