<?php

use App\Models\Group;
use App\Models\Project;
use App\Models\User;

beforeEach(function () {
    seedTestData();
});

test('user has group relationship', function () {
    $group = Group::factory()->create();
    $user = User::factory()->create(['group_id' => $group->id]);

    expect($user->group->id)->toBe($group->id);
});

test('user has owned projects relationship', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['project_owner_id' => $user->id]);

    expect($user->ownedProjects->first()->id)->toBe($project->id);
});

test('user has supervised projects relationship', function () {
    $user = createSupervisor();
    $project = createProject();
    $project->supervisorLinks()->delete();
    \App\Models\ProjectSupervisor::create([
        'project_id' => $project->id,
        'supervisor_type' => User::class,
        'supervisor_id' => $user->id,
        'order_rank' => 1,
    ]);

    expect($user->fresh()->supervisedProjects->first()->id)->toBe($project->id);
});

test('slug is auto-generated', function () {
    $user = User::factory()->create(['name' => 'John Doe']);

    expect($user->slug)->toBe('john-doe');
});

test('can access panel returns true for valid roles', function () {
    $admin = createUserWithRole('Administrator');
    $supervisor = createSupervisor();
    $researcher = createUserWithRole('Researcher');

    $panel = \Filament\Facades\Filament::getPanel('admin');
    
    expect($admin->canAccessPanel($panel))->toBeTrue();
    expect($supervisor->canAccessPanel($panel))->toBeTrue();
    expect($researcher->canAccessPanel($panel))->toBeTrue();
});

test('can access panel returns false for users without roles', function () {
    $user = User::factory()->create();

    $panel = \Filament\Facades\Filament::getPanel('admin');
    expect($user->canAccessPanel($panel))->toBeFalse();
});

test('get filament avatar url returns storage url', function () {
    $user = User::factory()->create(['avatar_url' => 'avatars/test.jpg']);

    $url = $user->getFilamentAvatarUrl();
    expect($url)->toContain('avatars/test.jpg');
});

test('get filament avatar url returns null when no avatar', function () {
    $user = User::factory()->create(['avatar_url' => null]);

    expect($user->getFilamentAvatarUrl())->toBeNull();
});

