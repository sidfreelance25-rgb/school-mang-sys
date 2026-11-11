@extends('layouts.app')
@section('title','Students')
@section('heading','Students')
@section('subheading','Admission, profiles, and class allocation')
@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <form class="flex flex-wrap gap-2" method="GET">
        <input class="form-input w-56" type="search" name="search" value="{{ request('search') }}" placeholder="Search name or admission no">
        <select class="form-input w-40" name="class_id">
            <option value="">All classes</option>
            @foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id')==$class->id)>{{ $class->name }}</option>@endforeach
        </select>
        <select class="form-input w-36" name="status">
            <option value="">All status</option>
            @foreach(['active','transferred','left','alumni'] as $status)<option value="{{ $status }}" @selected(request('status')==$status)>{{ ucfirst($status) }}</option>@endforeach
        </select>
        <button class="btn-soft">Filter</button>
    </form>
    <div class="flex gap-2">
        <a href="{{ route('students.promote') }}" class="btn-soft">Promote</a>
        <a href="{{ route('students.create') }}" class="btn-brand">Admit Student</a>
    </div>
</div>
<div class="panel">
<div class="table-wrap">
<table class="data">
<thead><tr><th>Admission</th><th>Name</th><th>Class</th><th>Session</th><th>Status</th><th></th></tr></thead>
<tbody>
@forelse($students as $student)
<tr>
<td class="font-semibold">{{ $student->admission_no }}</td>
<td>{{ $student->user->name }}<div class="text-xs text-slate-500">{{ $student->user->email }}</div></td>
<td>{{ $student->full_class }}</td>
<td>{{ $student->academicSession->name ?? '-' }}</td>
<td><span class="badge {{ $student->status==='active'?'badge-success':'badge-warning' }}">{{ ucfirst($student->status) }}</span></td>
<td class="space-x-2 whitespace-nowrap">
<a class="text-teal-700 font-semibold" href="{{ route('students.show',$student) }}">View</a>
<a class="text-slate-600 font-semibold" href="{{ route('students.edit',$student) }}">Edit</a>
</td>
</tr>
@empty
<tr><td colspan="6" class="py-10 text-center text-slate-500">No students found.</td></tr>
@endforelse
</tbody>
</table>
</div>
<div class="p-4">{{ $students->links() }}</div>
</div>
@endsection