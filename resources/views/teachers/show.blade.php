@extends('layouts.app')
@section('title',$teacher->user->name)
@section('heading',$teacher->user->name)
@section('subheading',$teacher->employee_id)
@section('content')
<div class="mb-4 flex gap-2"><a href="{{ route('teachers.edit',$teacher) }}" class="btn-brand">Edit</a>
<form method="POST" action="{{ route('teachers.destroy',$teacher) }}" onsubmit="return confirm('Delete teacher?')">@csrf @method('DELETE')<button class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white">Delete</button></form></div>
<div class="grid gap-4 lg:grid-cols-2">
<div class="panel p-5 space-y-2">
<div><span class="text-slate-500">Email:</span> {{ $teacher->user->email }}</div>
<div><span class="text-slate-500">Phone:</span> {{ $teacher->user->phone ?: '-' }}</div>
<div><span class="text-slate-500">Qualification:</span> {{ $teacher->qualification ?: '-' }}</div>
<div><span class="text-slate-500">Specialization:</span> {{ $teacher->specialization ?: '-' }}</div>
<div><span class="text-slate-500">Joining:</span> {{ optional($teacher->joining_date)->format('d M Y') ?: '-' }}</div>
</div>
<div class="panel p-5">
<h3 class="font-semibold mb-2">Class / Subject Assignments</h3>
@forelse($assignments as $row)
<div class="rounded-lg border border-slate-200 px-3 py-2 mb-2">{{ $row->class_name }} · {{ $row->subject_name }}</div>
@empty<div class="text-slate-500 text-sm">No assignments.</div>@endforelse
</div>
</div>
@endsection