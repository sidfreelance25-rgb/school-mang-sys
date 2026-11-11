<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SchoolMS') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700|source-serif-4:600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .display { font-family: 'Source Serif 4', serif; }
    </style>
</head>
<body class="antialiased text-slate-900">
<div class="min-h-screen grid lg:grid-cols-2">
    <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-teal-800 via-cyan-800 to-slate-900 p-10 text-white">
        <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,.25), transparent 35%), radial-gradient(circle at 80% 0%, rgba(251,146,60,.35), transparent 30%), radial-gradient(circle at 70% 80%, rgba(45,212,191,.25), transparent 40%);"></div>
        <div class="relative">
            <div class="text-xs uppercase tracking-[0.25em] text-teal-200">School Management System</div>
            <h1 class="display mt-4 text-5xl font-semibold leading-tight">SchoolMS</h1>
            <p class="mt-4 max-w-md text-teal-50/90">A simple platform for admissions, attendance, exams, fees, and day-to-day school operations.</p>
        </div>
        <div class="relative text-sm text-teal-100/80">Desktop · Tablet · Mobile ready</div>
    </div>
    <div class="flex items-center justify-center bg-slate-50 px-6 py-10">
        <div class="w-full max-w-md">
            <div class="mb-6 lg:hidden">
                <div class="text-xs uppercase tracking-[0.2em] text-teal-700">SchoolMS</div>
                <div class="display text-3xl font-semibold">Sign in</div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                {{ $slot }}
            </div>
            <p class="mt-4 text-center text-xs text-slate-500">Demo password for seeded users: <strong>password</strong></p>
        </div>
    </div>
</div>
</body>
</html>
