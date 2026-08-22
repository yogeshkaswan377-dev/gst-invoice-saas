<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\DTOs\InvoiceData;
use App\DTOs\InvoiceItemData;
use App\Http\Requests\StoreProformaRequest;
use App\Http\Requests\UpdateProformaRequest;
use App\Http\Controllers\invoice;
use App\Models\Client;
use App\Models\Company;
use App\Services\Invoice\GSTInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use App\Services\AuditService;

class GSTInvoiceController extends Controller
{
    public function __construct(
        private GSTInvoiceService $gstInvoiceService
    ) {}

    public function index(Request $request)
    {
        $companyId = Auth::user()->current_company_id;
        $filters = $request->only(['status', 'date_from', 'date_to', 'search', 'client_id']);
        $invoices = $this->gstInvoiceService->listGSTInvoices($companyId, $filters);
        return view('gst-invoices.index', compact('invoices', 'filters'));
    }

    public function create()
    {
        Gate::authorize('create', Invoice::class);

        $companyId = Auth::user()->current_company_id;
        $company = Company::find($companyId);
        $clients = Client::where('company_id', $companyId)->where('status', 'active')->get();
        // No $products
        return view('gst-invoices.create', compact('company', 'clients'));
    }

    public function store(StoreProformaRequest $request)
    {
        Gate::authorize('create', Invoice::class);

        $companyId = Auth::user()->current_company_id;

        // Handle manual client creation
        if ($request->client_mode === 'manual') {
            $client = Client::create([
                'company_id' => $companyId,
                'client_type' => $request->manual_client_gstin ? 'business' : 'individual',
                'name' => $request->manual_client_name,
                'company_name' => $request->manual_client_company,
                'gstin' => $request->manual_client_gstin,
                'email' => $request->manual_client_email,
                'phone' => $request->manual_client_phone,
                'address_line_1' => $request->manual_client_address,
                'state_code' => $request->manual_client_state_code,
                'state_name' => $request->manual_client_state_name ?? '',
                'pincode' => $request->manual_client_pincode,
                'state' => $request->manual_client_state_name ?? '',
                'country' => 'India',
                'status' => 'active',
            ]);

            $clientId = $client->id;
        } else {
            $clientId = (int) $request->client_id;
        }

        $items = collect($request->items)->map(function ($item) {
            return new InvoiceItemData(
                name: $item['name'],
                quantity: (int) $item['quantity'],
                unit_price: (float) $item['unit_price'],
                description: $item['description'] ?? null,
                hsn_sac_code: $item['hsn_sac_code'] ?? null,
                gst_rate: (float) ($item['gst_rate'] ?? 18.00),
                taxable_amount: (float) ($item['unit_price'] * $item['quantity']),
                productId: isset($item['product_id']) ? (int)$item['product_id'] : null,   // NEW
            );
        })->toArray();

        $invoiceData = new InvoiceData(
            company_id: $companyId,
            client_id: $clientId,
            created_by: Auth::id(),
            invoice_type: 'gst_invoice',
            gst_mode: $request->gst_mode ?? 'exclusive',
            invoice_date: $request->invoice_date,
            due_date: $request->due_date,
            reference_number: $request->reference_number,
            discount_type: $request->discount_type,
            discount_amount: (float) ($request->discount_amount ?? 0),
            shipping_charges: (float) ($request->shipping_charges ?? 0),
            commission: (float) ($request->commission ?? 0),
            reverse_charge: $request->has('reverse_charge'),
            notes: $request->notes,
            terms_and_conditions: $request->terms_and_conditions,
            payment_terms: $request->payment_terms ?? 'Net 15',
            show_hsn_sac: $request->has('show_hsn_sac'),
            items: $items,
            status: $request->status ?? 'draft',
        );

        $invoice = $this->gstInvoiceService->createGSTInvoice($invoiceData);
        AuditService::log('created', Invoice::class, $invoice->id, 'GST Invoice created');
        return redirect()->route('gst-invoices.show', $invoice->id)
            ->with('success', 'GST Invoice created!');
    }

    public function show(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->gstInvoiceService->getInvoice($id, $companyId);

        if (!$invoice || $invoice->invoice_type !== 'gst_invoice') {
            abort(404);
        }

        Gate::authorize('view', $invoice);

        return view('gst-invoices.show', compact('invoice'));
    }

