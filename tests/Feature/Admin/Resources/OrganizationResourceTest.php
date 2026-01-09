<?php

use App\Filament\Resources\Organizations\Pages\CreateOrganization;
use App\Filament\Resources\Organizations\Pages\ListOrganizations;
use App\Models\Organization;

beforeEach(function () {
    seedTestData();
    $this->user = authenticateAs(createSupervisor());
});

test('can list organizations', function () {
    $organization = Organization::factory()->create();

    livewire(ListOrganizations::class)
        ->assertCanSeeTableRecords([$organization]);
});

test('can create organization', function () {
    livewire(CreateOrganization::class)
        ->fillForm([
            'name' => 'New Organization',
            'url' => 'https://example.com',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Organization::where('name', 'New Organization')->exists())->toBeTrue();
});

test('can edit organization', function () {
    $organization = Organization::factory()->create();

    livewire(\App\Filament\Resources\Organizations\Pages\EditOrganization::class, ['record' => $organization->getRouteKey()])
        ->fillForm([
            'name' => 'Updated Organization',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($organization->fresh()->name)->toBe('Updated Organization');
});



