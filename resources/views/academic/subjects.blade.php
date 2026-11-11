@extends('layouts.app')
@section('title','Subjects')
@section('heading','Subjects')
@section('content')
<div class="grid gap-4 lg:grid-cols-2">
<form method="POST" action="{{ route('academic.subjects.store') }}" class="panel p-5 space-y-3">@csrf
<h3 class="font-semibold">Add Subject</h3>
<input class="form-input" name="name" required>
<input class="form-input" name="code" required>
<select class="form-input" name="type"><option value="core">Core</option><option value="elective">Elective</option><option value="optional">Optional</option></select>
<button class="btn-brand">Save Subject</button>
</form>
<form method="POST" action="{{ route('academic.subjects.assign') }}" class="panel p-5 space-y-3">@csrf
<h3 class="font-semibold">Assign to Class</h3>
<select class="form-input" name="school_class_id">@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select>
<select class="form-input" name="subject_id">@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select>
<select class="form-input" name="teacher_id"><option value="">Teacher</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>@endforeach</select>
<button class="btn-brand">Assign</button>
</form>
</div>
<div class="panel mt-4"><div class="table-wrap"><table class="data"><thead><tr><th>Code</th><th>Name</th><th>Type</th></tr></thead><tbody>
@foreach($subjects as $subject)<tr><td>{{ $subject->code }}</td><td>{{ $subject->name }}</td><td>{{ ucfirst($subject->type) }}</td></tr>@endforeach
</tbody></table></div></div>
@endsection