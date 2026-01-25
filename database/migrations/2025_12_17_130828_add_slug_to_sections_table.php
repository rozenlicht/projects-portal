<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Section;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already exists (in case migration partially ran)
        if (!Schema::hasColumn('sections', 'slug')) {
            // First, add the column as nullable (no unique constraint yet)
            Schema::table('sections', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        // Generate slugs for existing sections
        Section::chunk(100, function ($sections) {
            foreach ($sections as $section) {
                if (empty($section->slug)) {
                    $baseSlug = Str::slug($section->name);
                    $slug = $baseSlug;
                    $counter = 1;
                    
                    // Ensure unique slug
                    while (Section::where('slug', $slug)->where('id', '!=', $section->id)->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }
                    
                    $section->slug = $slug;
                    $section->save();
                }
            }
        });

        // Try to drop existing unique index if it exists (in case migration partially ran)
        try {
            Schema::table('sections', function (Blueprint $table) {
                $table->dropUnique(['sections_slug_unique']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, that's fine
        }

        // Now make it non-nullable (only if it was added as nullable)
        // If the column was created with unique() in create_sections_table, it's already not nullable
        if (Schema::hasColumn('sections', 'slug')) {
            try {
                // Check if column is nullable by trying to change it
                Schema::table('sections', function (Blueprint $table) {
                    $table->string('slug')->nullable(false)->change();
                });
            } catch (\Exception $e) {
                // Column might already be not nullable, that's fine
            }
        }
        
        // Add unique index separately (only if it doesn't already exist)
        try {
            Schema::table('sections', function (Blueprint $table) {
                $table->unique('slug');
            });
        } catch (\Exception $e) {
            // Unique index already exists (e.g., from create_sections_table), that's fine
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
