<?php

use App\Models\Project;

beforeEach(function () {
    seedTestData();
});

test('past projects route returns 200', function () {
    $response = $this->get('/projects/past');

    $response->assertStatus(200);
});

test('only past projects are shown', function () {
    $pastProject = createProject();
    $pastProject->update(['student_name' => 'John Doe']);

    $availableProject = createProject();
    $availableProject->update(['student_name' => null, 'student_email' => null]);

    $response = $this->get('/projects/past');

    $response->assertSee($pastProject->name);
    $response->assertDontSee($availableProject->name);
});

test('past projects are ordered by latest first', function () {
    $oldProject = createProject();
    $oldProject->update([
        'student_name' => 'Old Student',
        'created_at' => now()->subDays(10),
    ]);

    $newProject = createProject();
    $newProject->update([
        'student_name' => 'New Student',
        'created_at' => now()->subDays(1),
    ]);

    $response = $this->get('/projects/past');

    $response->assertStatus(200);
    $projects = $response->viewData('projects');
    expect($projects->first()->id)->toBe($newProject->id);
});

test('pagination works for past projects', function () {
    // Create more than 12 past projects
    for ($i = 0; $i < 15; $i++) {
        $project = createProject();
        $project->update(['student_name' => 'Student ' . $i]);
    }

    $response = $this->get('/projects/past');

    $response->assertStatus(200);
    $response->assertViewHas('projects');
});



