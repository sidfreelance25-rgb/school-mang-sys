@extends('layouts.app')
@section('title','Homework')
@section('heading','Homework & Assignments')
@section('content')
@if(auth()->user()->hasAnyRole(\App\Enums\UserRole::Admin,\App\Enums\UserRole::Principal,\App\Enums\UserRole::Teacher))
<form method="POST" enctype="multipart/form-data" action="{{ route('homework.store') }}" class="panel p-4 mb-4 grid gap-3 md:grid-cols-2">@csrf
<input class="form-input md:col-span-2" name="title" placeholder="Title" required>
<textarea class="form-input md:col-span-2" name="description" rows="3" placeholder="Description"></textarea>
<select class="form-input" name="school_class_id">@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select>
<select class="form-input" name="section_id"><option value="">All sections</option>@foreach($sections as $section)<option value="{{ $section->id }}">{{ $section->display_name }}</option>@endforeach</select>
<select class="form-input" name="subject_id">@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select>
<input class="form-input" type="date" name="assigned_date" value="{{ now()->format('Y-m-d') }}" required>
<input class="form-input" type="date" name="due_date" required>
<input class="form-input" type="file" name="attachment">
<button class="btn-brand">Assign Homework</button>
</form>
@endif
<div class="panel"><div class="table-wrap"><table class="data"><thead><tr><th>Title</th><th>Class</th><th>Subject</th><th>Due</th><th></th></tr></thead><tbody>
@forelse($homeworks as $homework)
<tr>
<td>{{ $homework->title }}</td>
<td>{{ $homework->schoolClass->name }} {{ $homework->section->name ?? '' }}</td>
<td>{{ $homework->subject->name }}</td>
<td>{{ $homework->due_date->format('d M Y') }}</td>
<td><a class="text-teal-700 font-semibold" href="{{ route('homework.show',$homework) }}">Open</a></td>
</tr>
@empty<tr><td colspan="5" class="py-8 text-center text-slate-500">No homework yet.</td></tr>@endforelse
</tbody></table></div><div class="p-4">{{ $homeworks->links() }}</div></div>
@endsection