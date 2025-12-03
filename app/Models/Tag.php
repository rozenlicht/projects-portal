<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'category' => TagCategory::class,
        ];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}

enum TagCategory: string
{
    case Group = 'group';
    case Nature = 'nature';
    case Focus = 'focus';
}
