<?php

use App\Models\Group;
use App\Models\User;
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
            $table->foreignId('group_leader_id')->nullable()->after('section_id')->constrained('users')->nullOnDelete();
        });

        // Try to guess the group leader by matching last name from group name
        Group::chunk(100, function ($groups) {
            foreach ($groups as $group) {
                // Remove "Group " prefix (case-insensitive)
                $searchName = preg_replace('/^Group\s+/i', '', $group->name);
                
                // Search for users whose name contains this last name
                $leader = User::where('name', 'LIKE', "%{$searchName}%")->first();
                
                if ($leader) {
                    $group->update(['group_leader_id' => $leader->id]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['group_leader_id']);
            $table->dropColumn('group_leader_id');
        });
    }
};
