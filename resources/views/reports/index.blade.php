@extends('layouts.app')
@section('title','Reports')
@section('heading','Reports & Analytics')
@section('content')
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
@foreach([
['Admissions','reports.admissions','Student admission listings'],
['Attendance','reports.attendance','Student and teacher attendance'],
['Fees','reports.fees','Collections and pending dues'],
['Examinations','reports.examinations','Recent result entries'],
['Expenses','reports.expenses','School expense summaries'],
['Class Students','reports.class-students','Class-wise student rolls'],
] as [$title,$route,$desc])
<a href="{{ route($route) }}" class="panel p-5 hover:border-teal-300 transition">
<div class="display text-xl font-semibold">{{ $title }}</div>
<p class="mt-2 text-sm text-slate-500">{{ $desc }}</p>
</a>
@endforeach
</div>
@endsection