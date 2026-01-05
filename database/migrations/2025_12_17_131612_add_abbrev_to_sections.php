<?php

use App\Models\Section;
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
        Schema::table('sections', function (Blueprint $table) {
            $table->string('abbrev_id')->nullable()->after('name');
        });

        // Mechanics of Materials = MOM, Processing and Performance = PP, Microsystems = MS
        Section::where('name', 'Mechanics of Materials')->update(['abbrev_id' => 'MOM']);
        Section::where('name', 'Processing and Performance')->update(['abbrev_id' => 'PP']);
        Section::where('name', 'Microsystems')->update(['abbrev_id' => 'MS']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('abbrev_id');
        });
    }
};
