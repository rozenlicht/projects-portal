<?php

test('tests use SQLite in-memory database', function () {
    expect(config('database.default'))->toBe('sqlite');
    expect(config('database.connections.sqlite.database'))->toBe(':memory:');
});

test('database connection is sqlite', function () {
    $connection = \Illuminate\Support\Facades\DB::connection()->getName();
    expect($connection)->toBe('sqlite');
});



