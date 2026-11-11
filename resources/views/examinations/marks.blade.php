@extends('layouts.app')
@section('title','Enter Marks')
@section('heading','Enter Marks')
@section('subheading',$schedule->subject->name.' · '.$schedule->schoolClass->name)
@section('content')
<form method="POST" action="{{ route('examinations.marks.store',$schedule) }}" class="panel p-4">@csrf
<div class="table-wrap"><table class="data"><thead><tr><th>Student</th><th>Marks / {{ $schedule->max_marks }}</th><th>Absent</th><th>Remarks</th></tr></thead><tbody>
@foreach($students as $student)
@php $result=$results->get($student->id); @endphp
<tr>
<td>{{ $student->user->name }}</td>
<td><input class="form-input" type="number" step="0.01" name="marks[{{ $student->id }}][marks_obtained]" value="{{ $result->marks_obtained ?? '' }}" max="{{ $schedule->max_marks }}"></td>
<td><input type="checkbox" name="marks[{{ $student->id }}][is_absent]" value="1" @checked($result->is_absent ?? false)></td>
<td><input class="form-input" name="marks[{{ $student->id }}][remarks]" value="{{ $result->remarks ?? '' }}"></td>
</tr>
@endforeach
</tbody></table></div>
<button class="btn-brand mt-4">Save Marks</button>
</form>
@endsection