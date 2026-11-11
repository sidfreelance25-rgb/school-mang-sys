@php
    $user = auth()->user();
@endphp

<a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>

@if($user->hasAnyRole(\App\Enums\UserRole::Admin, \App\Enums\UserRole::Principal))
    <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">People</div>
    <a href="{{ route('students.index') }}" class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}">Students</a>
    <a href="{{ route('teachers.index') }}" class="sidebar-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">Teachers</a>
    <a href="{{ route('parents.index') }}" class="sidebar-link {{ request()->routeIs('parents.*') ? 'active' : '' }}">Parents</a>

    <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Academics</div>
    <a href="{{ route('academic.sessions') }}" class="sidebar-link {{ request()->routeIs('academic.sessions*') ? 'active' : '' }}">Sessions</a>
    <a href="{{ route('academic.classes') }}" class="sidebar-link {{ request()->routeIs('academic.classes*') || request()->routeIs('academic.sections*') ? 'active' : '' }}">Classes</a>
    <a href="{{ route('academic.subjects') }}" class="sidebar-link {{ request()->routeIs('academic.subjects*') ? 'active' : '' }}">Subjects</a>
@endif

@if($user->hasAnyRole(\App\Enums\UserRole::Admin, \App\Enums\UserRole::Principal, \App\Enums\UserRole::Teacher))
    <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Classroom</div>
    <a href="{{ route('attendance.students') }}" class="sidebar-link {{ request()->routeIs('attendance.students*') || request()->routeIs('attendance.report') ? 'active' : '' }}">Attendance</a>
    @if($user->canManageSchool())
        <a href="{{ route('attendance.teachers') }}" class="sidebar-link {{ request()->routeIs('attendance.teachers*') ? 'active' : '' }}">Staff Attendance</a>
    @endif
    <a href="{{ route('timetable.index') }}" class="sidebar-link {{ request()->routeIs('timetable.*') ? 'active' : '' }}">Timetable</a>
    <a href="{{ route('examinations.index') }}" class="sidebar-link {{ request()->routeIs('examinations.*') ? 'active' : '' }}">Examinations</a>
    <a href="{{ route('homework.index') }}" class="sidebar-link {{ request()->routeIs('homework.*') ? 'active' : '' }}">Homework</a>
    <a href="{{ route('leaves.index') }}" class="sidebar-link {{ request()->routeIs('leaves.*') ? 'active' : '' }}">Leave</a>
@endif

@if($user->hasAnyRole(\App\Enums\UserRole::Student, \App\Enums\UserRole::Parent))
    <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">My School</div>
    <a href="{{ route('homework.index') }}" class="sidebar-link {{ request()->routeIs('homework.*') ? 'active' : '' }}">Homework</a>
    <a href="{{ route('notices.index') }}" class="sidebar-link {{ request()->routeIs('notices.*') ? 'active' : '' }}">Notices</a>
@endif

@if($user->hasAnyRole(\App\Enums\UserRole::Admin, \App\Enums\UserRole::Accountant))
    <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Finance</div>
    <a href="{{ route('fees.index') }}" class="sidebar-link {{ request()->routeIs('fees.*') ? 'active' : '' }}">Fees</a>
    <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">Expenses</a>
@endif

@if($user->hasAnyRole(\App\Enums\UserRole::Admin, \App\Enums\UserRole::Principal, \App\Enums\UserRole::Accountant))
    <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Insights</div>
    <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">Reports</a>
@endif

@if($user->hasAnyRole(\App\Enums\UserRole::Admin, \App\Enums\UserRole::Principal, \App\Enums\UserRole::Teacher, \App\Enums\UserRole::Student, \App\Enums\UserRole::Parent))
    @unless($user->hasAnyRole(\App\Enums\UserRole::Student, \App\Enums\UserRole::Parent))
        <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Communication</div>
        <a href="{{ route('notices.index') }}" class="sidebar-link {{ request()->routeIs('notices.*') ? 'active' : '' }}">Notices</a>
    @endunless
@endif

@if($user->hasAnyRole(\App\Enums\UserRole::Admin, \App\Enums\UserRole::Principal))
    <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">System</div>
    <a href="{{ route('settings.edit') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">Settings</a>
@endif
