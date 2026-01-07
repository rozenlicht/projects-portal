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
        // Set created_by_id to project_owner_id for existing records where created_by_id is null
        DB::table('projects')
            ->whereNull('created_by_id')
            ->whereNotNull('project_owner_id')
            ->update(['created_by_id' => DB::raw('project_owner_id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally clear created_by_id if needed, but usually we don't want to lose data
        // DB::table('projects')->update(['created_by_id' => null]);
    }
};
