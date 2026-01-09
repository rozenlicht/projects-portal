<?php

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedTestData();
    Storage::fake('public');
});

test('onboarding shows form for valid token', function () {
    $user = User::factory()->unverified()->create([
        'invitation_token' => 'valid-token',
        'invitation_sent_at' => now(),
    ]);

    $response = $this->get('/onboarding/valid-token');

    $response->assertStatus(200);
    $response->assertSee($user->email);
});

test('onboarding returns 404 for invalid token', function () {
    $response = $this->get('/onboarding/invalid-token');

    $response->assertStatus(404);
});

test('onboarding returns 410 for expired token', function () {
    $user = User::factory()->unverified()->create([
        'invitation_token' => 'expired-token',
        'invitation_sent_at' => now()->subDays(8), // 8 days ago (expired)
    ]);

    $response = $this->get('/onboarding/expired-token');

    $response->assertStatus(410);
});

test('onboarding form validates password', function () {
    $user = User::factory()->unverified()->create([
        'invitation_token' => 'test-token',
        'invitation_sent_at' => now(),
    ]);

    $response = $this->post('/onboarding/test-token', [
        'password' => 'short',
        'password_confirmation' => 'short',
        'group_id' => createGroup()->id,
    ]);

    $response->assertSessionHasErrors('password');
});

test('onboarding form validates password confirmation', function () {
    $user = User::factory()->unverified()->create([
        'invitation_token' => 'test-token',
        'invitation_sent_at' => now(),
    ]);

    $response = $this->post('/onboarding/test-token', [
        'password' => 'Password123!',
        'password_confirmation' => 'DifferentPassword123!',
        'group_id' => createGroup()->id,
    ]);

    $response->assertSessionHasErrors('password');
});

test('onboarding form validates group_id', function () {
    $user = User::factory()->unverified()->create([
        'invitation_token' => 'test-token',
        'invitation_sent_at' => now(),
    ]);

    $response = $this->post('/onboarding/test-token', [
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'group_id' => 99999, // Non-existent group
    ]);

    $response->assertSessionHasErrors('group_id');
});

test('onboarding successfully sets up user', function () {
    $group = createGroup();
    $user = User::factory()->unverified()->create([
        'invitation_token' => 'test-token',
        'invitation_sent_at' => now(),
    ]);

    $response = $this->post('/onboarding/test-token', [
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
        'group_id' => $group->id,
    ]);

    $response->assertRedirect('/');
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->invitation_token)->toBeNull();
    expect($user->group_id)->toBe($group->id);
    expect(Hash::check('NewPassword123!', $user->password))->toBeTrue();
});

test('onboarding handles avatar upload', function () {
    $group = createGroup();
    $user = User::factory()->unverified()->create([
        'invitation_token' => 'test-token',
        'invitation_sent_at' => now(),
    ]);

    $avatar = UploadedFile::fake()->image('avatar.jpg', 100, 100);

    $response = $this->post('/onboarding/test-token', [
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
        'group_id' => $group->id,
        'avatar' => $avatar,
    ]);

    $response->assertRedirect('/');

    $user->refresh();
    expect($user->avatar_url)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar_url);
});

test('onboarding validates avatar file type', function () {
    $group = createGroup();
    $user = User::factory()->unverified()->create([
        'invitation_token' => 'test-token',
        'invitation_sent_at' => now(),
    ]);

    $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->post('/onboarding/test-token', [
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
        'group_id' => $group->id,
        'avatar' => $invalidFile,
    ]);

    $response->assertSessionHasErrors('avatar');
});

test('onboarding validates avatar file size', function () {
    $group = createGroup();
    $user = User::factory()->unverified()->create([
        'invitation_token' => 'test-token',
        'invitation_sent_at' => now(),
    ]);

    $largeFile = UploadedFile::fake()->image('avatar.jpg')->size(3000); // 3MB, exceeds 2MB limit

    $response = $this->post('/onboarding/test-token', [
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
        'group_id' => $group->id,
        'avatar' => $largeFile,
    ]);

    $response->assertSessionHasErrors('avatar');
});

test('onboarding handles expired token on POST', function () {
    $user = User::factory()->unverified()->create([
        'invitation_token' => 'expired-token',
        'invitation_sent_at' => now()->subDays(8),
    ]);

    $response = $this->post('/onboarding/expired-token', [
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
        'group_id' => createGroup()->id,
    ]);

    $response->assertSessionHasErrors('token');
});



