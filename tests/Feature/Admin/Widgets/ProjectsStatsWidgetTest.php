<?php

use App\Filament\Widgets\ProjectsStatsWidget;
use App\Models\Project;
use App\Models\ProjectType;

beforeEach(function () {
    seedTestData();
    $this->user = authenticateAs(createSupervisor());
});

test('projects stats widget renders', function () {
    livewire(ProjectsStatsWidget::class)
        ->assertSuccessful();
});

test('widget displays total projects count', function () {
    Project::factory()->count(5)->create();

    livewire(ProjectsStatsWidget::class)
        ->assertSuccessful()
        ->assertSee('Total Projects');
});

test('widget displays available projects count', function () {
    Project::factory()->available()->count(3)->create();
    Project::factory()->taken()->count(2)->create();

    livewire(ProjectsStatsWidget::class)
        ->assertSuccessful()
        ->assertSee('Available Projects');
});

test('widget displays past projects count', function () {
    Project::factory()->taken()->count(4)->create();

    livewire(ProjectsStatsWidget::class)
        ->assertSuccessful()
        ->assertSee('Completed Projects');
});

test('widget displays projects by type', function () {
    $bachelorType = ProjectType::where('slug', 'bachelor_thesis')->first();
    $masterType = ProjectType::where('slug', 'master_thesis')->first();

    $bachelorProject = Project::factory()->available()->create();
    $bachelorProject->types()->attach($bachelorType->id);

    $masterProject = Project::factory()->available()->create();
    $masterProject->types()->attach($masterType->id);

    livewire(ProjectsStatsWidget::class)
        ->assertSuccessful()
        ->assertSee('Currently available by Type');
});

