<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class ProformaController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('company', 'client')
            ->where('invoice_type', 'proforma');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('client', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $proformas = $query->latest()->paginate(20)->withQueryString();

        return view('super-admin.proformas.index', compact('proformas'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('company', 'client', 'items');
        return view('super-admin.proformas.show', ['proforma' => $invoice]);
    }
}
