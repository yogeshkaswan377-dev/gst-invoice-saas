<?php

namespace App\Http\Controllers;

use App\DTOs\ProductData;
use App\DTOs\StockAdjustmentData;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\StockService;
use App\Repositories\Contracts\StockHistoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\AuditService;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private StockService $stockService,
        private StockHistoryRepositoryInterface $historyRepository
    ) {}

    public function index(Request $request)
    {
        $companyId = Auth::user()->current_company_id;

        $products = $this->productService->getAllForCompany($companyId, [
            'search'   => $request->input('search'),
            'gst_rate' => $request->input('gst_rate'),
        ]);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        Gate::authorize('create', Product::class);

        return view('products.create');
    }

    public function store(StoreProductRequest $request)
    {
        Gate::authorize('create', Product::class);

        $data = new ProductData(
            companyId: Auth::user()->current_company_id,
            itemNo: $request->item_no,
            name: $request->name,
            description: $request->description,
            hsnSacCode: $request->hsn_sac_code,
            unitPrice: $request->unit_price,
            gstRate: $request->gst_rate,
            unit: $request->unit,
            isActive: true,
            stock: $request->stock ?? 0,
            stockUnit: $request->stock_unit,
            stockDeductionType: $request->stock_deduction_type,
            consumptionPerPiece: $request->consumption_per_piece,
            minimumStock: $request->minimum_stock ?? 0,
            sellingPrice: $request->selling_price,
        );

        $this->productService->create($data);
        AuditService::log('created', Product::class, $product->id, 'Product created');
        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        Gate::authorize('update', $product);

        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        Gate::authorize('update', $product);

        $data = new ProductData(
            companyId: $product->company_id,
            itemNo: $request->item_no,
            name: $request->name,
            description: $request->description,
            hsnSacCode: $request->hsn_sac_code,
            unitPrice: $request->unit_price,
            gstRate: $request->gst_rate,
            unit: $request->unit,
            isActive: $request->boolean('is_active', true),
            stock: $product->stock,         // stock not changed via edit form
            stockUnit: $request->stock_unit,
            stockDeductionType: $request->stock_deduction_type,
            consumptionPerPiece: $request->consumption_per_piece,
            minimumStock: $request->minimum_stock ?? 0,
            sellingPrice: $request->selling_price,
        );

        $this->productService->update($product, $data);
        AuditService::log('updated', Product::class, $product->id, 'Product updated');
        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);

        AuditService::log('deleted', Product::class, $product->id, 'Product deleted');
        $this->productService->delete($product);
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    public function adjustStock(StockAdjustmentRequest $request, Product $product)
    {
        Gate::authorize('adjustStock', $product);

        $adjustmentData = new StockAdjustmentData(
            productId: $product->id,
            companyId: Auth::user()->current_company_id,
            userId: Auth::id(),
            adjustmentType: $request->adjustment_type,
            quantity: $request->quantity,
            remarks: $request->remarks,
        );

        try {
            $this->stockService->adjustStock($adjustmentData);
            AuditService::log('updated', Product::class, $product->id, 'Stock adjusted', [
                'type' => $request->adjustment_type,
                'quantity' => $request->quantity,
            ]);
            return redirect()->route('products.index')->with('success', 'Stock adjusted.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function stockHistory(Product $product)
    {
        $histories = $this->historyRepository->getForProduct($product->id, Auth::user()->current_company_id);
        return view('products.stock-history', compact('product', 'histories'));
    }

    public function import()
    {
        return view('products.import');
    }

    public function processImport(Request $request) // Placeholder, implement with CSV import service
    {
        // ...
    }

    public function export()
    {
        // Placeholder for CSV export
    }

    // ============================================================
    // AJAX Endpoints for Invoice Builder
    // ============================================================

    /**
     * Search products for invoice line item (AJAX)
     */
    public function search(Request $request)
    {
        $companyId = Auth::user()->current_company_id;

        $query = $request->get('q');
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

    /**
     * Get stock/deduction info for a product (AJAX)
     */
    public function stockInfo(Product $product)
    {
        return response()->json([
            'stock' => $product->stock,
            'stock_unit' => $product->stock_unit,
            'stock_deduction_type' => $product->stock_deduction_type,
            'consumption_per_piece' => $product->consumption_per_piece,
        ]);
    }
}
