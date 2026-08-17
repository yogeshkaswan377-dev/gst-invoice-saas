<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | GST Billing Pro</title>
    <meta name="description" content="Professional GST billing & invoicing software">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta name="theme-color" content="#1e3a8a">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #3b82f6;
            --accent: #f59e0b;
            --bg: #f1f5f9;
            --surface: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --radius: 16px;
            --shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--bg);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 10px;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 260px;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            z-index: 1040;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 800;
        }

        .brand-text {
            color: white;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
        }

        .brand-text small {
            display: block;
            font-size: 10px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.4);
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 2px;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.06);
            color: white;
            padding-left: 20px;
        }

        .nav-item.active {
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
        }

        .nav-item i {
            width: 20px;
            font-size: 15px;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            object-fit: cover;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-info .name {
            color: white;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info .email {
            color: rgba(255, 255, 255, 0.4);
            font-size: 10px;
        }

        .btn-logout {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            font-size: 14px;
            padding: 6px;
            transition: var(--transition);
        }

        .btn-logout:hover {
            color: #ef4444;
        }

        /* Topbar */
        .topbar {
            margin-left: 260px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .topbar h1 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 8px 16px 8px 36px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 13px;
            width: 200px;
            outline: none;
            transition: var(--transition);
        }

        .search-box input:focus {
            border-color: var(--primary-light);
            width: 240px;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13px;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 24px;
            min-height: 100vh;
        }

        /* Cards */
        .stat-card {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 20px 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: var(--transition);
            border: 1px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            border-color: #bfdbfe;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -30%;
            right: -30%;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #dbeafe, #ede9fe);
            opacity: 0.2;
            border-radius: 50%;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .stat-icon.blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .stat-icon.green {
            background: #d1fae5;
            color: #059669;
        }

        .stat-icon.amber {
            background: #fef3c7;
            color: #d97706;
        }

        .stat-icon.purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--text);
            position: relative;
            z-index: 1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--muted);
            font-weight: 500;
            margin-top: 4px;
            position: relative;
            z-index: 1;
        }

        /* Table Card */
        .table-card {
            background: var(--surface);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .table-card .table-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-card .table-header h2 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }

        .table-card table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-card thead {
            background: #f8fafc;
        }

        .table-card th {
            padding: 12px 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
        }

        .table-card td {
            padding: 12px 20px;
            font-size: 13px;
            border-top: 1px solid #f1f5f9;
        }

        .table-card tbody tr:hover {
            background: #f8fafc;
        }

        /* Badges */
        .badge-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-overdue {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-draft {
            background: #e2e8f0;
            color: #475569;
        }

        /* Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
            border: none;
            padding: 9px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-gradient:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
            color: white;
        }

        .btn-outline {
            background: white;
            color: var(--primary);
            border: 1.5px solid var(--border);
            padding: 9px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .btn-outline:hover {
            border-color: var(--primary-light);
            background: #f8fafc;
            color: var(--primary);
        }

        /* Mobile Bottom Nav */
        .bottom-nav {
            display: none;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .topbar {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
                padding: 16px;
                padding-bottom: 80px;
            }

            .bottom-nav {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                border-top: 1px solid var(--border);
                z-index: 1050;
                justify-content: space-around;
                padding: 8px 0 calc(8px + env(safe-area-inset-bottom));
            }

            .bottom-nav a {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2px;
                font-size: 10px;
                color: #94a3b8;
                text-decoration: none;
                font-weight: 500;
            }

            .bottom-nav a.active {
                color: var(--primary);
            }

            .bottom-nav a i {
                font-size: 18px;
            }

            .bottom-nav .fab {
                width: 48px;
                height: 48px;
                background: linear-gradient(135deg, #2563eb, #7c3aed);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 20px;
                margin-top: -24px;
                box-shadow: 0 4px 20px rgba(37, 99, 235, 0.5);
            }

            .stat-card {
                padding: 16px;
            }

            .stat-value {
                font-size: 22px;
            }
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1035;
        }

        .sidebar-overlay.show {
            display: block;
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon" style="background:none;">
                <img src="{{ asset('images/logo.png') }}" alt="InvoiceFlow" style="width:42px; height:42px; border-radius:12px;">
            </div>
            <div class="brand-text">
                InvoiceFlow
                <small>Smart GST Billing</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="{{ route('gst-invoices.index') }}" class="nav-item {{ request()->routeIs('gst-invoices.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> GST Invoices
            </a>
            <a href="{{ route('proformas.index') }}" class="nav-item {{ request()->routeIs('proformas.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Proformas
            </a>
            <a href="{{ route('clients.index') }}" class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Clients
            </a>
            <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="{{ route('reports.gstr1') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a href="{{ route('company.settings') }}" class="nav-item {{ request()->routeIs('company.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="{{ route('staff.invite.form') }}" class="nav-item {{ request()->routeIs('staff.invite.*') ? 'active' : '' }}">
                <i class="fas fa-user-plus"></i> Staff Invite
            </a>
        </nav>

        <div class="sidebar-footer">
            <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=3b82f6&color=fff' }}"
                class="avatar-sm" alt="">
            <div class="user-info">
                <div class="name">{{ Auth::user()->name }}</div>
                <div class="email">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Topbar -->
    <header class="topbar">
        <div style="display:flex; align-items:center; gap:12px;">
            <button onclick="toggleSidebar()" style="background:none; border:none; font-size:18px; color:#64748b; cursor:pointer;">
                <i class="fas fa-bars"></i>
            </button>
            <h1>@yield('title', 'Dashboard')</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        document.querySelectorAll('.sidebar-nav .nav-item').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 1024) toggleSidebar();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>