<?php

use App\Enums\PaymentMethodType;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

test('modal shows the right fields for each selected type', function () {
    $component = Livewire::test('pages::seller.payment-methods')
        ->call('create')
        ->assertSee('Account Name')
        ->assertSee('Account Number')
        ->assertDontSee('QR Image');

    $component->set('type', 'qris')
        ->assertSee('QR Image')
        ->assertDontSee('Account Name')
        ->assertDontSee('Account Number');

    $component->set('type', 'bank')
        ->assertSee('Account Name')
        ->assertSee('Account Number')
        ->assertDontSee('QR Image');
});

test('seller can create a bank transfer payment method', function () {
    Livewire::test('pages::seller.payment-methods')
        ->set('type', 'bank')
        ->set('name', 'BCA')
        ->set('accountName', 'PT Embodied Studio')
        ->set('accountNumber', '1234567890')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('payment_methods', [
        'name' => 'BCA',
        'type' => 'bank',
        'account_name' => 'PT Embodied Studio',
        'account_number' => '1234567890',
        'is_active' => true,
    ]);
});

test('seller can create a qris payment method with an image', function () {
    Storage::fake('supabase');

    Livewire::test('pages::seller.payment-methods')
        ->set('type', 'qris')
        ->set('name', 'QRIS')
        ->set('photo', UploadedFile::fake()->image('qris.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    $method = PaymentMethod::where('name', 'QRIS')->firstOrFail();

    expect($method->type)->toBe(PaymentMethodType::Qris);
    expect($method->image_path)->not->toBeNull();
    Storage::disk('supabase')->assertExists($method->image_path);
});

test('qris payment method requires an image', function () {
    Livewire::test('pages::seller.payment-methods')
        ->set('type', 'qris')
        ->set('name', 'QRIS')
        ->call('save')
        ->assertHasErrors('photo');

    $this->assertDatabaseMissing('payment_methods', ['name' => 'QRIS']);
});

test('bank payment method requires account details', function () {
    Livewire::test('pages::seller.payment-methods')
        ->set('type', 'bank')
        ->set('name', 'BCA')
        ->call('save')
        ->assertHasErrors(['accountName', 'accountNumber']);
});

test('seller can toggle a payment method active state', function () {
    $method = PaymentMethod::factory()->create(['is_active' => true]);

    Livewire::test('pages::seller.payment-methods')
        ->call('toggleActive', $method->id);

    expect($method->fresh()->is_active)->toBeFalse();
});

test('seller can delete a payment method', function () {
    $method = PaymentMethod::factory()->create();

    Livewire::test('pages::seller.payment-methods')
        ->call('delete', $method->id);

    $this->assertDatabaseMissing('payment_methods', ['id' => $method->id]);
});

test('guests cannot access seller payment methods', function () {
    auth()->logout();

    $this->get(route('seller.payment-methods'))->assertRedirect(route('login'));
});
