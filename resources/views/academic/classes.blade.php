@extends('layouts.app')
@section('title','Classes & Sections')
@section('heading','Classes & Sections')
@section('content')
<div class="grid gap-4 xl:grid-cols-3">
<form method="POST" action="{{ route('academic.classes.store') }}" class="panel p-5 space-y-3">@csrf
<h3 class="font-semibold">Add Class</h3>
<input class="form-input" name="name" placeholder="Class 8" required>
<input class="form-input" name="numeric_name" placeholder="8">
<input class="form-input" type="number" name="sort_order" placeholder="Sort order">
<button class="btn-brand">Create Class</button>
</form>
<form method="POST" action="{{ route('academic.sections.store') }}" class="panel p-5 space-y-3">@csrf
<h3 class="font-semibold">Add Section</h3>
<select class="form-input" name="school_class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select>
<input class="form-input" name="name" placeholder="A" required>
<select class="form-input" name="class_teacher_id"><option value="">Class teacher</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>@endforeach</select>
<input class="form-input" type="number" name="capacity" value="40">
<button class="btn-brand">Create Section</button>
</form>
<div class="panel xl:col-span-1 p-5">
<h3 class="font-semibold mb-3">Structure</h3>
@foreach($classes as $class)
<div class="mb-4">
<div class="font-semibold">{{ $class->name }}</div>
@foreach($class->sections as $section)
<div class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ $section->name }} · Teacher: {{ $section->classTeacher->name ?? 'Unassigned' }} · Cap {{ $section->capacity }}</div>
@endforeach
</div>
@endforeach
</div>
</div>
@endsection