    public function edit(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->gstInvoiceService->getInvoice($id, $companyId);

        if (!$invoice || !$invoice->isEditable()) {
            abort(404);
        }

        $company = Company::find($companyId);
        $clients = Client::where('company_id', $companyId)->where('status', 'active')->get();

        Gate::authorize('update', $invoice);

        return view('gst-invoices.edit', compact('invoice', 'company', 'clients'));
    }

    public function update(UpdateProformaRequest $request, int $id)
    {
        Gate::authorize('update', $this->gstInvoiceService->getInvoice($id, $companyId));

        $companyId = Auth::user()->current_company_id;

        $items = collect($request->items)->map(function ($item) {
            return new InvoiceItemData(
                name: $item['name'],
                quantity: (int) $item['quantity'],
                unit_price: (float) $item['unit_price'],
                description: $item['description'] ?? null,
                hsn_sac_code: $item['hsn_sac_code'] ?? null,
                gst_rate: (float) ($item['gst_rate'] ?? 18.00),
                taxable_amount: (float) ($item['unit_price'] * $item['quantity']),
                productId: isset($item['product_id']) ? (int)$item['product_id'] : null,   // NEW
            );
        })->toArray();

        $invoiceData = new InvoiceData(
            company_id: $companyId,
            client_id: (int) $request->client_id,
            created_by: Auth::id(),
            invoice_type: 'gst_invoice',
            gst_mode: $request->gst_mode ?? 'exclusive',
            invoice_date: $request->invoice_date,
            due_date: $request->due_date,
            reference_number: $request->reference_number,
            discount_type: $request->discount_type,
            discount_amount: (float) ($request->discount_amount ?? 0),
            shipping_charges: (float) ($request->shipping_charges ?? 0),
            commission: (float) ($request->commission ?? 0),
            reverse_charge: $request->has('reverse_charge'),
            notes: $request->notes,
            terms_and_conditions: $request->terms_and_conditions,
            payment_terms: $request->payment_terms ?? 'Net 15',
            show_hsn_sac: $request->has('show_hsn_sac'),
            items: $items,
            updated_by: Auth::id(),
        );

        $this->gstInvoiceService->updateGSTInvoice($id, $invoiceData);
        AuditService::log('updated', Invoice::class, $id, 'GST Invoice updated');
        return redirect()->route('gst-invoices.show', $id)
            ->with('success', 'GST Invoice updated!');
    }

    public function destroy(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->gstInvoiceService->getInvoice($id, $companyId);
        if (!$invoice || !$invoice->isDeletable()) {
            return back()->with('error', 'Cannot delete this invoice.');
        }
        Gate::authorize('delete', $invoice);

        AuditService::log('deleted', Invoice::class, $id, 'GST Invoice deleted');
        $this->gstInvoiceService->deleteGSTInvoice($id);   // now handles stock

        return redirect()->route('gst-invoices.index')
            ->with('success', 'GST Invoice deleted.');
    }

    public function pdf(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->gstInvoiceService->getInvoice($id, $companyId);

        if (!$invoice) abort(404);

        $pdf = Pdf::loadView('gst-invoices.pdf', compact('invoice'));
        return $pdf->download('GST-Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function stream(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->gstInvoiceService->getInvoice($id, $companyId);
        if (!$invoice) abort(404);
        $pdf = Pdf::loadView('gst-invoices.pdf', compact('invoice'));
        return $pdf->stream('GST-Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function bulkPdf(Request $request)
    {
        $ids = explode(',', $request->ids);
        $companyId = Auth::user()->current_company_id;

        $zip = new ZipArchive();
        $zipName = 'invoices-' . now()->format('Ymd') . '.zip';
        $zipPath = storage_path('app/' . $zipName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($ids as $id) {
                $invoice = $this->gstInvoiceService->getInvoice(trim($id), $companyId);
                if ($invoice) {
                    $pdf = Pdf::loadView('gst-invoices.pdf', compact('invoice'));
                    $zip->addFromString($invoice->invoice_number . '.pdf', $pdf->output());
                }
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend();
    }

    public function sendEmail(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->invoiceService->getInvoice($id, $companyId);

        if (!$invoice || !$invoice->client->email) {
            return back()->with('error', 'Client email not found.');
        }

        Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));
        AuditService::log('sent', Invoice::class, $invoice->id, 'Invoice email sent');
        return back()->with('success', 'Invoice emailed successfully!');
    }
}
