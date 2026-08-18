<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;


class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $clients = Client::where('company_id', Auth::user()->company_id)->paginate(15);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        Gate::authorize('create', Client::class);

        $states = config('indian_states.states');

        return view('Clients.create', compact('states'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Client::class);

        $validated = $request->validate([
            'client_type' => 'required|in:individual,business,export',

            'name' => 'required|string|max:255',

            'company_name' => 'required_if:client_type,business|nullable|string|max:255',

            'email' => 'nullable|email|max:255',

            'phone' => 'nullable|string|max:20',

            'gstin' => [
                'required_if:client_type,business',
                'nullable',
                'string',
                'size:15',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                'unique:clients,gstin',
            ],

            'state_code' => 'required|string|size:2',

            'state_name' => 'required|string|max:100',

            'address_line_1' => 'required|string|max:255',

            'city' => 'required|string|max:100',

            'pincode' => 'required|string|max:10',

            'country' => 'required|string|max:100',

            'credit_limit' => 'nullable|numeric|min:0',

            'is_active' => 'nullable|boolean',
        ]);

        // Calculate place of supply
        $companyState = Auth::user()->company->state_code;
        $validated['place_of_supply'] = ($validated['state_code'] === $companyState)
            ? 'intra_state'
            : 'inter_state';

        // Add state and status fields
        $validated['state'] = $validated['state_name'];
        $validated['status'] =
            !empty($validated['is_active'])
            ? 'active'
            : 'inactive';
        $validated['company_id'] = session('current_company_id');

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        Gate::authorize('update', $client);

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        Gate::authorize('update', $client);

        $validated = $request->validate([
            'client_type' => 'required|in:individual,business,export',

            'name' => 'required|string|max:255',

            'company_name' => 'required_if:client_type,business|nullable|string|max:255',

            'email' => 'nullable|email|max:255',

            'phone' => 'nullable|string|max:20',

            'gstin' => [
                'required_if:client_type,business',
                'nullable',
                'string',
                'size:15',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                'unique:clients,gstin,' . $client->id,
            ],

            'state_code' => 'required|string|size:2',

            'state_name' => 'required|string|max:100',

            'address_line_1' => 'required|string|max:255',

            'city' => 'required|string|max:100',

            'pincode' => 'required|string|max:10',

            'country' => 'required|string|max:100',

            'credit_limit' => 'nullable|numeric|min:0',

            'is_active' => 'nullable|boolean',
        ]);

        // Recalculate place of supply
        $companyState = Auth::user()->company->state_code;
        $validated['place_of_supply'] = ($validated['state_code'] === $companyState)
            ? 'intra_state'
            : 'inter_state';

        $validated['state'] = $validated['state_name'];
        $validated['status'] =
            !empty($validated['is_active'])
            ? 'active'
            : 'inactive';


        $client->update($validated);
        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        Gate::authorize('delete', $client);

        Log::warning('Client deleted', [
            'user_id' => Auth::id(),
            'client_name' => $client->name,
            'company_id' => $client->company_id,
        ]);

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $clientsQuery = Client::where('company_id', Auth::user()->company_id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('company_name', 'LIKE', "%{$query}%")
                    ->orWhere('gstin', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            });

        // AJAX requests ke liye JSON return (used by Alpine.js component)
        if ($request->ajax() || $request->wantsJson()) {
            $clients = $clientsQuery->get();
            return response()->json([
                'success' => true,
                'data' => $clients
            ]);
        }

        // Normal form submission ke liye paginated view
        $clients = $clientsQuery->paginate(15);
        return view('clients.index', compact('clients'));
    }

    public function filterByState(Request $request)
    {
        $state = $request->get('state');
        $clients = Client::where('company_id', Auth::user()->company_id)
            ->where('state_name', $state)
            ->paginate(15);

        return view('clients.index', compact('clients'));
    }

    public function filterByStatus(Request $request)
    {
        $status = $request->get('status');
        $clients = Client::where('company_id', Auth::user()->company_id)
            ->where('status', $status)
            ->paginate(15);

        return view('clients.index', compact('clients'));
    }
}
