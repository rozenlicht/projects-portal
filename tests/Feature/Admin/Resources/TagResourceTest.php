<?php

use App\Filament\Resources\Tags\Pages\CreateTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Models\Tag;
use App\Models\TagCategory;

beforeEach(function () {
    seedTestData();
    $this->user = authenticateAs(createSupervisor());
});

test('can list tags', function () {
    $tag = Tag::factory()->create();

    livewire(ListTags::class)
        ->assertCanSeeTableRecords([$tag]);
});

test('can create tag', function () {
    livewire(CreateTag::class)
        ->fillForm([
            'name' => 'New Tag',
            'category' => TagCategory::Focus->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Tag::where('name', 'New Tag')->exists())->toBeTrue();
});

test('tag slug is auto-generated', function () {
    livewire(CreateTag::class)
        ->fillForm([
            'name' => 'Test Tag Name',
            'category' => TagCategory::Nature->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $tag = Tag::where('name', 'Test Tag Name')->first();
    expect($tag->slug)->toBe('test-tag-name');
});

test('can edit tag', function () {
    $tag = Tag::factory()->create();

    livewire(\App\Filament\Resources\Tags\Pages\EditTag::class, ['record' => $tag->getRouteKey()])
        ->fillForm([
            'name' => 'Updated Tag',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($tag->fresh()->name)->toBe('Updated Tag');
});



