@extends('layouts.app')

@section('title', 'My Profile - GST Billing Pro')
@section('meta_description', 'Update your profile information, password and account settings.')

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

@if (session('status') === 'profile-updated')
<div class="alert alert-success">
    Profile updated successfully!
</div>
@endif

<div class="mb-4">
    <h2 style="font-size:18px; font-weight:700; margin:0;">My Profile</h2>
    <p style="color:#64748b; font-size:12px; margin:4px 0 0;">Manage your account settings</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Profile Information --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-user-circle me-2 text-primary"></i>Profile Information</h5>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf @method('PATCH')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Name *</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Email *</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Designation</label>
                            <input type="text" name="designation" value="{{ old('designation', auth()->user()->designation) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Timezone</label>
                            <select name="timezone" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                                <option value="Asia/Kolkata" {{ (auth()->user()->timezone ?? 'Asia/Kolkata') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                                <option value="Asia/Dubai">Asia/Dubai</option>
                                <option value="America/New_York">America/New York</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:10px 24px; font-weight:600;">
                            <i class="fas fa-save me-2"></i> Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Update Password --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-lock me-2 text-warning"></i>Update Password</h5>

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">Current Password *</label>
                            <input type="password" name="current_password" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">New Password *</label>
                            <input type="password" name="password" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">Confirm Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:10px 24px; font-weight:600;">
                            <i class="fas fa-key me-2"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Profile Photo --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 text-center">
                <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b82f6&color=fff&size=100' }}"
                    style="width:80px; height:80px; border-radius:20px; object-fit:cover; margin-bottom:16px;">
                <h6 class="fw-bold">{{ auth()->user()->name }}</h6>
                <p class="text-muted" style="font-size:12px;">{{ auth()->user()->email }}</p>

                {{-- Use the form attribute to associate inputs outside the main form --}}
                <input type="file" name="profile_photo" form="profileForm" class="form-control" accept="image/*" style="border-radius:10px; padding:8px;">
                <button type="submit" form="profileForm" class="btn btn-sm mt-2" style="background:#f1f5f9; border-radius:10px; font-weight:500;">
                    <i class="fas fa-camera me-1"></i> Upload Photo
                </button>
            </div>
        </div>

        {{-- Delete Account --}}
        <div class="card border-danger rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Delete Account</h5>
                <p style="font-size:12px; color:#64748b;">Permanently delete your account and all associated data.</p>
                <button class="btn btn-outline-danger w-100" style="border-radius:10px;" onclick="return confirm('Are you sure? This cannot be undone.')">
                    <i class="fas fa-trash me-2"></i> Delete My Account
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 