<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('company');

        // Search by name, email, or company name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Role filter (through Spatie role_user pivot)
        if ($request->filled('role') && $request->role !== '') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('super-admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('super-admin.users.show', compact('user'));
    }

    public function suspend(User $user)
    {
        $user->update(['is_active' => false]);

        return back()->with('success', 'User suspended successfully.');
    }

    public function approve(User $user)
    {
        $user->update(['is_active' => true]);

        return back()->with('success', 'User approved successfully.');
    }
}
