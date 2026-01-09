<?php

use Filament\Pages\Dashboard;

beforeEach(function () {
    seedTestData();
    $this->user = authenticateAs(createSupervisor());
});

test('dashboard is accessible to authenticated users with roles', function () {
    $this->get('/admin')
        ->assertSuccessful();
});

test('non-authenticated users cannot access dashboard', function () {
    auth()->logout();

    $this->get('/admin')
        ->assertRedirect('/admin/login');
});

test('users without roles cannot access dashboard', function () {
    $userWithoutRole = \App\Models\User::factory()->create();
    $this->actingAs($userWithoutRole);

    $this->get('/admin')
        ->assertForbidden();
});



