<?php

use App\Models\Category;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Categories')] class extends Component {
    use WithPagination;

    public ?int $editingId = null;
    public string $name = '';
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return Category::withCount('products')
            ->when($this->search, fn ($query) => $query->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($this->search).'%']))
            ->orderBy('name')
            ->paginate(10);
    }

    public function create(): void
    {
        $this->resetForm();
        Flux::modal('category-form')->show();
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);

        $this->resetForm();
        $this->editingId = $category->id;
        $this->name = $category->name;
        Flux::modal('category-form')->show();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $slug = Str::slug($validated['name']);

        $slugTaken = Category::where('slug', $slug)
            ->when($this->editingId, fn ($query) => $query->whereKeyNot($this->editingId))
            ->exists();

        if ($slugTaken) {
            $this->addError('name', 'A category with this name already exists.');

            return;
        }

        Category::updateOrCreate(
            ['id' => $this->editingId],
            ['name' => $validated['name'], 'slug' => $slug],
        );

        Flux::modal('category-form')->close();
        Flux::toast(variant: 'success', text: $this->editingId ? 'Category updated.' : 'Category created.');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Category::findOrFail($id)->delete();

        Flux::toast(variant: 'success', text: 'Category deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name']);
        $this->resetErrorBag();
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="mb-1 text-xs tracking-[0.3em] text-neutral-500 uppercase">Master Data</p>
            <flux:heading size="xl">Categories</flux:heading>
        </div>

        <flux:button variant="primary" wire:click="create">New Category</flux:button>
    </div>

    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search categories..." class="max-w-xs" />

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Products</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->categories as $category)
                <flux:table.row wire:key="category-{{ $category->id }}">
                    <flux:table.cell>{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>{{ $category->products_count }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify-end gap-2">
                            <flux:button size="sm" variant="ghost" wire:click="edit({{ $category->id }})">Edit</flux:button>
                            <flux:button size="sm" variant="ghost" wire:click="delete({{ $category->id }})" wire:confirm="Delete this category? Products in it will become uncategorized.">Delete</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3" class="text-center text-neutral-500">No categories yet.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{ $this->categories->links() }}

    <flux:modal name="category-form" class="max-w-md">
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">{{ $editingId ? 'Edit Category' : 'New Category' }}</flux:heading>

            <flux:input wire:model="name" label="Name" placeholder="e.g. Women" autofocus />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="filled">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" type="submit">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
