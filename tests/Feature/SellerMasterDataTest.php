<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

test('seller can set and edit a product stock quantity', function () {
    $category = Category::factory()->create();

    Livewire::test('pages::seller.products')
        ->set('name', 'Stocked Item')
        ->set('categoryId', $category->id)
        ->set('description', 'Has a stock count.')
        ->set('price', 200000)
        ->set('stock', 15)
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::where('name', 'Stocked Item')->first();

    expect($product->stock)->toBe(15);
    expect($product->in_stock)->toBeTrue();

    Livewire::test('pages::seller.products')
        ->call('edit', $product->id)
        ->set('stock', 0)
        ->call('save')
        ->assertHasNoErrors();

    $product->refresh();

    expect($product->stock)->toBe(0);
    expect($product->in_stock)->toBeFalse();
});

test('seller can upload, replace and remove a product photo', function () {
    Storage::fake('supabase');

    $category = Category::factory()->create();
    $firstPhoto = UploadedFile::fake()->image('photo.jpg');

    Livewire::test('pages::seller.products')
        ->set('name', 'Photographed Product')
        ->set('categoryId', $category->id)
        ->set('description', 'Has a real photo.')
        ->set('price', 300000)
        ->set('photo', $firstPhoto)
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::where('name', 'Photographed Product')->first();

    expect($product->image)->not->toBeNull();
    Storage::disk('supabase')->assertExists($product->image);
    $firstImagePath = $product->image;

    $secondPhoto = UploadedFile::fake()->image('photo-2.jpg');

    Livewire::test('pages::seller.products')
        ->call('edit', $product->id)
        ->set('photo', $secondPhoto)
        ->call('save')
        ->assertHasNoErrors();

    $product->refresh();

    expect($product->image)->not->toBe($firstImagePath);
    Storage::disk('supabase')->assertExists($product->image);
    Storage::disk('supabase')->assertMissing($firstImagePath);

    Livewire::test('pages::seller.products')
        ->call('edit', $product->id)
        ->set('removePhoto', true)
        ->call('save')
        ->assertHasNoErrors();

    $product->refresh();

    expect($product->image)->toBeNull();
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