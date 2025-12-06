<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Tag extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'category' => TagCategory::class,
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
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
