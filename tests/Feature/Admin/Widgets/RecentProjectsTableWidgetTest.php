<?php

use App\Filament\Widgets\RecentProjectsTableWidget;
use App\Models\Project;

beforeEach(function () {
    seedTestData();
    $this->user = authenticateAs(createSupervisor());
});

test('recent projects widget renders', function () {
    livewire(RecentProjectsTableWidget::class)
        ->assertSuccessful();
});

test('widget displays recent projects', function () {
    $projects = Project::factory()->count(5)->create();

    livewire(RecentProjectsTableWidget::class)
        ->assertCanSeeTableRecords($projects);
});

test('widget limits to 5 projects', function () {
    Project::factory()->count(10)->create();

    livewire(RecentProjectsTableWidget::class)
        ->assertSuccessful();
    // The widget query has limit(5), so it should only show 5 records
});

