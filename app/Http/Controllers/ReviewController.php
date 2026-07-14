<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, string $token): RedirectResponse
    {
        $order = Order::with('items')->where('access_token', $token)->firstOrFail();

        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->status === OrderStatus::Completed, 403);

        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $purchasedProductIds = $order->items->pluck('product_id')->filter()->all();

        abort_unless(in_array((int) $validated['product_id'], $purchasedProductIds, true), 403);

        Review::updateOrCreate(
            ['user_id' => $request->user()->id, 'product_id' => $validated['product_id']],
            ['order_id' => $order->id, 'rating' => $validated['rating']],
        );

        return redirect()->route('orders.show', $token)->with('status', 'Thanks for your review!');
    }
}
