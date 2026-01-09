<?php

use App\Models\Group;
use App\Models\Project;
use App\Models\ProjectSupervisor;
use App\Models\Section;
use App\Models\User;

beforeEach(function () {
    seedTestData();
});

test('project number format is correct', function () {
    $section = createSection(['abbrev_id' => 'SEC']);
    $group = createGroup(['section_id' => $section->id, 'abbrev_id' => 'GRP']);
    $supervisor = createSupervisor(['group_id' => $group->id]);

    $project = createProject();
    $project->supervisorLinks()->delete();
    ProjectSupervisor::create([
        'project_id' => $project->id,
        'supervisor_type' => User::class,
        'supervisor_id' => $supervisor->id,
        'order_rank' => 1,
    ]);

    $project->generateProjectNumber();

    expect($project->fresh()->project_number)
        ->toMatch('/^\d{2}SECGRP\d{2}$/'); // YY + Section + Group + 2-digit number
});

test('project number increments correctly', function () {
    $section = createSection(['abbrev_id' => 'SEC']);
    $group = createGroup(['section_id' => $section->id, 'abbrev_id' => 'GRP']);
    $supervisor = createSupervisor(['group_id' => $group->id]);

    // Create first project
    $project1 = createProject();
    $project1->supervisorLinks()->delete();
    ProjectSupervisor::create([
        'project_id' => $project1->id,
        'supervisor_type' => User::class,
        'supervisor_id' => $supervisor->id,
        'order_rank' => 1,
    ]);
    $project1->generateProjectNumber();
    $number1 = $project1->fresh()->project_number;

    // Create second project
    $project2 = createProject();
    $project2->supervisorLinks()->delete();
    ProjectSupervisor::create([
        'project_id' => $project2->id,
        'supervisor_type' => User::class,
        'supervisor_id' => $supervisor->id,
        'order_rank' => 1,
    ]);
    $project2->generateProjectNumber();
    $number2 = $project2->fresh()->project_number;

    // Extract the number part
    $num1 = (int) substr($number1, -2);
    $num2 = (int) substr($number2, -2);

    expect($num2)->toBe($num1 + 1);
});

test('project number uses year from created_at', function () {
    $section = createSection(['abbrev_id' => 'SEC']);
    $group = createGroup(['section_id' => $section->id, 'abbrev_id' => 'GRP']);
    $supervisor = createSupervisor(['group_id' => $group->id]);

    $project = createProject();
    $project->update(['created_at' => now()->year(2024)]);
    $project->supervisorLinks()->delete();
    ProjectSupervisor::create([
        'project_id' => $project->id,
        'supervisor_type' => User::class,
        'supervisor_id' => $supervisor->id,
        'order_rank' => 1,
    ]);

    $project->generateProjectNumber();

    expect($project->fresh()->project_number)->toStartWith('24');
});

test('project number returns null when supervisor missing', function () {
    $project = createProject();
    $project->supervisorLinks()->delete();

    $result = $project->generateProjectNumber();

    expect($result)->toBeNull();
});

test('project number returns null when group missing', function () {
    $supervisor = createSupervisor(['group_id' => null]);
    $project = createProject();
    $project->supervisorLinks()->delete();
    ProjectSupervisor::create([
        'project_id' => $project->id,
        'supervisor_type' => User::class,
        'supervisor_id' => $supervisor->id,
        'order_rank' => 1,
    ]);

    $result = $project->generateProjectNumber();

    expect($result)->toBeNull();
});

test('project number returns null when section abbrev_id missing', function () {
    $section = createSection(['abbrev_id' => null]);
    $group = createGroup(['section_id' => $section->id, 'abbrev_id' => 'GRP']);
    $supervisor = createSupervisor(['group_id' => $group->id]);

    $project = createProject();
    $project->supervisorLinks()->delete();
    ProjectSupervisor::create([
        'project_id' => $project->id,
        'supervisor_type' => User::class,
        'supervisor_id' => $supervisor->id,
        'order_rank' => 1,
    ]);

    $result = $project->generateProjectNumber();

    expect($result)->toBeNull();
});

test('project number returns null when group abbrev_id missing', function () {
    $section = createSection(['abbrev_id' => 'SEC']);
    $group = createGroup(['section_id' => $section->id, 'abbrev_id' => null]);
    $supervisor = createSupervisor(['group_id' => $group->id]);

    $project = createProject();
    $project->supervisorLinks()->delete();
    ProjectSupervisor::create([
        'project_id' => $project->id,
        'supervisor_type' => User::class,
        'supervisor_id' => $supervisor->id,
        'order_rank' => 1,
    ]);

    $result = $project->generateProjectNumber();

    expect($result)->toBeNull();
});



