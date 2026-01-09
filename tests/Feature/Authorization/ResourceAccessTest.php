<?php

beforeEach(function () {
    seedTestData();
});

test('user resource is admin-only', function () {
    $admin = authenticateAs(createUserWithRole('Administrator'));
    $nonAdmin = authenticateAs(createSupervisor());

    $this->actingAs($admin)
        ->get('/admin/users')
        ->assertSuccessful();

    $this->actingAs($nonAdmin)
        ->get('/admin/users')
        ->assertForbidden();
});

test('section resource is admin-only', function () {
    $admin = authenticateAs(createUserWithRole('Administrator'));
    $nonAdmin = authenticateAs(createSupervisor());

    $this->actingAs($admin)
        ->get('/admin/sections')
        ->assertSuccessful();

    $this->actingAs($nonAdmin)
        ->get('/admin/sections')
        ->assertForbidden();
});

test('other resources are accessible to appropriate roles', function () {
    $supervisor = authenticateAs(createSupervisor());

    $this->actingAs($supervisor)
        ->get('/admin/projects')
        ->assertSuccessful();

    $this->actingAs($supervisor)
        ->get('/admin/tags')
        ->assertSuccessful();

    $this->actingAs($supervisor)
        ->get('/admin/organizations')
        ->assertSuccessful();
});

test('non-authenticated users cannot access admin panel', function () {
    $this->get('/admin')
        ->assertRedirect('/admin/login');

    $this->get('/admin/projects')
        ->assertRedirect('/admin/login');
});



