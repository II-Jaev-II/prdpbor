<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    Storage::fake('public');

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'designation' => 'Test Designation',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'e_signature' => UploadedFile::fake()->image('signature.jpg'),
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    Storage::disk('public')->assertExists('signatures/' . basename(auth()->user()->e_signature));
});
