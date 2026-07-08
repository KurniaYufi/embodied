<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('dashboard shows live master data counts', function () {
    Product::factory()->count(3)->create();

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('3');
});

test('seller can list, create, edit and delete a category', function () {
    Category::factory()->create(['name' => 'Existing Category']);

    Livewire::test('pages::seller.categories')
        ->assertSee('Existing Category')
        ->set('name', 'Outerwear')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('categories', ['name' => 'Outerwear', 'slug' => 'outerwear']);

    $category = Category::where('name', 'Outerwear')->first();

    Livewire::test('pages::seller.categories')
        ->call('edit', $category->id)
        ->set('name', 'Outerwear Updated')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Outerwear Updated']);

    Livewire::test('pages::seller.categories')
        ->call('delete', $category->id);

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('seller can create a product with sizes and a category', function () {
    $category = Category::factory()->create();
    $sizeS = Size::factory()->create(['label' => 'S']);
    $sizeM = Size::factory()->create(['label' => 'M']);

    Livewire::test('pages::seller.products')
        ->set('name', 'Test Jacket')
        ->set('categoryId', $category->id)
        ->set('description', 'A warm jacket.')
        ->set('price', 500000)
        ->set('sizeIds', [$sizeS->id, $sizeM->id])
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::where('name', 'Test Jacket')->first();

    expect($product)->not->toBeNull();
    expect($product->category_id)->toBe($category->id);
    expect($product->sizes->pluck('label')->sort()->values()->all())->toBe(['M', 'S']);
});

test('seller can delete a product', function () {
    $product = Product::factory()->create();

    Livewire::test('pages::seller.products')
        ->call('delete', $product->id);

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('guests cannot access seller master data', function () {
    auth()->logout();

    $this->get(route('seller.products'))->assertRedirect(route('login'));
});