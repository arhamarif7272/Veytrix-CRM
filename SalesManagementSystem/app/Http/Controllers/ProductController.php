<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:50',
            'unit_price'  => 'required|numeric|min:0',
            'unit'        => 'nullable|string|max:50',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:50',
            'unit_price'  => 'required|numeric|min:0',
            'unit'        => 'nullable|string|max:50',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
