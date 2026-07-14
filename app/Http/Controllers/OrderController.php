<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()->latest()->paginate(10);

        return view('order-history', ['orders' => $orders]);
    }

    public function show(string $token): View
    {
        $order = Order::with('items')->where('access_token', $token)->firstOrFail();

        $order->cancelDueToExpiredPayment();

        return view('order-status', [
            'order' => $order,
            'paymentMethods' => PaymentMethod::active()->ordered()->get(),
        ]);
    }

    public function uploadProof(Request $request, string $token): RedirectResponse
    {
        $order = Order::where('access_token', $token)->firstOrFail();

        $order->cancelDueToExpiredPayment();

        if (! in_array($order->status, [OrderStatus::PendingPayment, OrderStatus::AwaitingConfirmation], true)) {
            return back()->withErrors(['proof' => 'This order can no longer accept a new payment proof.']);
        }

        $validated = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'proof' => ['required', 'image', 'max:5120'],
        ]);

        if ($order->payment_proof_path) {
            Storage::disk('supabase')->delete($order->payment_proof_path);
        }

        $path = $request->file('proof')->store('payment-proofs', 'supabase');

        $order->update([
            'payment_method_id' => $validated['payment_method_id'],
            'payment_proof_path' => $path,
            'payment_proof_uploaded_at' => now(),
            'status' => OrderStatus::AwaitingConfirmation,
        ]);

        return redirect()->route('orders.show', $token)->with('status', 'Payment proof uploaded. We will confirm it shortly.');
    }
}
