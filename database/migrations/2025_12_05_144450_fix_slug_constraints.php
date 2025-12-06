<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Tag;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix tags table
        if (Schema::hasColumn('tags', 'slug')) {
            // Try to drop the unique constraint if it exists
            try {
                Schema::table('tags', function (Blueprint $table) {
                    $table->dropUnique(['tags_slug_unique']);
                });
            } catch (\Exception $e) {
                // Try alternative index name
                try {
                    Schema::table('tags', function (Blueprint $table) {
                        $table->dropUnique(['slug']);
                    });
                } catch (\Exception $e2) {
                    // Index doesn't exist or has different name, continue
                }
            }

            // Generate slugs for tags that don't have them
            Tag::whereNull('slug')->orWhere('slug', '')->chunk(100, function ($tags) {
                foreach ($tags as $tag) {
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
            });

            // Make sure column is not nullable and add unique constraint
            Schema::table('tags', function (Blueprint $table) {
                $table->string('slug')->nullable(false)->change();
            });
            
            Schema::table('tags', function (Blueprint $table) {
                $table->unique('slug');
            });
        }

        // Fix users table
        if (Schema::hasColumn('users', 'slug')) {
            // Try to drop the unique constraint if it exists
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropUnique(['users_slug_unique']);
                });
            } catch (\Exception $e) {
                // Try alternative index name
                try {
                    Schema::table('users', function (Blueprint $table) {
                        $table->dropUnique(['slug']);
                    });
                } catch (\Exception $e2) {
                    // Index doesn't exist or has different name, continue
                }
            }

            // Generate slugs for users that don't have them
            User::whereNull('slug')->orWhere('slug', '')->chunk(100, function ($users) {
                foreach ($users as $user) {
                    $baseSlug = Str::slug($user->name);
                    $slug = $baseSlug;
                    $counter = 1;
                    
                    // Ensure unique slug
                    while (User::where('slug', $slug)->where('id', '!=', $user->id)->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }
                    
                    $user->slug = $slug;
                    $user->save();
                }
            });

            // Make sure column is not nullable and add unique constraint
            Schema::table('users', function (Blueprint $table) {
                $table->string('slug')->nullable(false)->change();
            });
            
            Schema::table('users', function (Blueprint $table) {
                $table->unique('slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a fix migration, no need to reverse
    }
};
