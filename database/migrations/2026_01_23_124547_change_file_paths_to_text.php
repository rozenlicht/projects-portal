<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change featured_image in projects table from VARCHAR to TEXT
        Schema::table('projects', function (Blueprint $table) {
            $table->text('featured_image')->nullable()->change();
        });

        // Change avatar_url in users table from VARCHAR to TEXT
        Schema::table('users', function (Blueprint $table) {
            $table->text('avatar_url')->nullable()->change();
        });

        // Change logo in organizations table from VARCHAR to TEXT
        Schema::table('organizations', function (Blueprint $table) {
            $table->text('logo')->nullable()->change();
        });

        // Change avatar_url in external_supervisors table from VARCHAR to TEXT
        Schema::table('external_supervisors', function (Blueprint $table) {
            $table->text('avatar_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert featured_image in projects table from TEXT to VARCHAR
        Schema::table('projects', function (Blueprint $table) {
            $table->string('featured_image')->nullable()->change();
        });

        // Revert avatar_url in users table from TEXT to VARCHAR
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->change();
        });

        // Revert logo in organizations table from TEXT to VARCHAR
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('logo')->nullable()->change();
        });

        // Revert avatar_url in external_supervisors table from TEXT to VARCHAR
        Schema::table('external_supervisors', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->change();
        });
    }
};
