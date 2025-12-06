<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Tag;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already exists (in case migration partially ran)
        if (!Schema::hasColumn('tags', 'slug')) {
            // First, add the column as nullable (no unique constraint yet)
            Schema::table('tags', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        // Generate slugs for existing tags
        Tag::chunk(100, function ($tags) {
            foreach ($tags as $tag) {
                if (empty($tag->slug)) {
                    $baseSlug = Str::slug($tag->name);
                    $slug = $baseSlug;
                    $counter = 1;
                    
                    // Ensure unique slug
                    while (Tag::where('slug', $slug)->where('id', '!=', $tag->id)->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }
                    
                    $tag->slug = $slug;
                    $tag->save();
                }
            }
        });

        // Try to drop existing unique index if it exists (in case migration partially ran)
        try {
            Schema::table('tags', function (Blueprint $table) {
                $table->dropUnique(['tags_slug_unique']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, that's fine
        }

        // Now make it non-nullable
        Schema::table('tags', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
        
        // Add unique index separately
        Schema::table('tags', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
