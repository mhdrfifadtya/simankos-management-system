<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SiManKos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { DEFAULT: '#0F172A', 600: '#1E293B', 500: '#334155' },
                        sky: { kos: '#38BDF8', light: '#BAE6FD', dark: '#0284C7' },
                        smoke: '#F1F5F9',
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    boxShadow: {
                        'card': '0 1px 3px 0 rgba(0,0,0,.06), 0 1px 2px -1px rgba(0,0,0,.04)',
                        'card-hover': '0 4px 6px -1px rgba(0,0,0,.08), 0 2px 4px -2px rgba(0,0,0,.04)',
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { -webkit-font-smoothing: antialiased; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Sidebar */
        .sidebar-item { display:flex; align-items:center; gap:.75rem; padding:.625rem 1rem; border-radius:.625rem; color:#94A3B8; font-size:.8125rem; font-weight:500; transition:all .15s; }
        .sidebar-item:hover { color:#fff; background:rgba(255,255,255,.07); }
        .sidebar-item.active { color:#38BDF8; background:rgba(56,189,248,.12); }
        .sidebar-item svg { flex-shrink:0; }
        .sidebar-group { padding:.5rem 1rem .25rem; font-size:.6875rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#475569; }

        /* Cards */
        .card { background:#fff; border-radius:.875rem; border:1px solid #F1F5F9; box-shadow:0 1px 3px rgba(0,0,0,.05); }
        .card-header { padding:1.25rem 1.5rem; border-bottom:1px solid #F8FAFC; }
        .card-body { padding:1.5rem; }

        /* Buttons */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.5rem 1rem; border-radius:.625rem; font-size:.8125rem; font-weight:600; transition:all .15s; cursor:pointer; }
        .btn-primary { background:#38BDF8; color:#0F172A; }
        .btn-primary:hover { background:#0EA5E9; }
        .btn-secondary { background:#F8FAFC; color:#475569; border:1px solid #E2E8F0; }
        .btn-secondary:hover { background:#F1F5F9; }
        .btn-danger { background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; }
        .btn-danger:hover { background:#FEE2E2; }
        .btn-success { background:#F0FDF4; color:#16A34A; border:1px solid #BBF7D0; }
        .btn-success:hover { background:#DCFCE7; }
        .btn-sm { padding:.375rem .75rem; font-size:.75rem; }
        .btn-lg { padding:.75rem 1.5rem; font-size:.9375rem; }

        /* Badges */
        .badge { display:inline-flex; align-items:center; padding:.2rem .625rem; border-radius:999px; font-size:.6875rem; font-weight:600; line-height:1.2; }
        .badge-green { background:#DCFCE7; color:#15803D; }
        .badge-blue { background:#DBEAFE; color:#1D4ED8; }
        .badge-sky { background:#E0F2FE; color:#0369A1; }
        .badge-yellow { background:#FEF9C3; color:#A16207; }
        .badge-orange { background:#FFF7ED; color:#C2410C; }
        .badge-red { background:#FEF2F2; color:#DC2626; }
        .badge-gray { background:#F8FAFC; color:#64748B; border:1px solid #E2E8F0; }
        .badge-navy { background:#EFF6FF; color:#1E40AF; }

        /* Tables */
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; }
        thead tr { border-bottom:1px solid #F1F5F9; }
        thead th { padding:.75rem 1rem; text-align:left; font-size:.6875rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94A3B8; white-space:nowrap; }
        tbody tr { border-bottom:1px solid #F8FAFC; transition:background .1s; }
        tbody tr:last-child { border-bottom:0; }
        tbody tr:hover { background:#FAFAFA; }
        tbody td { padding:.875rem 1rem; font-size:.8125rem; color:#334155; vertical-align:middle; }

        /* Forms */
        .form-label { display:block; font-size:.8125rem; font-weight:600; color:#334155; margin-bottom:.375rem; }
        .form-input { width:100%; padding:.625rem .875rem; background:#fff; border:1.5px solid #E2E8F0; border-radius:.625rem; font-size:.875rem; color:#0F172A; font-family:inherit; transition:border .15s,box-shadow .15s; outline:none; }
        .form-input:focus { border-color:#38BDF8; box-shadow:0 0 0 3px rgba(56,189,248,.1); }
        .form-input::placeholder { color:#CBD5E1; }
        select.form-input { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394A3B8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right .75rem center; background-size:1rem; padding-right:2.5rem; }
        textarea.form-input { resize:vertical; min-height:100px; }
        .form-error { font-size:.75rem; color:#DC2626; margin-top:.25rem; }
        .form-hint { font-size:.75rem; color:#94A3B8; margin-top:.25rem; }

        /* Alerts */
        .alert { padding:.875rem 1rem; border-radius:.75rem; font-size:.875rem; display:flex; align-items:flex-start; gap:.625rem; }
        .alert-success { background:#F0FDF4; border:1px solid #BBF7D0; color:#15803D; }
        .alert-error { background:#FEF2F2; border:1px solid #FECACA; color:#DC2626; }
        .alert-warning { background:#FFFBEB; border:1px solid #FDE68A; color:#92400E; }
        .alert-info { background:#EFF6FF; border:1px solid #BFDBFE; color:#1D4ED8; }

        /* Pagination */
        .pagination { display:flex; align-items:center; gap:.25rem; }
        .page-link { display:inline-flex; align-items:center; justify-content:center; min-width:2rem; height:2rem; padding:0 .5rem; border-radius:.5rem; font-size:.8125rem; font-weight:500; color:#64748B; transition:all .1s; }
        .page-link:hover { background:#F1F5F9; color:#0F172A; }
        .page-link.active { background:#38BDF8; color:#0F172A; font-weight:700; }

        /* Stat cards */
        .stat-card { padding:1.25rem; background:#fff; border-radius:.875rem; border:1px solid #F1F5F9; box-shadow:0 1px 3px rgba(0,0,0,.04); }
        .stat-icon { width:2.5rem; height:2.5rem; border-radius:.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

        [x-cloak] { display: none !important; }

        /* Scrollbar */
        ::-webkit-scrollbar { width:6px; height:6px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:#E2E8F0; border-radius:99px; }
        ::-webkit-scrollbar-thumb:hover { background:#CBD5E1; }

        /* ── Toast Notifications ── */
        #toast-container { position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999; display:flex; flex-direction:column-reverse; gap:.75rem; pointer-events:none; }
        .toast { display:flex; align-items:flex-start; gap:.875rem; min-width:300px; max-width:420px; padding:1rem 1.125rem; border-radius:.875rem; box-shadow:0 8px 24px rgba(0,0,0,.12), 0 2px 6px rgba(0,0,0,.08); pointer-events:all; position:relative; overflow:hidden; animation:toastIn .3s cubic-bezier(.34,1.56,.64,1) both; }
        .toast.removing { animation:toastOut .25s ease-in both; }
        .toast-success { background:#fff; border:1.5px solid #BBF7D0; }
        .toast-error   { background:#fff; border:1.5px solid #FECACA; }
        .toast-warning { background:#fff; border:1.5px solid #FDE68A; }
        .toast-info    { background:#fff; border:1.5px solid #BFDBFE; }
        .toast-icon { flex-shrink:0; width:2rem; height:2rem; border-radius:.625rem; display:flex; align-items:center; justify-content:center; }
        .toast-success .toast-icon { background:#DCFCE7; color:#16A34A; }
        .toast-error   .toast-icon { background:#FEF2F2; color:#DC2626; }
        .toast-warning .toast-icon { background:#FEF9C3; color:#A16207; }
        .toast-info    .toast-icon { background:#EFF6FF; color:#2563EB; }
        .toast-body { flex:1; min-width:0; }
        .toast-title { font-size:.8125rem; font-weight:700; color:#0F172A; line-height:1.3; }
        .toast-msg   { font-size:.8125rem; color:#475569; margin-top:.125rem; line-height:1.4; }
        .toast-close { flex-shrink:0; width:1.25rem; height:1.25rem; display:flex; align-items:center; justify-content:center; color:#94A3B8; cursor:pointer; border-radius:.375rem; transition:color .15s; margin-top:.0625rem; }
        .toast-close:hover { color:#475569; }
        .toast-progress { position:absolute; bottom:0; left:0; height:3px; border-radius:0 0 .875rem .875rem; animation:toastProgress linear both; }
        .toast-success .toast-progress { background:#22C55E; }
        .toast-error   .toast-progress { background:#EF4444; }
        .toast-warning .toast-progress { background:#EAB308; }
        .toast-info    .toast-progress { background:#3B82F6; }
        @keyframes toastIn { from { opacity:0; transform:translateX(100%) scale(.95); } to { opacity:1; transform:translateX(0) scale(1); } }
        @keyframes toastOut { from { opacity:1; transform:translateX(0) scale(1); } to { opacity:0; transform:translateX(100%) scale(.95); } }
        @keyframes toastProgress { from { width:100%; } to { width:0%; } }
        @keyframes spin { to { transform:rotate(360deg); } }
        .animate-spin { animation:spin 1s linear infinite; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F8FAFC] h-full" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
<div class="flex h-screen overflow-hidden">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-[#0F172A] transition-transform duration-300 lg:relative lg:translate-x-0 shadow-xl"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-kos to-sky-dark flex items-center justify-center shadow-lg shadow-sky-kos/30 flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <p class="text-white font-extrabold text-base leading-none tracking-tight">SiManKos</p>
                <p class="text-slate-500 text-xs mt-0.5">Kelola Kos, Lebih Mudah.</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">

            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10-4a1 1 0 011-1h4a1 1 0 011 1v8a1 1 0 01-1 1h-4a1 1 0 01-1-1v-8z"/></svg>
                Dashboard
            </a>

            <p class="sidebar-group mt-3">Properti & Penyewa</p>

            <a href="{{ route('rooms.index') }}" class="sidebar-item {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Manajemen Kamar
            </a>
            <a href="{{ route('facilities.index') }}" class="sidebar-item {{ request()->routeIs('facilities.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                Fasilitas
            </a>
            <a href="{{ route('tenants.index') }}" class="sidebar-item {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Manajemen Penyewa
            </a>
            <a href="{{ route('rentals.index') }}" class="sidebar-item {{ request()->routeIs('rentals.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Kontrak Sewa
            </a>

            <p class="sidebar-group mt-3">Keuangan</p>

            <a href="{{ route('invoices.index') }}" class="sidebar-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Tagihan (Invoice)
            </a>
            <a href="{{ route('payments.index') }}" class="sidebar-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Pembayaran
            </a>
            <p class="sidebar-group mt-3">Sistem</p>

            <a href="{{ route('notifications.index') }}" class="sidebar-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifikasi & WA
            </a>
            <a href="{{ route('reports.payments') }}" class="sidebar-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Laporan
            </a>
            <a href="{{ route('settings.index') }}" class="sidebar-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan
            </a>
        </nav>

        {{-- User --}}
        <div class="px-3 py-3 border-t border-white/5">
            <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-white/5 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-kos/30 to-sky-dark/30 flex items-center justify-center flex-shrink-0">
                    <span class="text-sky-kos text-sm font-bold">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate leading-none">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-slate-500 text-xs mt-0.5 truncate">Administrator</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-colors" title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" x-cloak></div>

    {{-- ═══ MAIN ═══ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="bg-white border-b border-slate-100 px-5 py-3.5 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors lg:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-1.5 text-sm">
                    <span class="text-slate-400">SiManKos</span>
                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="font-semibold text-slate-700">@yield('page-title', 'Dashboard')</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden sm:block text-xs text-slate-400 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                    {{ now()->locale('id')->isoFormat('ddd, D MMM YYYY') }}
                </span>
                <a href="{{ route('notifications.index') }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-sky-kos hover:bg-sky-kos/10 transition-colors relative">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </a>
                <a href="{{ route('settings.profile') }}" class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-kos to-sky-dark flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </a>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-5 lg:p-6">

            @yield('content')
        </main>
    </div>
</div>

{{-- ── Toast Container ── --}}
<div id="toast-container"></div>

<script>
const TOAST_ICONS = {
    success: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>',
    error:   '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>',
    warning: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>',
    info:    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
};
const TOAST_TITLES = { success:'Berhasil', error:'Gagal', warning:'Perhatian', info:'Informasi' };
const TOAST_DURATION = { success:4500, error:7000, warning:6000, info:5000 };

function siToast(message, type = 'info', title = null, duration = null) {
    const container = document.getElementById('toast-container');
    const ms = duration ?? TOAST_DURATION[type] ?? 5000;

    const el = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.innerHTML = `
        <div class="toast-icon">${TOAST_ICONS[type] ?? TOAST_ICONS.info}</div>
        <div class="toast-body">
            <p class="toast-title">${title ?? TOAST_TITLES[type] ?? 'Notifikasi'}</p>
            <p class="toast-msg">${message}</p>
        </div>
        <button class="toast-close" onclick="siToastRemove(this.closest('.toast'))">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="toast-progress" style="animation-duration:${ms}ms"></div>
    `;
    container.appendChild(el);
    const timer = setTimeout(() => siToastRemove(el), ms);
    el._timer = timer;
}

function siToastRemove(el) {
    if (!el || el.classList.contains('removing')) return;
    clearTimeout(el._timer);
    el.classList.add('removing');
    el.addEventListener('animationend', () => el.remove(), { once: true });
}

// Render server-side flash messages as toasts on load
document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
        siToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
        siToast(@json(session('error')), 'error');
    @endif
    @if($errors->any())
        const errs = @json($errors->all());
        siToast(errs.join('<br>'), 'error', 'Validasi Gagal', 8000);
    @endif
});
</script>

@stack('scripts')
</body>
</html>
