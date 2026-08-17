<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation - GST Billing Pro</title>
    <meta name="robots" content="noindex, nofollow">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 50%, #eef2ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.1);
        }

        .icon-circle {
            width: 64px;
            height: 64px;
            background: #ede9fe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #7c3aed;
            margin: 0 auto 20px;
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 6px;
        }

        .subtitle {
            color: #64748b;
            font-size: 13px;
            text-align: center;
            margin-bottom: 24px;
        }

        .invite-badge {
            background: linear-gradient(135deg, #ede9fe, #fae8ff);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: center;
        }

        .invite-badge .company {
            font-size: 16px;
            font-weight: 700;
            color: #5b21b6;
        }

        .invite-badge .role {
            font-size: 12px;
            color: #7c3aed;
        }

        .form-control {
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 12px 14px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .btn-join {
            background: linear-gradient(135deg, #6d28d9, #7c3aed);
            color: white;
            border: none;
            padding: 13px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            width: 100%;
        }

        .btn-join:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
        }

        .footer-text {
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <div class="icon-circle"><i class="fas fa-envelope-open-text"></i></div>
        <h1>You're Invited! 🎉</h1>
        <p class="subtitle">Join your team on GST Billing Pro</p>

        <div class="invite-badge">
            <div class="company">
                <i class="fas fa-building me-2"></i> {{ $invite->company->name ?? 'Company Name' }}
            </div>
            <div class="role mt-1">
                <i class="fas fa-user-tag me-1"></i> Role: {{ ucfirst($invite->role ?? 'Staff') }}
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger rounded-3 border-0" style="font-size:13px;">
            @foreach($errors->all() as $error)
            <div><i class="fas fa-exclamation-circle me-2"></i> {{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form action="{{ route('invite.register', $invite->token) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $invite->email ?? '') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Email</label>
                <input type="email" value="{{ $invite->email }}" class="form-control" disabled style="background:#f8fafc;">
                <input type="hidden" name="email" value="{{ $invite->email }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="+91 9876543210">
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13px;">Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Min 8 characters" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:13px;">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" class="form-check-input" required>
                <label class="form-check-label" style="font-size:12px;">I agree to the terms & conditions</label>
            </div>

            <button type="submit" class="btn-join">
                <i class="fas fa-check-circle me-2"></i> Accept Invitation & Join
            </button>
        </form>

        <p class="footer-text">
            Invited by {{ $invite->invitedBy->name ?? 'Admin' }} •
            Expires {{ $invite->expires_at?->format('d M, Y') ?? 'Never' }}
        </p>
    </div>
</body>

</html>