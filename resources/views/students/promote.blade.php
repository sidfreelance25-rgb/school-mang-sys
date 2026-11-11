@extends('layouts.app')
@section('title','Promote Students')
@section('heading','Promote Students')
@section('subheading','Move students to the next session/class')
@section('content')
<form method="POST" action="{{ route('students.promote.store') }}" class="panel p-5 space-y-4">
@csrf
<div class="grid gap-4 md:grid-cols-2">
<div><label class="form-label">To Session</label><select class="form-input" name="to_session_id" required>@foreach($sessions as $session)<option value="{{ $session->id }}">{{ $session->name }}</option>@endforeach</select></div>
<div><label class="form-label">To Class</label><select class="form-input" name="to_class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select></div>
<div><label class="form-label">To Section</label><select class="form-input" name="to_section_id" required>@foreach($sections as $section)<option value="{{ $section->id }}">{{ $section->display_name }}</option>@endforeach</select></div>
<div><label class="form-label">Status</label><select class="form-input" name="status"><option value="promoted">Promoted</option><option value="retained">Retained</option></select></div>
</div>
<div class="table-wrap max-h-96 overflow-y-auto border border-slate-200 rounded-xl">
<table class="data"><thead><tr><th></th><th>Student</th><th>Current</th></tr></thead><tbody>
@foreach($students as $student)
<tr><td><input type="checkbox" name="student_ids[]" value="{{ $student->id }}"></td><td>{{ $student->user->name }} ({{ $student->admission_no }})</td><td>{{ $student->full_class }}</td></tr>
@endforeach
</tbody></table>
</div>
<button class="btn-brand">Promote Selected</button>
</form>
@endsection