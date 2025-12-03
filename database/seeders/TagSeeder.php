<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
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
}

