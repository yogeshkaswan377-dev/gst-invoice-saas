@extends('layouts.app')

@section('title', 'Invite Staff - GST Billing Pro')
@section('meta_description', 'Invite team members to your company workspace.')

@section('content')
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (session('success'))
<div class="alert alert-success">
    {!! session('success') !!}
</div>
@endif
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 style="font-size:18px; font-weight:700; margin:0;">Invite Staff</h2>
        <p style="color:#64748b; font-size:12px; margin:4px 0 0;">Send invitations to your team members</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        {{-- Invite Form --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-paper-plane me-2 text-primary"></i>Send Invitation</h5>

                <form action="{{ route('staff.invite.send') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Email *</label>
                            <input type="email" name="email" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="colleague@company.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Role *</label>
                            <select name="role" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Message (Optional)</label>
                            <textarea name="message" rows="3" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="Add a personal message to the invitation..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:10px 24px; font-weight:600;">
                            <i class="fas fa-paper-plane me-2"></i> Send Invitation
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Pending Invitations --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0"><i class="fas fa-clock me-2 text-warning"></i>Pending Invitations</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.05em; background:#f8fafc;">
                            <tr>
                                <th class="ps-4">Email</th>
                                <th>Role</th>
                                <th>Sent</th>
                                <th>Expires</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingInvitations ?? [] as $invite)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $invite->email }}</td>
                                <td><span class="badge rounded-pill" style="background:#ede9fe; color:#7c3aed;">{{ ucfirst($invite->role) }}</span></td>
                                <td class="text-muted">{{ $invite->created_at?->format('d M Y') ?? 'N/A' }}</td>
                                <td class="text-muted">{{ $invite->expires_at?->format('d M Y') ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge rounded-pill" style="background:#fef3c7; color:#92400e;">Pending</span>
                                </td>
                                {{--
                                <td>
                                    <form action="{{ route('staff.invite.resend', $invite->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm" style="background:#dbeafe; color:#1d4ed8; border-radius:8px; font-size:11px;">
                                    <i class="fas fa-redo me-1"></i> Resend
                                </button>
                                </form>
                                <form action="{{ route('staff.invite.cancel', $invite->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm" style="background:#fee2e2; color:#991b1b; border-radius:8px; font-size:11px;">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </button>
                                </form>
                                </td>
                                --}}
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No pending invitations</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        {{-- Team Members --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-users me-2 text-success"></i>Team Members</h5>

                @forelse($teamMembers ?? [] as $member)
                <div class="d-flex align-items-center gap-3 mb-3 pb-3" style="border-bottom:1px solid #f1f5f9;">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=3b82f6&color=fff&size=40"
                        style="width:40px; height:40px; border-radius:10px;">
                    <div class="flex-grow-1">
                        <h6 class="fw-semibold mb-0">{{ $member->name }}</h6>
                        <small class="text-muted">{{ $member->email }}</small>
                    </div>
                    <span class="badge rounded-pill" style="background:#ede9fe; color:#7c3aed; font-size:11px;">{{ ucfirst($member->pivot->role ?? 'Staff') }}</span>
                </div>
                @empty
                <p class="text-muted text-center py-3" style="font-size:13px;">No team members yet</p>
                @endforelse
            </div>
        </div>

        {{-- Role Info --}}
        {{-- Role Info --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-info"></i>Roles & Permissions</h5>

                <div class="mb-3">
                    <span class="badge d-block mb-1" style="background:#dbeafe; color:#1d4ed8; font-size:12px;">Admin</span>
                    <small class="text-muted">Full access — manage invoices, clients, products, and staff</small>
                </div>
                <div class="mb-3">
                    <span class="badge d-block mb-1" style="background:#d1fae5; color:#065f46; font-size:12px;">Manager</span>
                    <small class="text-muted">Create & edit invoices, clients, and view reports</small>
                </div>
                <div>
                    <span class="badge d-block mb-1" style="background:#fef3c7; color:#92400e; font-size:12px;">Staff</span>
                    <small class="text-muted">Create invoices and manage assigned clients</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection