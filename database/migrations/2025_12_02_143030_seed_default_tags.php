<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
            // Use DB directly to avoid model slug generation during migration
            $exists = DB::table('tags')
                ->where('name', $tagName)
                ->where('category', 'nature')
                ->exists();
            
            if (!$exists) {
                DB::table('tags')->insert([
                    'name' => $tagName,
                    'category' => 'nature',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
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
            // Use DB directly to avoid model slug generation during migration
            $exists = DB::table('tags')
                ->where('name', $tagName)
                ->where('category', 'focus')
                ->exists();
            
            if (!$exists) {
                DB::table('tags')->insert([
                    'name' => $tagName,
                    'category' => 'focus',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete Nature category tags
        DB::table('tags')
            ->where('category', 'nature')
            ->whereIn('name', ['Experimental', 'Numerical'])
            ->delete();

        // Delete Focus category tags
        DB::table('tags')
            ->where('category', 'focus')
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
