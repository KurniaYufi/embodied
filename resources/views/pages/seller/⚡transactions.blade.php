<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Transactions')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public ?int $viewingId = null;
    public string $newStatus = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function orders()
    {
        return Order::query()
            ->when($this->search, fn ($query) => $query->where(function ($query) {
                $needle = '%'.mb_strtolower($this->search).'%';
                $query->whereRaw('LOWER(customer_name) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(number) LIKE ?', [$needle]);
            }))
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function viewingOrder(): ?Order
    {
        return $this->viewingId ? Order::with('items')->find($this->viewingId) : null;
    }

    #[Computed]
    public function statuses(): array
    {
        return OrderStatus::options();
    }

    public function showOrder(int $id): void
    {
        $order = Order::findOrFail($id);

        $this->viewingId = $order->id;
        $this->newStatus = $order->status->value;

        Flux::modal('order-detail')->show();
    }

    public function updateStatus(): void
    {
        $order = Order::findOrFail($this->viewingId);

        $order->update(['status' => $this->newStatus]);

        Flux::toast(variant: 'success', text: 'Order status updated.');
    }
}; ?>

<div class="flex flex-col gap-6">
    <div>
        <p class="mb-1 text-xs tracking-[0.3em] text-neutral-500 uppercase">Transaksi</p>
        <flux:heading size="xl">Transactions</flux:heading>
    </div>

    <div class="flex flex-wrap gap-3">
        <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by customer or order number..." class="max-w-xs" />

        <flux:select wire:model.live="statusFilter" placeholder="All statuses" class="max-w-xs">
            <flux:select.option value="">All statuses</flux:select.option>
            @foreach ($this->statuses as $status)
                <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Order</flux:table.column>
            <flux:table.column>Customer</flux:table.column>
            <flux:table.column>Total</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Proof</flux:table.column>
            <flux:table.column>Placed</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->orders as $order)
                <flux:table.row wire:key="order-{{ $order->id }}">
                    <flux:table.cell>{{ $order->number }}</flux:table.cell>
                    <flux:table.cell>{{ $order->customer_name }}</flux:table.cell>
                    <flux:table.cell>{{ $order->formatted_subtotal }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$order->status->color()">{{ $order->status->label() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($order->hasPaymentProof())
                            <a href="{{ $order->payment_proof_url }}" target="_blank" class="text-neutral-900 underline underline-offset-4">View</a>
                        @else
                            <span class="text-neutral-500">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $order->created_at->format('d M Y') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" variant="ghost" wire:click="showOrder({{ $order->id }})">View</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center text-neutral-500">No transactions yet.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{ $this->orders->links() }}

    <flux:modal name="order-detail" class="w-full max-w-2xl">
        @if ($this->viewingOrder)
            @php $order = $this->viewingOrder @endphp

            <div class="space-y-6">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:heading size="lg">{{ $order->number }}</flux:heading>
                        <p class="text-sm text-neutral-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <flux:badge :color="$order->status->color()">{{ $order->status->label() }}</flux:badge>
                </div>

                <div>
                    <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">Customer</p>
                    <p class="text-sm">{{ $order->customer_name }} &middot; {{ $order->customer_phone }}</p>
                    <p class="text-sm whitespace-pre-line text-neutral-600">{{ $order->shipping_address }}</p>
                    @if ($order->notes)
                        <p class="mt-1 text-xs text-neutral-500">Notes: {{ $order->notes }}</p>
                    @endif
                </div>

                <div>
                    <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">Items</p>
                    <div class="divide-y divide-neutral-100 border-y border-neutral-100">
                        @foreach ($order->items as $item)
                            <div class="flex items-center justify-between py-3 text-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 shrink-0 overflow-hidden bg-neutral-100">
                                        <x-product-image :image="$item->image" :gradient="$item->gradient" :alt="$item->product_name" />
                                    </div>
                                    <span>{{ $item->product_name }} <span class="text-neutral-500">({{ $item->size ?? 'One Size' }} &times; {{ $item->quantity }})</span></span>
                                </div>
                                <span>{{ $item->formatted_line_total }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-between pt-3 text-sm font-medium">
                        <span>Subtotal</span>
                        <span>{{ $order->formatted_subtotal }}</span>
                    </div>
                </div>

                @if ($order->hasPaymentProof())
                    <div>
                        <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">Proof of Payment</p>
                        <a href="{{ $order->payment_proof_url }}" target="_blank">
                            <img src="{{ $order->payment_proof_url }}" alt="Payment proof" class="max-h-72 border border-neutral-200 object-contain">
                        </a>
                    </div>
                @else
                    <p class="text-sm text-neutral-500">No payment proof uploaded yet.</p>
                @endif

                <div class="flex items-end gap-3 border-t border-neutral-200 pt-6">
                    <flux:select wire:model="newStatus" label="Update Status" class="flex-1">
                        @foreach ($this->statuses as $status)
                            <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:button variant="primary" wire:click="updateStatus">Save</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
