<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\DTOs\CompanyData;
use App\Services\Company\CompanySettingsService;
use App\Http\Requests\UpdateCompanySettingsRequest;
use App\Http\Requests\UpdateCompanyGstRequest;
use App\Http\Requests\UpdateCompanyBankRequest;
use App\Http\Requests\UpdateCompanyPreferencesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    protected CompanyRepositoryInterface $companyRepository;
    protected CompanySettingsService $companySettingsService;

    public function __construct(
        CompanyRepositoryInterface $companyRepository,
        CompanySettingsService $companySettingsService
    ) {
        $this->companyRepository = $companyRepository;
        $this->companySettingsService = $companySettingsService;
    }

    public function create()
    {
        return view('company.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $user = Auth::user();

        $companyData = CompanyData::fromRequest($request);

        $company = DB::transaction(function () use ($companyData, $user) {
            // Create company
            $company = $this->companyRepository->create($companyData->toArray());

            // Update user with company_id
            $user->company_id = $company->id;
            $user->save();

            // Assign role using Spatie Permission - MUST specify guard_name
            $user->assignRole('owner'); // This works because guard_name defaults to 'web' in config

            return $company;
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Company created successfully.');
    }

    public function switchCompany()
    {
        $user = Auth::user();
        $companies = $user->company()->get();

        return view('company.switch', compact('companies'));
    }

    public function setCurrentCompany(Request $request, $companyId)
    {
        $user = Auth::user();
        $company = $this->companyRepository->findOrFail($companyId);

        // Verify user belongs to this company
        if ($user->company_id != $company->id && !$user->hasRole('owner', $company->id)) {
            abort(403);
        }

        $user->current_company_id = $company->id;
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', 'Company created successfully! Please complete your profile.');
    }

    public function settings()
    {
        $company = Auth::user()->currentCompany;
        return view('company.settings', compact('company'));
    }

    public function updateSettings(UpdateCompanySettingsRequest $request)
    {
        $company = $request->user()->currentCompany;
        $data = $request->validated();

        $this->companyRepository->updateSettings($company->id, $data);

        if ($request->hasFile('logo')) {
            $this->companySettingsService->updateLogo($company->id, $request->file('logo'));
        }
        if ($request->hasFile('signature')) {
            $this->companySettingsService->updateSignature($company->id, $request->file('signature'));
        }

        return redirect()->route('company.settings')
            ->with('success', 'Company settings updated.');
    }

    public function updateGst(UpdateCompanyGstRequest $request)
    {
        $company = $request->user()->currentCompany;

        $data = $request->only(['gstin', 'pan']);
        $data['gst_mode_default'] = $request->gst_mode;   // map to actual column name

        // Safely get current GST settings (could be string or already decoded)
        $currentSettings = $company->gst_settings;
        if (is_string($currentSettings)) {
            $currentSettings = json_decode($currentSettings, true) ?? [];
        } elseif (is_array($currentSettings)) {
            // already an array (if model cast exists)
        } else {
            $currentSettings = [];
        }

        $currentSettings['default_rate'] = (int) $request->default_gst_rate;
        $currentSettings['default_mode'] = $request->gst_mode;

        // If model casts gst_settings to array, we can assign array directly; 
        // if not, encode to JSON string.
        if ($company->hasCast('gst_settings', 'array')) {
            $data['gst_settings'] = $currentSettings;
        } else {
            $data['gst_settings'] = json_encode($currentSettings);
        }

        $this->companyRepository->updateSettings($company->id, $data);

        return redirect()->route('company.settings')->with('success', 'GST settings updated.');
    }

    public function updatePreferences(UpdateCompanyPreferencesRequest $request)
    {
        $company = $request->user()->currentCompany;

        $data = [
            'invoice_prefix'        => $request->invoice_prefix,
            'quote_prefix'          => $request->quote_prefix,
            'default_payment_terms' => (int) $request->payment_terms,   // cast to int
        ];

        $this->companyRepository->updateSettings($company->id, $data);

        return redirect()->route('company.settings')->with('success', 'Invoice preferences updated.');
    }
}
