<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Copy logo to storage as if uploaded through GUI
        $sourcePath = public_path('assets/logos/tue_logo.png');
        $storageDirectory = 'organizations';
        $storagePath = $storageDirectory . '/tue_logo.png';
        
        // Ensure the organizations directory exists
        $fullStoragePath = storage_path('app/public/' . $storageDirectory);
        if (!File::exists($fullStoragePath)) {
            File::makeDirectory($fullStoragePath, 0755, true);
        }
        
        // Copy the file to storage
        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $fullStoragePath . '/tue_logo.png');
        }

        // Insert TU/e organization with storage path
        $tueId = DB::table('organizations')->insertGetId([
            'name' => 'TU/e',
            'logo' => $storagePath,
            'url' => 'https://www.tue.nl',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update existing projects to have TU/e as organization
        DB::table('projects')
            ->whereNull('organization_id')
            ->update(['organization_id' => $tueId]);

        // Drop the foreign key constraint
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
        });

        // Make organization_id not nullable
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable(false)->change();
        });

        // Re-add the foreign key constraint
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
        });

        // Make organization_id nullable again
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->change();
        });

        // Re-add the foreign key constraint with set null
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('set null');
        });

        // Delete TU/e organization and its logo from storage
        $tueOrg = DB::table('organizations')
            ->where('name', 'TU/e')
            ->first();
        
        if ($tueOrg && $tueOrg->logo) {
            // Delete the logo file from storage
            $logoPath = storage_path('app/public/' . $tueOrg->logo);
            if (File::exists($logoPath)) {
                File::delete($logoPath);
            }
        }
        
        DB::table('organizations')
            ->where('name', 'TU/e')
            ->delete();
    }
};
