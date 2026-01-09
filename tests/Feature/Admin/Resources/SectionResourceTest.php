<?php

use App\Filament\Resources\Sections\Pages\CreateSection;
use App\Filament\Resources\Sections\Pages\ListSections;
use App\Models\Section;

beforeEach(function () {
    seedTestData();
    $this->admin = authenticateAs(createUserWithRole('Administrator'));
});

test('section resource is admin-only', function () {
    $nonAdmin = authenticateAs(createSupervisor());

    $this->get('/admin/sections')
        ->assertForbidden();
});

test('admin can access section resource', function () {
    $this->get('/admin/sections')
        ->assertSuccessful();
});

test('can list sections', function () {
    $section = Section::factory()->create();

    livewire(ListSections::class)
        ->assertCanSeeTableRecords([$section]);
});

test('can create section', function () {
    livewire(CreateSection::class)
        ->fillForm([
            'name' => 'New Section',
            'abbrev_id' => 'NEW',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Section::where('name', 'New Section')->exists())->toBeTrue();
});

test('section slug is auto-generated', function () {
    livewire(CreateSection::class)
        ->fillForm([
            'name' => 'Test Section Name',
            'abbrev_id' => 'TSN',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $section = Section::where('name', 'Test Section Name')->first();
    expect($section->slug)->not->toBeNull();
});

test('can edit section', function () {
    $section = Section::factory()->create();

    livewire(\App\Filament\Resources\Sections\Pages\EditSection::class, ['record' => $section->getRouteKey()])
        ->fillForm([
            'name' => 'Updated Section',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($section->fresh()->name)->toBe('Updated Section');
});



