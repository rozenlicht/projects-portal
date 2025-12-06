<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already exists (in case migration partially ran)
        if (!Schema::hasColumn('users', 'slug')) {
            // First, add the column as nullable (no unique constraint yet)
            Schema::table('users', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        // Generate slugs for existing users
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                if (empty($user->slug)) {
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
            }
        });

        // Try to drop existing unique index if it exists (in case migration partially ran)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['users_slug_unique']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, that's fine
        }

        // Now make it non-nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
        
        // Add unique index separately
        Schema::table('users', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
