<?php

use App\Models\Product;

test('returns a successful response', function () {
    Product::factory()->count(5)->create();

    $response = $this->get(route('home'));

    $response->assertOk();
});