<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Services\AuditService;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query()->withCount('users');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('gstin', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%");
            });
        }

        $companies = $query->paginate(20)->withQueryString();

        return view('super-admin.companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->loadCount(['users', 'invoices', 'clients']);
        $company->loadSum('invoices', 'grand_total');
        return view('super-admin.companies.show', compact('company'));
    }

    public function approve(Company $company)
    {
        AuditService::log('approved', get_class($company), $company->id, 'Company approved');
        $company->update(['is_active' => 1]);
        return back()->with('success', 'Company approved!');
    }

    public function suspend(Company $company)
    {
        AuditService::log('suspended', get_class($company), $company->id, 'Company suspended');
        $company->update(['is_active' => 0]);
        return back()->with('success', 'Company suspended!');
    }

    public function users(Company $company)
    {
        $users = $company->users()->paginate(20);
        return view('super-admin.companies.users', compact('company', 'users'));
    }

    public function invoices(Company $company)
    {
        $invoices = $company->invoices()->with('client')->latest()->paginate(20);
        return view('super-admin.companies.invoices', compact('company', 'invoices'));
    }
}
