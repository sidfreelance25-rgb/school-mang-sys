@extends('layouts.app')
@section('title','Teacher Attendance')
@section('heading','Teacher Attendance')
@section('content')
<form method="GET" class="mb-4 flex gap-2"><input class="form-input w-44" type="date" name="date" value="{{ $date }}"><button class="btn-soft">Load</button></form>
<form method="POST" action="{{ route('attendance.teachers.store') }}" class="panel p-4">@csrf
<input type="hidden" name="date" value="{{ $date }}">
<div class="table-wrap"><table class="data"><thead><tr><th>Teacher</th><th>Status</th><th>Remarks</th></tr></thead><tbody>
@foreach($teachers as $teacher)
@php $row=$existing->get($teacher->id); @endphp
<tr>
<td>{{ $teacher->user->name }}</td>
<td><select class="form-input" name="attendance[{{ $teacher->id }}][status]">@foreach(['present','absent','leave'] as $st)<option value="{{ $st }}" @selected(($row->status ?? 'present')===$st)>{{ ucfirst($st) }}</option>@endforeach</select></td>
<td><input class="form-input" name="attendance[{{ $teacher->id }}][remarks]" value="{{ $row->remarks ?? '' }}"></td>
</tr>
@endforeach
</tbody></table></div>
<button class="btn-brand mt-4">Save</button>
</form>
@endsection