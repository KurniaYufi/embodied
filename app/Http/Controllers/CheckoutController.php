<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('checkout');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'string'],
        ]);

        $items = json_decode($validated['items'], associative: true);

        if (! is_array($items) || $items === []) {
            throw ValidationException::withMessages([
                'items' => 'Your bag is empty.',
            ]);
        }

        $subtotal = 0;
        $lineItems = [];

        foreach ($items as $item) {
            if (! isset($item['name'], $item['priceValue'], $item['gradient'], $item['qty'])) {
                continue;
            }

            $price = (int) $item['priceValue'];
            $quantity = max(1, (int) $item['qty']);

            $subtotal += $price * $quantity;

            $lineItems[] = [
                'product_name' => (string) $item['name'],
                'size' => isset($item['size']) ? (string) $item['size'] : null,
                'price' => $price,
                'quantity' => $quantity,
                'gradient' => (string) $item['gradient'],
                'image' => ! empty($item['image']) ? (string) $item['image'] : null,
            ];
        }

        if ($lineItems === []) {
            throw ValidationException::withMessages([
                'items' => 'Your bag is empty.',
            ]);
        }

        $order = Order::create([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'notes' => $validated['notes'] ?? null,
            'subtotal' => $subtotal,
        ]);

        foreach ($lineItems as $lineItem) {
            $order->items()->create($lineItem);
        }

        return redirect()->route('orders.show', $order->access_token);
    }
}
