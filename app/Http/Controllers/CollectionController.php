<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $category = (string) $request->query('category', '');
        $price = (string) $request->query('price', '');
        $stock = $request->boolean('stock', true);
        $sort = (string) $request->query('sort', 'recent');

        $products = Product::query()
            ->with('category')
            ->when($search !== '', fn ($query) => $query->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($search).'%']))
            ->when($category === 'new', fn ($query) => $query->where('is_new', true))
            ->when($category === 'bestseller', fn ($query) => $query->where('is_bestseller', true))
            ->when(! in_array($category, ['', 'new', 'bestseller'], true), fn ($query) => $query->whereHas(
                'category', fn ($query) => $query->where('slug', $category)
            ))
            ->when($price !== '', fn ($query) => match ($price) {
                'under-300' => $query->where('price', '<', 300000),
                '300-500' => $query->whereBetween('price', [300000, 500000]),
                '500-1000' => $query->whereBetween('price', [500000, 1000000]),
                '1000-plus' => $query->where('price', '>', 1000000),
                default => $query,
            })
            ->when($stock, fn ($query) => $query->where('in_stock', true))
            ->tap(fn ($query) => match ($sort) {
                'price-asc' => $query->orderBy('price'),
                'price-desc' => $query->orderByDesc('price'),
                'name-asc' => $query->orderBy('name'),
                default => $query->orderByDesc('id'),
            })
            ->get();

        return view('collection', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'search' => $search,
            'categoryFilter' => $category,
            'priceFilter' => $price,
            'stockFilter' => $stock,
            'sortFilter' => $sort,
        ]);
    }
}
