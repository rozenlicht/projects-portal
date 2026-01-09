<?php

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;

beforeEach(function () {
    seedTestData();
    $this->admin = authenticateAs(createUserWithRole('Administrator'));
});

test('user resource is admin-only', function () {
    $nonAdmin = authenticateAs(createSupervisor());

    $this->get('/admin/users')
        ->assertForbidden();
});

test('admin can access user resource', function () {
    $this->get('/admin/users')
        ->assertSuccessful();
});

test('can list users', function () {
    $user = User::factory()->create();

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords([$user]);
});

test('can create user', function () {
    $group = createGroup();

    livewire(\App\Filament\Resources\Users\Pages\CreateUser::class)
        ->fillForm([
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'Password123!',
            'group_id' => $group->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(User::where('email', 'newuser@example.com')->exists())->toBeTrue();
});



