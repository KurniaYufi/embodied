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
        <p class="mb-1 text-xs tracking-[0.3em] text-neutral-500 uppercase dark:text-neutral-400">Transaksi</p>
        <flux:heading size="xl">Transactions</flux:heading>
    </div>

    @php $statusOptions = collect($this->statuses)->mapWithKeys(fn ($status) => [$status->value => $status->label()])->all(); @endphp

    <div class="flex flex-wrap gap-3">
        <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by customer or order number..." class="max-w-xs" />

        <div class="ml-auto">
            <x-livewire-dropdown
                bind="statusFilter"
                :options="$statusOptions"
                :selected="$statusFilter"
                placeholder="All statuses"
            />
        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Order</flux:table.column>
            <flux:table.column>Customer</flux:table.column>
            <flux:table.column>Total</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Proof</flux:table.column>
            <flux:table.column>Placed</flux:table.column>
            <flux:table.column align="center">Action</flux:table.column>
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
                            <a href="{{ $order->payment_proof_url }}" target="_blank" class="text-neutral-900 underline underline-offset-4 dark:text-neutral-100">View</a>
                        @else
                            <span class="text-neutral-500 dark:text-neutral-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $order->created_at->format('d M Y') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify-center">
                            <flux:button size="sm" variant="ghost" icon="eye" tooltip="View" aria-label="View" wire:click="showOrder({{ $order->id }})" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center text-neutral-500 dark:text-neutral-400">No transactions yet.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{ $this->orders->links() }}

    <flux:modal name="order-detail" class="w-full max-w-2xl">
        @if ($this->viewingOrder)
            @php $order = $this->viewingOrder @endphp

            <div class="flex max-h-[80vh] flex-col">
                <div class="flex items-start justify-between gap-6 pe-8">
                    <div>
                        <flux:heading size="lg">{{ $order->number }}</flux:heading>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <flux:badge :color="$order->status->color()">{{ $order->status->label() }}</flux:badge>
                </div>

                <div class="mt-6 min-h-0 flex-1 space-y-6 overflow-y-auto ps-1 pe-1 pb-1">
                    <div>
                        <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase dark:text-neutral-400">Customer</p>
                        <p class="text-sm text-neutral-900 dark:text-neutral-100">{{ $order->customer_name }} &middot; {{ $order->customer_phone }}</p>
                        <p class="text-sm whitespace-pre-line text-neutral-600 dark:text-neutral-400">{{ $order->shipping_address }}</p>
                        @if ($order->notes)
                            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Notes: {{ $order->notes }}</p>
                        @endif
                    </div>

                    <div>
                        <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase dark:text-neutral-400">Items</p>
                        <div class="divide-y divide-neutral-100 border-y border-neutral-100 dark:divide-neutral-800 dark:border-neutral-800">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between py-3 text-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 shrink-0 overflow-hidden bg-neutral-100 dark:bg-neutral-800">
                                            <x-product-image :image="$item->image" :gradient="$item->gradient" :alt="$item->product_name" />
                                        </div>
                                        <span class="text-neutral-900 dark:text-neutral-100">{{ $item->product_name }} <span class="text-neutral-500 dark:text-neutral-400">({{ $item->size ?? 'One Size' }} &times; {{ $item->quantity }})</span></span>
                                    </div>
                                    <span class="text-neutral-900 dark:text-neutral-100">{{ $item->formatted_line_total }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between pt-3 text-sm font-medium text-neutral-900 dark:text-neutral-100">
                            <span>Subtotal</span>
                            <span>{{ $order->formatted_subtotal }}</span>
                        </div>
                    </div>

                    @if ($order->hasPaymentProof())
                        <div>
                            <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase dark:text-neutral-400">Proof of Payment</p>
                            <a href="{{ $order->payment_proof_url }}" target="_blank">
                                <img src="{{ $order->payment_proof_url }}" alt="Payment proof" class="max-h-72 border border-neutral-200 object-contain dark:border-neutral-700">
                            </a>
                        </div>
                    @else
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">No payment proof uploaded yet.</p>
                    @endif
                </div>

                <div class="flex shrink-0 items-end gap-3 border-t border-neutral-200 pt-6 dark:border-neutral-800">
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
