@extends('layouts.app')
@section('title','Timetable')
@section('heading','Timetable')
@section('content')
@if(!$session)
<div class="panel p-6 text-center text-slate-500">Set a current academic session first.</div>
@else
<div class="grid gap-4 xl:grid-cols-3 mb-4">
<form method="POST" action="{{ route('timetable.periods.store') }}" class="panel p-4 space-y-2">@csrf
<h3 class="font-semibold">Add Period</h3>
<input class="form-input" name="name" placeholder="Period 1" required>
<div class="grid grid-cols-2 gap-2"><input class="form-input" type="time" name="start_time" required><input class="form-input" type="time" name="end_time" required></div>
<input class="form-input" type="number" name="sort_order" value="{{ $periods->count()+1 }}">
<button class="btn-brand">Save Period</button>
</form>
<form method="GET" class="panel p-4 space-y-2">
<h3 class="font-semibold">View Class Timetable</h3>
<select class="form-input" name="section_id">@foreach($sections as $section)<option value="{{ $section->id }}" @selected($sectionId==$section->id)>{{ $section->display_name }}</option>@endforeach</select>
<button class="btn-soft">Load</button>
</form>
<form method="POST" action="{{ route('timetable.store') }}" class="panel p-4 space-y-2">@csrf
<h3 class="font-semibold">Assign Slot</h3>
<input type="hidden" name="academic_session_id" value="{{ $session->id }}">
<select class="form-input" name="school_class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select>
<select class="form-input" name="section_id" required>@foreach($sections as $section)<option value="{{ $section->id }}">{{ $section->display_name }}</option>@endforeach</select>
<select class="form-input" name="subject_id" required>@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select>
<select class="form-input" name="teacher_id" required>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>@endforeach</select>
<select class="form-input" name="period_id" required>@foreach($periods as $period)<option value="{{ $period->id }}">{{ $period->name }} ({{ substr($period->start_time,0,5) }}-{{ substr($period->end_time,0,5) }})</option>@endforeach</select>
<select class="form-input" name="day_of_week" required>@foreach($days as $day)<option value="{{ $day }}">{{ \App\Models\Timetable::dayName($day) }}</option>@endforeach</select>
<button class="btn-brand">Save Slot</button>
</form>
</div>
@if($sectionId)
<div class="panel overflow-x-auto">
<table class="data">
<thead><tr><th>Period</th>@foreach($days as $day)<th>{{ \App\Models\Timetable::dayName($day) }}</th>@endforeach</tr></thead>
<tbody>
@foreach($periods as $period)
<tr>
<td class="font-semibold">{{ $period->name }}<div class="text-xs text-slate-500">{{ substr($period->start_time,0,5) }}</div></td>
@foreach($days as $day)
@php $slot=$entries->get($day.'-'.$period->id)?->first(); @endphp
<td>
@if($slot)
<div class="text-sm font-semibold">{{ $slot->subject->name }}</div>
<div class="text-xs text-slate-500">{{ $slot->teacher->name }}</div>
<form method="POST" action="{{ route('timetable.destroy',$slot) }}">@csrf @method('DELETE')<button class="text-xs text-rose-600">Remove</button></form>
@else
<span class="text-slate-300">—</span>
@endif
</td>
@endforeach
</tr>
@endforeach
</tbody>
</table>
</div>
@endif
@endif
@endsection