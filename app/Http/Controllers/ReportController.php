<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Outstanding invoices report (dynamic)
     */
    public function outstanding()
    {
        $companyId = Auth::user()->current_company_id;
        $invoices = Invoice::where('company_id', $companyId)
            ->whereIn('status', ['sent', 'viewed', 'accepted', 'partially_paid', 'overdue'])
            ->where('balance_due', '>', 0)
            ->with('client')
            ->orderBy('due_date')
            ->get();

        $totalOutstanding = $invoices->sum('balance_due');

        $overdueInvoices = $invoices->filter(fn($inv) => $inv->due_date && $inv->due_date->isPast());
        $totalOverdue = $overdueInvoices->sum('balance_due');
        $avgDaysOverdue = $overdueInvoices->count() > 0
            ? round($overdueInvoices->avg(fn($inv) => now()->diffInDays($inv->due_date)))
            : 0;

        return view('reports.outstanding', compact(
            'invoices',
            'totalOutstanding',
            'totalOverdue',
            'avgDaysOverdue'
        ));
    }

    /**
     * GSTR-1 report (only GST invoices)
     */
    public function gstr1(Request $request)
    {
        $companyId = session('current_company_id') ?? auth()->user()->current_company_id;
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to = $request->to ?? now()->format('Y-m-d');

        // Fetch only GST invoices
        $invoices = Invoice::where('company_id', $companyId)
            ->where('invoice_type', 'gst_invoice')          // 👈 exclude proformas
            ->whereBetween('invoice_date', [$from, $to])
            ->whereIn('status', ['paid', 'sent', 'accepted'])
            ->get();

        // Summary
        $summary = [
            'total_invoices' => $invoices->count(),
            'taxable_value' => $invoices->sum('taxable_amount'),
            'total_gst' => $invoices->sum('total_gst_amount'),
            'hsn_count' => $invoices->pluck('items')->flatten()
                ->pluck('hsn_sac_code')
                ->unique()
                ->count(),
        ];

        // HSN Summary
        $hsnSummary = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.company_id', $companyId)
            ->where('invoices.invoice_type', 'gst_invoice')  // 👈 only GST invoices
            ->whereBetween('invoices.invoice_date', [$from, $to])
            ->select(
                'invoice_items.hsn_sac_code',
                DB::raw('SUM(invoice_items.quantity) as total_qty'),
                DB::raw('SUM(invoice_items.taxable_amount) as taxable_value'),
                DB::raw('SUM(invoice_items.igst_amount) as igst'),
                DB::raw('SUM(invoice_items.cgst_amount) as cgst'),
                DB::raw('SUM(invoice_items.sgst_amount) as sgst'),
                DB::raw('SUM(invoice_items.line_total_with_gst) as total')   // 👈 correct column
            )
            ->groupBy('invoice_items.hsn_sac_code')
            ->get();

        return view('reports.gstr1', compact('summary', 'hsnSummary', 'from', 'to'));
    }

    /**
     * Export GSTR-1 as CSV
     */
    public function exportCsv(Request $request)
    {
        $companyId = session('current_company_id') ?? auth()->user()->current_company_id;
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to = $request->to ?? now()->format('Y-m-d');
        $type = $request->type ?? 'gst_invoice';

        $invoices = Invoice::where('company_id', $companyId)
            ->when($type !== 'all', fn($q) => $q->where('invoice_type', $type))
            ->whereBetween('invoice_date', [$from, $to])
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="gstr1-report.csv"',
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Invoice #', 'Client', 'Date', 'Taxable Value', 'IGST', 'CGST', 'SGST', 'Total']);

            foreach ($invoices as $inv) {
                fputcsv($file, [
                    $inv->invoice_number,
                    $inv->client->name ?? 'N/A',
                    $inv->invoice_date ? $inv->invoice_date->format('d/m/Y') : '', // safe date
                    $inv->taxable_amount,
                    $inv->igst_amount,
                    $inv->cgst_amount,
                    $inv->sgst_amount,
                    $inv->grand_total,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export GSTR-1 as Excel
     */
    public function exportExcel(Request $request)
    {
        $companyId = Auth::user()->current_company_id;
        $from = $request->from;
        $to = $request->to;
        $type = $request->type ?? 'gst_invoice';

        $invoices = Invoice::where('company_id', $companyId)
            ->when($type !== 'all', fn($q) => $q->where('invoice_type', $type))
            ->when($from && $to, fn($q) => $q->whereBetween('invoice_date', [$from, $to]))
            ->with('client')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Invoice #');
        $sheet->setCellValue('B1', 'Client');
        $sheet->setCellValue('C1', 'Date');
        $sheet->setCellValue('D1', 'Taxable Value');
        $sheet->setCellValue('E1', 'IGST');
        $sheet->setCellValue('F1', 'CGST');
        $sheet->setCellValue('G1', 'SGST');
        $sheet->setCellValue('H1', 'Total');

        $row = 2;
        foreach ($invoices as $invoice) {
            $sheet->setCellValue('A' . $row, $invoice->invoice_number);
            $sheet->setCellValue('B' . $row, $invoice->client->name ?? '');
            $sheet->setCellValue('C' . $row, $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '');
            $sheet->setCellValue('D' . $row, $invoice->taxable_amount);
            $sheet->setCellValue('E' . $row, $invoice->igst_amount);
            $sheet->setCellValue('F' . $row, $invoice->cgst_amount);
            $sheet->setCellValue('G' . $row, $invoice->sgst_amount);
            $sheet->setCellValue('H' . $row, $invoice->grand_total);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'gstr1-report-' . now()->format('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    /**
     * Stub for future PDF export – not used
     */
    public function exportGstr1(Request $request)
    {
        // Not implemented; route not used
        abort(404);
    }
}
