@extends('layouts.app')
@section('title','Teachers')
@section('heading','Teachers')
@section('subheading','Staff profiles and subject assignments')
@section('content')
<div class="mb-4 flex flex-wrap justify-between gap-3">
<form method="GET" class="flex gap-2"><input class="form-input w-64" name="search" value="{{ request('search') }}" placeholder="Search teachers"><button class="btn-soft">Search</button></form>
<a href="{{ route('teachers.create') }}" class="btn-brand">Add Teacher</a>
</div>
<div class="panel"><div class="table-wrap"><table class="data">
<thead><tr><th>Employee ID</th><th>Name</th><th>Specialization</th><th>Phone</th><th></th></tr></thead>
<tbody>
@forelse($teachers as $teacher)
<tr>
<td class="font-semibold">{{ $teacher->employee_id }}</td>
<td>{{ $teacher->user->name }}<div class="text-xs text-slate-500">{{ $teacher->user->email }}</div></td>
<td>{{ $teacher->specialization ?: '-' }}</td>
<td>{{ $teacher->user->phone ?: '-' }}</td>
<td class="space-x-2"><a class="text-teal-700 font-semibold" href="{{ route('teachers.show',$teacher) }}">View</a><a class="text-slate-600 font-semibold" href="{{ route('teachers.edit',$teacher) }}">Edit</a></td>
</tr>
@empty<tr><td colspan="5" class="py-8 text-center text-slate-500">No teachers yet.</td></tr>@endforelse
</tbody></table></div><div class="p-4">{{ $teachers->links() }}</div></div>
@endsection