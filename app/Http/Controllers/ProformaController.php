<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\DTOs\InvoiceData;
use App\DTOs\InvoiceItemData;
use App\Http\Requests\StoreProformaRequest;
use App\Http\Requests\UpdateProformaRequest;
use App\Models\Client;
use App\Models\Company;
use App\Services\Invoice\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProformaController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {}

    /**
     * Display listing of proforma invoices
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->current_company_id;

        $filters = $request->only(['status', 'date_from', 'date_to', 'search', 'client_id']);
        $proformas = $this->invoiceService->listProformas($companyId, $filters);

        return view('proformas.index', compact('proformas', 'filters'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        Gate::authorize('create', Invoice::class);

        $company = Company::find(Auth::user()->current_company_id);
        $clients = Client::where('company_id', $company->id)
            ->where('status', 'active')
            ->get();

        return view('proformas.create', compact('company', 'clients'));
    }

    /**
     * Store new proforma
     */
    public function store(StoreProformaRequest $request)
    {
        Gate::authorize('create', Invoice::class);

        $companyId = Auth::user()->current_company_id;

        $items = collect($request->items)->map(function ($item) {
            return new InvoiceItemData(
                name: $item['name'],
                quantity: (int) $item['quantity'],
                unit_price: (float) $item['unit_price'],
                description: $item['description'] ?? null,
                gst_rate: (float) ($item['gst_rate'] ?? 18.00),
                discount_type: $item['discount_type'] ?? null,
                discount_value: (float) ($item['discount_value'] ?? 0),
                taxable_amount: (float) ($item['unit_price'] * $item['quantity']),
            );
        })->toArray();

        $invoiceData = new InvoiceData(
            company_id: $companyId,
            client_id: (int) $request->client_id,
            created_by: Auth::id(),
            invoice_type: 'proforma',
            gst_mode: 'exclusive',
            gst_rate: 18.00,
            invoice_date: $request->invoice_date,
            due_date: $request->due_date,
            reference_number: $request->reference_number,
            discount_type: $request->discount_type,
            discount_amount: (float) ($request->discount_amount ?? 0),
            shipping_charges: (float) ($request->shipping_charges ?? 0),
            commission: (float) ($request->commission ?? 0),
            notes: $request->notes,
            terms_and_conditions: $request->terms_and_conditions,
            payment_terms: $request->payment_terms ?? 'Net 15',
            logistics_details: $request->logistics_details,
            estimated_delivery_date: $request->estimated_delivery_date,
            items: $items,
            status: $request->status ?? 'draft',
        );

        $invoice = $this->invoiceService->createProforma($invoiceData);

        return redirect()
            ->route('proformas.show', $invoice->id)
            ->with('success', 'Proforma invoice #' . $invoice->invoice_number . ' created successfully!');
    }

    /**
     * Show proforma invoice
     */
    public function show(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->invoiceService->getInvoice($id, $companyId);

        if (!$invoice || $invoice->invoice_type !== 'proforma') {
            abort(404);
        }

        Gate::authorize('view', $invoice);

        return view('proformas.show', ['proforma' => $invoice]);
    }

    /**
     * Edit proforma
     */
    public function edit(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->invoiceService->getInvoice($id, $companyId);

        if (!$invoice || !$invoice->isEditable()) {
            abort(404);
        }

        $company = Company::find($companyId);
        $clients = Client::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        Gate::authorize('update', $invoice);

        return view('proformas.edit', compact('invoice', 'company', 'clients'));
    }

    /**
     * Update proforma
     */
    public function update(UpdateProformaRequest $request, int $id)
    {
        $invoice = $this->invoiceService->getInvoice($id, $companyId);
        Gate::authorize('update', $invoice);

        $companyId = Auth::user()->current_company_id;

        $items = collect($request->items)->map(function ($item) {
            return new InvoiceItemData(
                name: $item['name'],
                quantity: (int) $item['quantity'],
                unit_price: (float) $item['unit_price'],
                description: $item['description'] ?? null,
                gst_rate: (float) ($item['gst_rate'] ?? 18.00),
                discount_type: $item['discount_type'] ?? null,
                discount_value: (float) ($item['discount_value'] ?? 0),
                taxable_amount: (float) ($item['unit_price'] * $item['quantity']),
            );
        })->toArray();

        $invoiceData = new InvoiceData(
            company_id: $companyId,
            client_id: (int) $request->client_id,
            created_by: Auth::id(),
            invoice_type: 'proforma',
            gst_mode: 'exclusive',
            gst_rate: 18.00,
            invoice_date: $request->invoice_date,
            due_date: $request->due_date,
            reference_number: $request->reference_number,
            discount_type: $request->discount_type,
            discount_amount: (float) ($request->discount_amount ?? 0),
            shipping_charges: (float) ($request->shipping_charges ?? 0),
            commission: (float) ($request->commission ?? 0),
            notes: $request->notes,
            terms_and_conditions: $request->terms_and_conditions,
            payment_terms: $request->payment_terms ?? 'Net 15',
            logistics_details: $request->logistics_details,
            estimated_delivery_date: $request->estimated_delivery_date,
            items: $items,
            updated_by: Auth::id(),
        );

        $this->invoiceService->updateProforma($id, $invoiceData);

        return redirect()
            ->route('proformas.show', $id)
            ->with('success', 'Proforma invoice updated successfully!');
    }

    /**
     * Delete proforma
     */
    public function destroy(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->invoiceService->getInvoice($id, $companyId);

        if (!$invoice || !$invoice->isDeletable()) {
            return back()->with('error', 'Cannot delete this invoice.');
        }

        // LOG THE DELETION
        Log::warning('Invoice deleted', [
            'user_id' => Auth::id(),
            'invoice_number' => $invoice->invoice_number,
            'invoice_type' => $invoice->invoice_type,
            'company_id' => $companyId,
        ]);

        $this->invoiceService->deleteProforma($id);

        Gate::authorize('delete', $invoice);

        return redirect()->route('proformas.index')
            ->with('success', 'Proforma invoice deleted.');
    }


    public function convertToGst(int $id)
    {
        return redirect()->route('proformas.show', $id)
            ->with('info', 'GST Invoice feature coming in Phase 4B.');
    }

    public function pdf(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->invoiceService->getInvoice($id, $companyId);

        if (!$invoice) abort(404);

        $pdf = Pdf::loadView('proformas.pdf', compact('invoice'));
        return $pdf->download('Proforma-' . $invoice->invoice_number . '.pdf');
    }

    public function stream(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->invoiceService->getInvoice($id, $companyId);
        if (!$invoice) abort(404);
        $pdf = Pdf::loadView('proformas.pdf', compact('invoice'));
        return $pdf->stream('Proforma-' . $invoice->invoice_number . '.pdf');
    }

    public function sendEmail(int $id)
    {
        $companyId = Auth::user()->current_company_id;
        $invoice = $this->invoiceService->getInvoice($id, $companyId);

        if (!$invoice || !$invoice->client->email) {
            return back()->with('error', 'Client email not found.');
        }

        Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));

        Log::info('Invoice emailed', [
            'invoice_number' => $invoice->invoice_number,
            'sent_to' => $invoice->client->email,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Invoice emailed successfully!');
    }
}
