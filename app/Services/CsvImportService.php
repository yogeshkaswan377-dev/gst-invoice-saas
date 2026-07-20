<?php

namespace App\Services;

use App\DTOs\CsvRowData;
use App\DTOs\StockAdjustmentData;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class CsvImportService
{
    public function __construct(
        private ProductService $productService,
        private StockService $stockService,
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * Import CSV data with specified mode.
     *
     * @param string $filePath
     * @param string $mode
     * @param int $companyId
     * @param int $userId
     * @return ImportResult
     */
    public function import(string $filePath, string $mode, int $companyId, int $userId): ImportResult
    {
        $rows = array_map('str_getcsv', file($filePath));
        $header = array_shift($rows);
        $header = array_map('strtolower', $header);

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +1 for 0-based, +1 for header
            $rowData = $this->parseRow($header, $row);

            // Validate the row
            $rowErrors = $this->validateRow($rowData, $mode, $companyId);
            if (!empty($rowErrors)) {
                $skipped++;
                $errors[] = "Row {$rowNumber}: " . implode(', ', $rowErrors);
                continue;
            }

            try {
                match ($mode) {
                    'add_new' => $this->handleAddNew($rowData, $companyId, $userId),
                    'update_existing' => $this->handleUpdateExisting($rowData, $companyId, $userId),
                    'replace_stock' => $this->handleReplaceStock($rowData, $companyId, $userId),
                    'add_stock' => $this->handleAddStock($rowData, $companyId, $userId),
                };
                $imported++;
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        return new ImportResult($imported, $skipped, $errors);
    }

    private function parseRow(array $header, array $row): CsvRowData
    {
        $data = array_combine($header, $row);
        return new CsvRowData(
            itemNo: $data['item_no'] ?? null,
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            hsnSacCode: $data['hsn_sac_code'] ?? null,
            unitPrice: isset($data['unit_price']) ? (float) $data['unit_price'] : null,
            gstRate: isset($data['gst_rate']) ? (float) $data['gst_rate'] : null,
            unit: $data['unit'] ?? null,
            stock: isset($data['stock']) ? (float) $data['stock'] : null,
            stockUnit: $data['stock_unit'] ?? null,
            stockDeductionType: $data['stock_deduction_type'] ?? 'Meter',
            consumptionPerPiece: isset($data['consumption_per_piece']) ? (float) $data['consumption_per_piece'] : null,
            minimumStock: isset($data['minimum_stock']) ? (float) $data['minimum_stock'] : null,
            sellingPrice: isset($data['selling_price']) ? (float) $data['selling_price'] : null,
        );
    }

    private function validateRow(CsvRowData $data, string $mode, int $companyId): array
    {
        $errors = [];

        if (empty($data->name)) {
            $errors[] = 'Product name is required.';
        }

        // For add_new, item_no must be unique
        if ($mode === 'add_new' && !empty($data->itemNo)) {
            $exists = $this->productRepository->findByItemNo($data->itemNo, $companyId);
            if ($exists) {
                $errors[] = "Item No '{$data->itemNo}' already exists.";
            }
        }

        // For update_existing/replace_stock/add_stock, product must exist by item_no (or name)
        if (in_array($mode, ['update_existing', 'replace_stock', 'add_stock'])) {
            $product = null;
            if (!empty($data->itemNo)) {
                $product = $this->productRepository->findByItemNo($data->itemNo, $companyId);
            }
            if (!$product && !empty($data->name)) {
                // find by name (first match)
                $product = $this->productRepository->findByName($data->name, $companyId);
            }
            if (!$product) {
                $errors[] = "Product not found by item_no or name.";
            }
        }

        // Stock must be numeric if present
        if (isset($data->stock) && !is_numeric($data->stock)) {
            $errors[] = 'Stock must be a number.';
        }

        // For piece type, consumption_per_piece required
        if ($data->stockDeductionType === 'Piece' && empty($data->consumptionPerPiece)) {
            $errors[] = 'Consumption per piece required for Piece type.';
        }

        return $errors;
    }

    private function handleAddNew(CsvRowData $data, int $companyId, int $userId): void
    {
        // Create product with stock if provided (default 0)
        $productData = new \App\DTOs\ProductData(
            companyId: $companyId,
            itemNo: $data->itemNo,
            name: $data->name,
            description: $data->description,
            hsnSacCode: $data->hsnSacCode,
            unitPrice: $data->unitPrice ?? 0,
            gstRate: $data->gstRate ?? 18,
            unit: $data->unit ?? 'pcs',
            isActive: true,
            stock: $data->stock ?? 0,
            stockUnit: $data->stockUnit ?? 'Mtr',
            stockDeductionType: $data->stockDeductionType ?? 'Meter',
            consumptionPerPiece: $data->consumptionPerPiece,
            minimumStock: $data->minimumStock ?? 0,
            sellingPrice: $data->sellingPrice,
        );
        $this->productService->create($productData);
        // No stock history log for initial stock? We could log a manual add if stock > 0.
        if (($data->stock ?? 0) > 0) {
            $product = $this->productRepository->findByItemNo($data->itemNo, $companyId);
            $this->stockService->adjustStock(new StockAdjustmentData(
                productId: $product->id,
                companyId: $companyId,
                userId: $userId,
                adjustmentType: 'add',
                quantity: $data->stock,
                remarks: 'Initial stock via CSV import (add_new)'
            ));
        }
    }

    private function handleUpdateExisting(CsvRowData $data, int $companyId, int $userId): void
    {
        $product = $this->findProduct($data, $companyId);
        if (!$product) return;

        $productData = new \App\DTOs\ProductData(
            companyId: $companyId,
            itemNo: $data->itemNo ?? $product->item_no,
            name: $data->name ?? $product->name,
            description: $data->description ?? $product->description,
            hsnSacCode: $data->hsnSacCode ?? $product->hsn_sac_code,
            unitPrice: $data->unitPrice ?? $product->unit_price,
            gstRate: $data->gstRate ?? $product->gst_rate,
            unit: $data->unit ?? $product->unit,
            isActive: true,
            stock: $product->stock, // stock not changed
            stockUnit: $data->stockUnit ?? $product->stock_unit,
            stockDeductionType: $data->stockDeductionType ?? $product->stock_deduction_type,
            consumptionPerPiece: $data->consumptionPerPiece ?? $product->consumption_per_piece,
            minimumStock: $data->minimumStock ?? $product->minimum_stock,
            sellingPrice: $data->sellingPrice ?? $product->selling_price,
        );
        $this->productService->update($product, $productData);
    }

    private function handleReplaceStock(CsvRowData $data, int $companyId, int $userId): void
    {
        $product = $this->findProduct($data, $companyId);
        if (!$product) return;

        // Replace stock means set exact stock value
        $adjustment = new StockAdjustmentData(
            productId: $product->id,
            companyId: $companyId,
            userId: $userId,
            adjustmentType: 'set',
            quantity: $data->stock,
            remarks: 'Stock replaced via CSV import (replace_stock)'
        );
        $this->stockService->adjustStock($adjustment);
    }

    private function handleAddStock(CsvRowData $data, int $companyId, int $userId): void
    {
        $product = $this->findProduct($data, $companyId);
        if (!$product) return;

        // Add stock (positive addition)
        $adjustment = new StockAdjustmentData(
            productId: $product->id,
            companyId: $companyId,
            userId: $userId,
            adjustmentType: 'add',
            quantity: $data->stock,
            remarks: 'Stock added via CSV import (add_stock)'
        );
        $this->stockService->adjustStock($adjustment);
    }

    private function findProduct(CsvRowData $data, int $companyId)
    {
        if (!empty($data->itemNo)) {
            return $this->productRepository->findByItemNo($data->itemNo, $companyId);
        }
        // fallback by name (first match)
        return $this->productRepository->findByName($data->name, $companyId);
    }
}
