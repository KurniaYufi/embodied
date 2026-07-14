<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('Products')] class extends Component {
    use WithPagination, WithFileUploads;

    const GRADIENTS = [
        'from-neutral-300 to-neutral-400' => 'Neutral',
        'from-neutral-200 to-neutral-400' => 'Slate',
        'from-stone-200 to-stone-400' => 'Stone',
        'from-sky-100 to-neutral-300' => 'Sky',
        'from-neutral-300 to-neutral-500' => 'Charcoal',
        'from-emerald-900/30 to-neutral-400' => 'Emerald',
    ];

    public ?int $editingId = null;
    public string $search = '';

    public string $name = '';
    public ?int $categoryId = null;
    public string $description = '';
    public ?int $price = null;
    public string $gradient = 'from-neutral-300 to-neutral-400';
    public bool $isBestseller = false;
    public bool $isNew = false;
    public int $stock = 0;

    /** @var array<int, int> */
    public array $sizeIds = [];

    public $photo = null;

    public bool $removePhoto = false;

    public ?string $currentImage = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function products()
    {
        return Product::with(['category', 'sizes'])
            ->when($this->search, fn ($query) => $query->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($this->search).'%']))
            ->orderByDesc('id')
            ->paginate(10);
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    #[Computed]
    public function sizes()
    {
        return Size::orderBy('sort_order')->get();
    }

    #[Computed]
    public function gradients(): array
    {
        return self::GRADIENTS;
    }

    public function create(): void
    {
        $this->resetForm();
        Flux::modal('product-form')->show();
    }

    public function edit(int $id): void
    {
        $product = Product::with('sizes')->findOrFail($id);

        $this->resetForm();

        $this->editingId = $product->id;
        $this->name = $product->name;
        $this->categoryId = $product->category_id;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->gradient = $product->gradient;
        $this->isBestseller = $product->is_bestseller;
        $this->isNew = $product->is_new;
        $this->stock = $product->stock;
        $this->sizeIds = $product->sizes->pluck('id')->all();
        $this->currentImage = $product->image;

        Flux::modal('product-form')->show();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'categoryId' => ['nullable', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'gradient' => ['required', Rule::in(array_keys(self::GRADIENTS))],
            'stock' => ['required', 'integer', 'min:0'],
            'sizeIds' => ['array'],
            'sizeIds.*' => ['exists:sizes,id'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        $slug = Str::slug($validated['name']);

        $slugTaken = Product::where('slug', $slug)
            ->when($this->editingId, fn ($query) => $query->whereKeyNot($this->editingId))
            ->exists();

        if ($slugTaken) {
            $this->addError('name', 'A product with this name already exists.');

            return;
        }

        $imagePath = $this->currentImage;

        if ($this->photo) {
            if ($imagePath) {
                Storage::disk('supabase')->delete($imagePath);
            }
            $imagePath = $this->photo->store('products', 'supabase');
        } elseif ($this->removePhoto && $imagePath) {
            Storage::disk('supabase')->delete($imagePath);
            $imagePath = null;
        }

        $product = Product::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $validated['name'],
                'slug' => $slug,
                'category_id' => $validated['categoryId'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'gradient' => $validated['gradient'],
                'image' => $imagePath,
                'is_bestseller' => $this->isBestseller,
                'is_new' => $this->isNew,
                'stock' => $validated['stock'],
            ],
        );

        $product->sizes()->sync($validated['sizeIds'] ?? []);

        Flux::modal('product-form')->close();
        Flux::toast(variant: 'success', text: $this->editingId ? 'Product updated.' : 'Product created.');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('supabase')->delete($product->image);
        }

        $product->delete();

        Flux::toast(variant: 'success', text: 'Product deleted.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'name', 'categoryId', 'description', 'price', 'isBestseller',
            'isNew', 'stock', 'sizeIds', 'photo', 'removePhoto', 'currentImage',
        ]);
        $this->gradient = array_key_first(self::GRADIENTS);
        $this->resetErrorBag();
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="mb-1 text-xs tracking-[0.3em] text-neutral-500 uppercase dark:text-neutral-400">Master Data</p>
            <flux:heading size="xl">Products</flux:heading>
        </div>

        <flux:button variant="primary" wire:click="create">New Product</flux:button>
    </div>

    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search products..." class="max-w-xs" />

    <flux:table>
        <flux:table.columns>
            <flux:table.column align="center">Image</flux:table.column>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Category</flux:table.column>
            <flux:table.column>Price</flux:table.column>
            <flux:table.column>Badges</flux:table.column>
            <flux:table.column>Stock</flux:table.column>
            <flux:table.column>Sizes</flux:table.column>
            <flux:table.column align="center">Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->products as $product)
                <flux:table.row wire:key="product-{{ $product->id }}">
                    <flux:table.cell align="center">
                        <div class="h-10 w-10 shrink-0 overflow-hidden bg-neutral-100 dark:bg-neutral-800">
                            <x-product-image :image="$product->image_url" :gradient="$product->gradient" :alt="$product->name" />
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $product->name }}</flux:table.cell>
                    <flux:table.cell>{{ $product->category?->name ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $product->formatted_price }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($product->badge)
                            <flux:badge size="sm">{{ $product->badge }}</flux:badge>
                        @else
                            —
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$product->stock > 0 ? 'green' : 'red'">
                            {{ $product->stock > 0 ? $product->stock : 'Out of stock' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $product->sizes->pluck('label')->implode(', ') ?: '—' }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify-center gap-2">
                            <flux:button size="sm" variant="ghost" icon="pencil" tooltip="Edit" aria-label="Edit" wire:click="edit({{ $product->id }})" />
                            <flux:button size="sm" variant="ghost" icon="trash" tooltip="Delete" aria-label="Delete" wire:click="delete({{ $product->id }})" wire:confirm="Delete this product?" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center text-neutral-500 dark:text-neutral-400">No products yet.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{ $this->products->links() }}

    <flux:modal name="product-form" class="w-full max-w-2xl">
        <form wire:submit="save" class="flex max-h-[80vh] flex-col">
            <div class="pe-8">
                <flux:heading size="lg">{{ $editingId ? 'Edit Product' : 'New Product' }}</flux:heading>
            </div>

            <div class="mt-6 min-h-0 flex-1 space-y-6 overflow-y-auto ps-1 pe-1 pb-1">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="name" label="Name" placeholder="e.g. Drape Linen Coat" autofocus />

                    <flux:select wire:model="categoryId" label="Category" placeholder="Select category">
                        @foreach ($this->categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:textarea wire:model="description" label="Description" rows="3" />

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="price" type="number" label="Price (Rp)" min="0" />
                    <flux:input wire:model="stock" type="number" label="Stock Quantity" min="0" />
                </div>

                <div>
                    <label for="product-photo" class="mb-2 block text-sm font-medium dark:text-neutral-100">Product Photo</label>
                    <input
                        id="product-photo"
                        type="file"
                        wire:model="photo"
                        accept="image/*"
                        class="block w-full text-sm text-neutral-600 file:mr-4 file:border-0 file:bg-neutral-900 file:px-4 file:py-2 file:text-xs file:tracking-[0.15em] file:text-white file:uppercase dark:text-neutral-400"
                    >
                    <div wire:loading wire:target="photo" class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">Uploading…</div>
                    @error('photo') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="mt-3 h-24 w-24 border border-neutral-200 object-cover dark:border-neutral-700">
                    @elseif ($currentImage && ! $removePhoto)
                        <div class="mt-3 flex items-center gap-3">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($currentImage) }}" alt="Current photo" class="h-24 w-24 border border-neutral-200 object-cover dark:border-neutral-700">
                            <flux:checkbox wire:model="removePhoto" label="Remove photo" />
                        </div>
                    @endif
                </div>

                <flux:select wire:model="gradient" label="{{ $currentImage || $photo ? 'Backdrop Tone (used if photo is removed)' : 'Backdrop Tone' }}">
                    @foreach ($this->gradients as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                <div>
                    <flux:checkbox.group wire:model="sizeIds" label="Available Sizes" class="flex flex-wrap items-center gap-x-6 gap-y-2 *:data-flux-field:mb-0!">
                        @foreach ($this->sizes as $size)
                            <flux:checkbox value="{{ $size->id }}" label="{{ $size->label }}" />
                        @endforeach
                    </flux:checkbox.group>
                </div>

                <div>
                    <flux:checkbox.group label="Badge" class="flex flex-wrap items-center gap-x-6 gap-y-2 *:data-flux-field:mb-0!">
                        <flux:checkbox wire:model="isBestseller" label="Bestseller badge" />
                        <flux:checkbox wire:model="isNew" label="New badge" />
                    </flux:checkbox.group>
                </div>
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
