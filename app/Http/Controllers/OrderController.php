<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(string $token): View
    {
        $order = Order::with('items')->where('access_token', $token)->firstOrFail();

        return view('order-status', ['order' => $order]);
    }

    public function uploadProof(Request $request, string $token): RedirectResponse
    {
        $order = Order::where('access_token', $token)->firstOrFail();

        if (! in_array($order->status, [OrderStatus::PendingPayment, OrderStatus::AwaitingConfirmation], true)) {
            return back()->withErrors(['proof' => 'This order can no longer accept a new payment proof.']);
        }

        $request->validate([
            'proof' => ['required', 'image', 'max:5120'],
        ]);

        if ($order->payment_proof_path) {
            Storage::disk('public')->delete($order->payment_proof_path);
        }

        $path = $request->file('proof')->store('payment-proofs', 'public');

        $order->update([
            'payment_proof_path' => $path,
            'payment_proof_uploaded_at' => now(),
            'status' => OrderStatus::AwaitingConfirmation,
        ]);

        return redirect()->route('orders.show', $token)->with('status', 'Payment proof uploaded. We will confirm it shortly.');
    }
}
