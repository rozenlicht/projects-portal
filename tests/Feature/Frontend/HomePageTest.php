<?php

test('home route returns 200', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('home page renders correctly', function () {
    $response = $this->get('/');

    $response->assertViewIs('welcome');
});



