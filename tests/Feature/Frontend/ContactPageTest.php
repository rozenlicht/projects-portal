<?php

test('contact route returns 200', function () {
    $response = $this->get('/contact');

    $response->assertStatus(200);
});

test('contact page renders correctly', function () {
    $response = $this->get('/contact');

    $response->assertViewIs('contact');
});



