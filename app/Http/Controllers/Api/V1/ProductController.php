<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::where('company_id', session('current_company_id'))
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('hsn_sac_code', 'LIKE', "%{$search}%");
            })
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'hsn_sac_code' => 'nullable|string|max:8',
            'unit_price' => 'required|numeric|min:0',
            'gst_rate' => 'nullable|numeric|min:0|max:100',
            'unit' => 'nullable|string|max:20',
        ]);

        $validated['company_id'] = session('current_company_id');
        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => new ProductResource($product),
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'hsn_sac_code' => 'nullable|string|max:8',
            'unit_price' => 'sometimes|numeric|min:0',
            'gst_rate' => 'nullable|numeric|min:0|max:100',
            'unit' => 'nullable|string|max:20',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => new ProductResource($product->fresh()),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }

    public function search(Request $request)
    {
        $companyId = Auth::user()->current_company_id;

        $query = $request->get('q'); // the search term
        $products = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('hsn_sac_code', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'hsn_sac_code', 'unit_price', 'gst_rate']);

        return response()->json($products);
    }

    public function stockInfo(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'stock' => $product->stock,
            'stock_unit' => $product->stock_unit,
            'stock_deduction_type' => $product->stock_deduction_type,
            'consumption_per_piece' => $product->consumption_per_piece,
        ]);
    }
}
