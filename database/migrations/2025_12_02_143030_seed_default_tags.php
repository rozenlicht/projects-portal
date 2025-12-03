<?php

use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Nature category tags
        $natureTags = [
            'Experimental',
            'Numerical',
        ];

        foreach ($natureTags as $tagName) {
            Tag::firstOrCreate([
                'name' => $tagName,
                'category' => TagCategory::Nature,
            ]);
        }

        // Focus category tags
        $focusTags = [
            'Metals',
            'Steel',
            '3D printing',
            'Meta materials',
            'Nature-inspired',
            'Damage models',
            'Simulation development',
        ];

        foreach ($focusTags as $tagName) {
            Tag::firstOrCreate([
                'name' => $tagName,
                'category' => TagCategory::Focus,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete Nature category tags
        Tag::where('category', TagCategory::Nature)
            ->whereIn('name', ['Experimental', 'Numerical'])
            ->delete();

        // Delete Focus category tags
        Tag::where('category', TagCategory::Focus)
            ->whereIn('name', [
                'Metals',
                'Steel',
                '3D printing',
                'Meta materials',
                'Nature-inspired',
                'Damage models',
                'Simulation development',
            ])
            ->delete();
    }
};
