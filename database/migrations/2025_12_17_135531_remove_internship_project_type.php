<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find the internship project type
        $internshipType = DB::table('project_types')
            ->where('slug', 'internship')
            ->first();

        if ($internshipType) {
            // Remove all project-project_type relationships for internships
            DB::table('project_project_type')
                ->where('project_type_id', $internshipType->id)
                ->delete();

            // Delete the internship project type
            DB::table('project_types')
                ->where('id', $internshipType->id)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create the internship project type if it doesn't exist
        $internshipType = DB::table('project_types')
            ->where('slug', 'internship')
            ->first();

        if (!$internshipType) {
            DB::table('project_types')->insert([
                'name' => 'Internship',
                'slug' => 'internship',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
