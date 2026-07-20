<?php

namespace App\Services\NumberGenerator;

use App\Models\Invoice;
use App\Models\InvoiceSequence;
use Illuminate\Support\Facades\DB;

class InvoiceNumberGenerator
{
    public function generateInvoiceNumber(int $companyId): string
    {
        return $this->generateNumber($companyId, 'invoice', 'INV');
    }

    public function generateProformaNumber(int $companyId): string
    {
        return $this->generateNumber($companyId, 'proforma', 'PF');
    }

    public function generateQuoteNumber(int $companyId): string
    {
        return $this->generateNumber($companyId, 'quote', 'Q');
    }

    protected function generateNumber(int $companyId, string $type, string $prefix): string
    {
        return DB::transaction(function () use ($companyId, $type, $prefix) {
            $year = date('Y');

            // Get or create sequence record with lock
            $sequence = InvoiceSequence::where([
                'company_id' => $companyId,
                'type' => $type,
                'year' => $year,
            ])->lockForUpdate()->first();

            if (!$sequence) {
                // Find the highest existing invoice number for this company/type/year
                $lastNumber = $this->getLastUsedNumber($companyId, $type, $prefix, $year);

                $sequence = InvoiceSequence::create([
                    'company_id' => $companyId,
                    'type' => $type,
                    'prefix' => $prefix,
                    'year' => $year,
                    'last_sequence' => $lastNumber,
                ]);
            }

            // Increment sequence
            $sequence->increment('last_sequence');
            $sequence->refresh();

            // Double-check: if this number already exists, increment again
            $attempts = 0;
            $invoiceNumber = sprintf('%s-%s-%05d', $prefix, $year, $sequence->last_sequence);

            while ($this->numberExists($companyId, $type, $invoiceNumber) && $attempts < 100) {
                $sequence->increment('last_sequence');
                $sequence->refresh();
                $invoiceNumber = sprintf('%s-%s-%05d', $prefix, $year, $sequence->last_sequence);
                $attempts++;
            }

            return $invoiceNumber;
        });
    }

    /**
     * Get the last used sequence number from actual invoices
     */
    protected function getLastUsedNumber(int $companyId, string $type, string $prefix, string $year): int
    {
        $typeMap = [
            'invoice' => 'gst_invoice',
            'proforma' => 'proforma',
            'quote' => 'quote',
        ];

        $invoiceType = $typeMap[$type] ?? 'gst_invoice';

        $lastInvoice = Invoice::where('company_id', $companyId)
            ->where('invoice_type', $invoiceType)
            ->where('invoice_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract sequence number from format: PREFIX-YYYY-XXXXX
            $parts = explode('-', $lastInvoice->invoice_number);
            return (int) end($parts);
        }

        return 0;
    }

    /**
     * Check if invoice number already exists
     */
    protected function numberExists(int $companyId, string $type, string $number): bool
    {
        return Invoice::where('company_id', $companyId)
            ->where('invoice_number', $number)
            ->exists();
    }

    public function previewNumber(int $companyId, string $type): string
    {
        $sequence = InvoiceSequence::where([
            'company_id' => $companyId,
            'type' => $type,
            'year' => date('Y'),
        ])->first();

        $prefix = $this->getPrefixForType($type);

        if ($sequence) {
            $nextSequence = $sequence->last_sequence + 1;
        } else {
            $nextSequence = $this->getLastUsedNumber($companyId, $type, $prefix, date('Y')) + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, date('Y'), $nextSequence);
    }

    protected function getPrefixForType(string $type): string
    {
        return match ($type) {
            'invoice' => 'INV',
            'proforma' => 'PF',
            'quote' => 'Q',
            default => 'INV',
        };
    }
}