<?php

use App\Enums\PaymentMethodType;
use App\Models\PaymentMethod;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Payment Methods')] class extends Component {
    use WithFileUploads;

    public ?int $editingId = null;
    public string $type = 'bank';
    public string $name = '';
    public string $accountName = '';
    public string $accountNumber = '';
    public $photo = null;
    public bool $removePhoto = false;
    public ?string $currentImage = null;
    public bool $isActive = true;

    #[Computed]
    public function paymentMethods()
    {
        return PaymentMethod::ordered()->get();
    }

    #[Computed]
    public function types(): array
    {
        return PaymentMethodType::cases();
    }

    public function create(): void
    {
        $this->resetForm();
        Flux::modal('payment-method-form')->show();
    }

    public function edit(int $id): void
    {
        $method = PaymentMethod::findOrFail($id);

        $this->resetForm();
        $this->editingId = $method->id;
        $this->type = $method->type->value;
        $this->name = $method->name;
        $this->accountName = $method->account_name ?? '';
        $this->accountNumber = $method->account_number ?? '';
        $this->currentImage = $method->image_path;
        $this->isActive = $method->is_active;

        Flux::modal('payment-method-form')->show();
    }

    public function save(): void
    {
        $rules = [
            'type' => ['required', Rule::in(array_column(PaymentMethodType::cases(), 'value'))],
            'name' => ['required', 'string', 'max:255'],
            'isActive' => ['boolean'],
        ];

        if ($this->type === PaymentMethodType::Bank->value) {
            $rules['accountName'] = ['required', 'string', 'max:255'];
            $rules['accountNumber'] = ['required', 'string', 'max:100'];
        } else {
            $rules['photo'] = [
                $this->currentImage && ! $this->removePhoto ? 'nullable' : 'required',
                'image',
                'max:4096',
            ];
        }

        $validated = $this->validate($rules);

        $imagePath = $this->currentImage;

        if ($this->photo) {
            if ($imagePath) {
                Storage::disk('supabase')->delete($imagePath);
            }
            $imagePath = $this->photo->store('payment-methods', 'supabase');
        } elseif ($this->removePhoto && $imagePath) {
            Storage::disk('supabase')->delete($imagePath);
            $imagePath = null;
        }

        PaymentMethod::updateOrCreate(
            ['id' => $this->editingId],
            [
                'type' => $validated['type'],
                'name' => $validated['name'],
                'account_name' => $this->type === PaymentMethodType::Bank->value ? $this->accountName : null,
                'account_number' => $this->type === PaymentMethodType::Bank->value ? $this->accountNumber : null,
                'image_path' => $this->type === PaymentMethodType::Qris->value ? $imagePath : null,
                'is_active' => $this->isActive,
            ],
        );

        Flux::modal('payment-method-form')->close();
        Flux::toast(variant: 'success', text: $this->editingId ? 'Payment method updated.' : 'Payment method added.');
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $method = PaymentMethod::findOrFail($id);
        $method->update(['is_active' => ! $method->is_active]);
    }

    public function delete(int $id): void
    {
        $method = PaymentMethod::findOrFail($id);

        if ($method->image_path) {
            Storage::disk('supabase')->delete($method->image_path);
        }

        $method->delete();

        Flux::toast(variant: 'success', text: 'Payment method deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'accountName', 'accountNumber', 'photo', 'removePhoto', 'currentImage']);
        $this->type = PaymentMethodType::Bank->value;
        $this->isActive = true;
        $this->resetErrorBag();
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="mb-1 text-xs tracking-[0.3em] text-neutral-500 uppercase dark:text-neutral-400">Master Data</p>
            <flux:heading size="xl">Payment Methods</flux:heading>
        </div>

        <flux:button variant="primary" wire:click="create">New Payment Method</flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Details</flux:table.column>
            <flux:table.column align="center">Status</flux:table.column>
            <flux:table.column align="center">Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->paymentMethods as $method)
                <flux:table.row wire:key="payment-method-{{ $method->id }}">
                    <flux:table.cell>{{ $method->name }}</flux:table.cell>
                    <flux:table.cell>{{ $method->type->label() }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($method->type === \App\Enums\PaymentMethodType::Bank)
                            {{ $method->account_number }}
                        @elseif ($method->image_path)
                            <div class="h-10 w-10 overflow-hidden border border-neutral-200 dark:border-neutral-700">
                                <img src="{{ $method->image_url }}" alt="{{ $method->name }}" class="h-full w-full object-cover">
                            </div>
                        @else
                            —
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:switch :checked="$method->is_active" wire:click="toggleActive({{ $method->id }})" />
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify-center gap-2">
                            <flux:button size="sm" variant="ghost" icon="pencil" tooltip="Edit" aria-label="Edit" wire:click="edit({{ $method->id }})" />
                            <flux:button size="sm" variant="ghost" icon="trash" tooltip="Delete" aria-label="Delete" wire:click="delete({{ $method->id }})" wire:confirm="Delete this payment method?" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center text-neutral-500 dark:text-neutral-400">No payment methods yet.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="payment-method-form" class="w-full max-w-md">
        <form wire:submit="save" class="flex max-h-[80vh] flex-col">
            <div class="pe-8">
                <flux:heading size="lg">{{ $editingId ? 'Edit Payment Method' : 'New Payment Method' }}</flux:heading>
            </div>

            <div class="mt-6 min-h-0 flex-1 space-y-6 overflow-y-auto ps-1 pe-1 pb-1">
                <flux:select wire:model.live="type" label="Type">
                    @foreach ($this->types as $typeOption)
                        <flux:select.option value="{{ $typeOption->value }}">{{ $typeOption->label() }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="name" label="Name" placeholder="e.g. BCA" autofocus />

                @if ($type === \App\Enums\PaymentMethodType::Bank->value)
                    <flux:input wire:model="accountName" label="Account Name" placeholder="e.g. PT Embodied Studio" />
                    <flux:input wire:model="accountNumber" label="Account Number" placeholder="e.g. 1234567890" />
                @else
                    <div>
                        <label for="payment-method-photo" class="mb-2 block text-sm font-medium dark:text-neutral-100">QR Image</label>
                        <input
                            id="payment-method-photo"
                            type="file"
                            wire:model="photo"
                            accept="image/*"
                            class="block w-full text-sm text-neutral-600 file:mr-4 file:border-0 file:bg-neutral-900 file:px-4 file:py-2 file:text-xs file:tracking-[0.15em] file:text-white file:uppercase dark:text-neutral-400"
                        >
                        <div wire:loading wire:target="photo" class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">Uploading…</div>
                        @error('photo') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="mt-3 h-32 w-32 border border-neutral-200 object-cover dark:border-neutral-700">
                        @elseif ($currentImage && ! $removePhoto)
                            <div class="mt-3 flex items-center gap-3">
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($currentImage) }}" alt="Current QR" class="h-32 w-32 border border-neutral-200 object-cover dark:border-neutral-700">
                                <flux:checkbox wire:model="removePhoto" label="Remove image" />
                            </div>
                        @endif
                    </div>
                @endif

                <flux:switch wire:model="isActive" label="Active" description="Visible to customers at checkout" />
            </div>

            <div class="flex shrink-0 justify-end gap-2 border-t border-neutral-200 pt-6 dark:border-neutral-800">
                <flux:modal.close>
                    <flux:button variant="filled">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" type="submit">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
