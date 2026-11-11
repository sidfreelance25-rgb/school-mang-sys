<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'SchoolMS') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700|source-serif-4:600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand: #0f766e;
            --brand-dark: #115e59;
            --ink: #0f172a;
            --muted: #64748b;
            --surface: #f1f5f9;
            --panel: #ffffff;
            --line: #e2e8f0;
            --accent: #ea580c;
        }
        body { font-family: 'Manrope', sans-serif; color: var(--ink); background: linear-gradient(180deg, #ecfeff 0%, var(--surface) 240px, var(--surface) 100%); }
        .display { font-family: 'Source Serif 4', serif; }
        .sidebar-link { display: flex; align-items: center; gap: .75rem; padding: .65rem .9rem; border-radius: .75rem; color: #cbd5e1; font-weight: 500; transition: .15s ease; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,.08); color: white; }
        .stat-card { background: var(--panel); border: 1px solid var(--line); border-radius: 1rem; padding: 1.1rem 1.25rem; }
        .panel { background: var(--panel); border: 1px solid var(--line); border-radius: 1rem; }
        .btn-brand { background: var(--brand); color: white; border-radius: .7rem; padding: .55rem 1rem; font-weight: 600; }
        .btn-brand:hover { background: var(--brand-dark); }
        .btn-soft { background: #ccfbf1; color: #115e59; border-radius: .7rem; padding: .55rem 1rem; font-weight: 600; }
        .badge { display: inline-flex; align-items: center; border-radius: .5rem; padding: .15rem .55rem; font-size: .75rem; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #ffedd5; color: #9a3412; }
        .badge-info { background: #e0f2fe; color: #075985; }
        .table-wrap { overflow-x: auto; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { text-align: left; font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); padding: .75rem 1rem; border-bottom: 1px solid var(--line); }
        table.data td { padding: .85rem 1rem; border-bottom: 1px solid var(--line); vertical-align: top; }
        .form-input { width: 100%; border: 1px solid var(--line); border-radius: .7rem; padding: .6rem .8rem; background: white; }
        .form-label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: .35rem; color: #334155; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .print-area { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="antialiased" x-data="{ sidebarOpen: false }">
<div class="min-h-screen lg:flex">
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden no-print" @click="sidebarOpen = false" style="display:none;"></div>

    <aside class="no-print fixed inset-y-0 left-0 z-50 w-72 -translate-x-full bg-slate-900 text-white transition lg:static lg:translate-x-0 lg:flex-shrink-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="flex h-full flex-col">
            <div class="border-b border-white/10 px-5 py-5">
                <div class="text-xs uppercase tracking-[0.2em] text-teal-300">SchoolMS</div>
                <div class="display mt-1 text-2xl font-semibold">{{ \App\Models\SchoolSetting::current()->name }}</div>
                <div class="mt-1 text-sm text-slate-400">{{ auth()->user()->role->label() }}</div>
            </div>
            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 text-sm">
                @include('layouts.sidebar')
            </nav>
            <div class="border-t border-white/10 p-4 text-sm text-slate-400">
                Signed in as <span class="text-white">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <header class="no-print sticky top-0 z-30 border-b border-slate-200/80 bg-white/80 backdrop-blur">
            <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6">
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-lg border border-slate-200 p-2 lg:hidden" @click="sidebarOpen = true">
                        <span class="sr-only">Open menu</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="display text-xl font-semibold text-slate-900">@yield('heading', 'Dashboard')</h1>
                        <p class="text-sm text-slate-500">@yield('subheading', 'Manage school operations')</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('profile.edit') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="px-4 py-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <ul class="list-disc ps-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>
    </div>
</div>
</body>
</html>
