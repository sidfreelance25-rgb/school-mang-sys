@extends('layouts.app')
@section('title',$examination->name)
@section('heading',$examination->name)
@section('subheading',$examination->examType->name)
@section('content')
<form method="POST" action="{{ route('examinations.schedules.store',$examination) }}" class="panel p-4 mb-4 grid gap-3 md:grid-cols-3">@csrf
<select class="form-input" name="school_class_id">@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select>
<select class="form-input" name="subject_id">@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select>
<input class="form-input" type="date" name="exam_date" required>
<input class="form-input" type="time" name="start_time">
<input class="form-input" type="time" name="end_time">
<input class="form-input" type="number" step="0.01" name="max_marks" value="100" required>
<input class="form-input" type="number" step="0.01" name="passing_marks" value="33" required>
<input class="form-input" name="room" placeholder="Room">
<button class="btn-brand">Add Schedule</button>
</form>
<div class="panel"><div class="table-wrap"><table class="data"><thead><tr><th>Class</th><th>Subject</th><th>Date</th><th>Marks</th><th></th></tr></thead><tbody>
@foreach($examination->schedules as $schedule)
<tr>
<td>{{ $schedule->schoolClass->name }}</td>
<td>{{ $schedule->subject->name }}</td>
<td>{{ $schedule->exam_date->format('d M Y') }}</td>
<td>{{ $schedule->max_marks }} / Pass {{ $schedule->passing_marks }}</td>
<td><a class="text-teal-700 font-semibold" href="{{ route('examinations.marks',$schedule) }}">Enter Marks</a></td>
</tr>
@endforeach
</tbody></table></div></div>
@endsection