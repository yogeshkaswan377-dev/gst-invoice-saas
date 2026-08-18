<?php

namespace App\Http\Controllers;

use App\Models\CompanyInvite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class StaffController extends Controller
{
    public function inviteForm()
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Only company owners can manage staff invitations.');
        }

        $company = auth()->user()->currentCompany;

        if (!$company) {
            return redirect()->route('company.select')
                ->withErrors(['company' => 'Please select a company first.']);
        }

        $teamMembers = User::where('company_id', $company->id)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'staff']);
            })->get();

        $pendingInvitations = CompanyInvite::where('company_id', $company->id)
            ->whereNull('accepted_at')
            ->get();

        return view('staff.invite', compact('teamMembers', 'pendingInvitations'));
    }

    public function sendInvite(Request $request)
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Only company owners can manage staff invitations.');
        }

        $company = auth()->user()->currentCompany;

        if (!$company) {
            return back()->withErrors(['company' => 'No company selected.']);
        }

        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,staff',
        ]);

        $token = Str::random(64);

        CompanyInvite::create([
            'company_id' => $company->id,
            'invited_by' => auth()->id(),
            'email' => $request->email,
            'role' => $request->role,
            'token' => $token,
        ]);

        $link = route('invite.accept', $token);

        // Uncomment to send email when ready
        // Mail::to($request->email)->send(new StaffInviteMail($link));

        return back()->with('success', "Invite sent! Link: <a href='{$link}'>{$link}</a>");
    }

    public function acceptInvite($token)
    {
        $invite = CompanyInvite::where('token', $token)->whereNull('accepted_at')->firstOrFail();
        return view('auth.invite-register', compact('invite'));
    }

    public function registerFromInvite(Request $request, $token)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $invite = CompanyInvite::where('token', $token)->whereNull('accepted_at')->firstOrFail();

        $user = User::create([
            'name' => $request->name,
            'email' => $invite->email,
            'password' => Hash::make($request->password),
            'company_id' => $invite->company_id,
            'current_company_id' => $invite->company_id,
        ]);

        $user->assignRole($invite->role, $invite->company_id);
        $invite->update(['accepted_at' => now()]);

        auth()->login($user);
        session(['current_company_id' => $invite->company_id]);

        return redirect()->route('dashboard')->with('success', 'Welcome to the team!');
    }
}
