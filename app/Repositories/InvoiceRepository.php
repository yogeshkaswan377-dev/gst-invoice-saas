<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
{
    public function __construct(Invoice $model)
    {
        parent::__construct($model);
    }

    public function getByCompany(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('company_id', $companyId)
            ->with(['client', 'items', 'createdBy']);

        // Filter by invoice type
        if (!empty($filters['type'])) {
            $query->where('invoice_type', $filters['type']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by client
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->where('invoice_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('invoice_date', '<=', $filters['date_to']);
        }

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id, int $companyId): ?Invoice
    {
        return $this->model->where('id', $id)
            ->where('company_id', $companyId)
            ->with(['client', 'items', 'payments', 'createdBy'])
            ->first();
    }

    public function findByNumber(string $invoiceNumber, int $companyId): ?Invoice
    {
        return $this->model->where('invoice_number', $invoiceNumber)
            ->where('company_id', $companyId)
            ->with(['client', 'items', 'payments'])
            ->first();
    }

    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // Extract items from data; they don't belong to the invoices table
            $items = $data['items'] ?? [];
            unset($data['items']);

            $invoice = $this->model->create($data);

            if (!empty($items)) {
                foreach ($items as $index => $item) {
                    $item['sort_order'] = $index + 1;
                    $invoice->items()->create($item);
                }
            }

            return $invoice->load(['client', 'items']);
        });
    }

    public function update(int $id, array $data): Invoice
    {
        return DB::transaction(function () use ($id, $data) {
            $invoice = $this->model->findOrFail($id);
            $invoice->update($data);

            // Update items if provided
            if (isset($data['items'])) {
                // Delete existing items
                $invoice->items()->delete();

                // Create new items
                foreach ($data['items'] as $index => $item) {
                    $item['sort_order'] = $index + 1;
                    $invoice->items()->create($item);
                }
            }

            return $invoice->load(['client', 'items']);
        });
    }

    public function delete(int $id): bool
    {
        $invoice = $this->model->findOrFail($id);
        return $invoice->delete();
    }

    public function getByStatus(int $companyId, string $status): Collection
    {
        return $this->model->where('company_id', $companyId)
            ->where('status', $status)
            ->with(['client', 'items'])
            ->get();
    }

    public function getOverdue(int $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)
            ->where('due_date', '<', now())
            ->whereIn('status', ['sent', 'viewed', 'partially_paid'])
            ->where('balance_due', '>', 0)
            ->with(['client'])
            ->get();
    }

    public function getByClient(int $clientId, int $companyId): Collection
    {
        return $this->model->where('client_id', $clientId)
            ->where('company_id', $companyId)
            ->with(['items'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByDateRange(int $companyId, string $startDate, string $endDate): Collection
    {
        return $this->model->where('company_id', $companyId)
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->with(['client', 'items'])
            ->orderBy('invoice_date')
            ->get();
    }

    public function getTotalByStatus(int $companyId, string $status): float
    {
        return $this->model->where('company_id', $companyId)
            ->where('status', $status)
            ->sum('grand_total');
    }

    public function getMonthlySummary(int $companyId, int $month, int $year): array
    {
        $invoices = $this->model->where('company_id', $companyId)
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->get();

        return [
            'total_invoices' => $invoices->count(),
            'total_amount' => $invoices->sum('grand_total'),
            'total_gst' => $invoices->sum('total_gst_amount'),
            'total_paid' => $invoices->sum('paid_amount'),
            'total_pending' => $invoices->sum('balance_due'),
            'by_status' => [
                'draft' => $invoices->where('status', 'draft')->count(),
                'sent' => $invoices->where('status', 'sent')->count(),
                'paid' => $invoices->where('status', 'paid')->count(),
                'overdue' => $invoices->where('status', 'overdue')->count(),
            ]
        ];
    }
}
