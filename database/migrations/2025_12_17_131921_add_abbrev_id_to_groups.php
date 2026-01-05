<?php

use App\Models\Group;
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
        Schema::table('groups', function (Blueprint $table) {
            $table->string('abbrev_id')->nullable()->after('name');
        });

        // Generate abbrev_id for existing groups
        $groups = Group::all();
        $usedAbbrevs = [];

        foreach ($groups as $group) {
            // Remove "Group" (case-insensitive) from the name
            $nameWithoutGroup = preg_replace('/^Group\s+/i', '', $group->name);
            
            // Remove spaces and capitalize
            $cleaned = strtoupper(str_replace(' ', '', $nameWithoutGroup));
            
            // Take first 4 characters
            $abbrev = substr($cleaned, 0, 4);
            
            // Ensure uniqueness
            $originalAbbrev = $abbrev;
            $counter = 1;
            while (in_array($abbrev, $usedAbbrevs)) {
                // If conflict, try to use more characters or add a number
                if (strlen($cleaned) > 4) {
                    $abbrev = substr($cleaned, 0, min(4 + $counter, strlen($cleaned)));
                } else {
                    $abbrev = $originalAbbrev . $counter;
                }
                $counter++;
            }
            
            $usedAbbrevs[] = $abbrev;
            $group->update(['abbrev_id' => $abbrev]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('abbrev_id');
        });
    }
};
