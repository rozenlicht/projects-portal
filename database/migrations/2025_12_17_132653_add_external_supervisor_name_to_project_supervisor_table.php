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
        Schema::table('project_supervisor', function (Blueprint $table) {
            // Add external supervisor name column
            $table->string('external_supervisor_name')->nullable()->after('supervisor_id');
            
            // Make supervisor_type and supervisor_id nullable for external supervisors
            $table->string('supervisor_type')->nullable()->change();
            $table->unsignedBigInteger('supervisor_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_supervisor', function (Blueprint $table) {
            // Remove external supervisor name column
            $table->dropColumn('external_supervisor_name');
            
            // Make supervisor_type and supervisor_id required again
            $table->string('supervisor_type')->nullable(false)->change();
            $table->unsignedBigInteger('supervisor_id')->nullable(false)->change();
        });
    }
};
