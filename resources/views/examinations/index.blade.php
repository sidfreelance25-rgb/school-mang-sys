@extends('layouts.app')
@section('title','Examinations')
@section('heading','Examinations')
@section('content')
<div class="grid gap-4 lg:grid-cols-2 mb-4">
<form method="POST" action="{{ route('examinations.types.store') }}" class="panel p-4 space-y-2">@csrf
<h3 class="font-semibold">Exam Type</h3>
<input class="form-input" name="name" required>
<input class="form-input" name="code" required>
<textarea class="form-input" name="description" rows="2"></textarea>
<button class="btn-brand">Save Type</button>
</form>
<form method="POST" action="{{ route('examinations.store') }}" class="panel p-4 space-y-2">@csrf
<h3 class="font-semibold">Create Examination</h3>
<input class="form-input" name="name" required>
<select class="form-input" name="exam_type_id">@foreach($examTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select>
<select class="form-input" name="academic_session_id">@foreach($sessions as $session)<option value="{{ $session->id }}">{{ $session->name }}</option>@endforeach</select>
<div class="grid grid-cols-2 gap-2"><input class="form-input" type="date" name="start_date" required><input class="form-input" type="date" name="end_date" required></div>
<textarea class="form-input" name="description" rows="2"></textarea>
<button class="btn-brand">Create</button>
</form>
</div>
<div class="panel"><div class="table-wrap"><table class="data"><thead><tr><th>Name</th><th>Type</th><th>Dates</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($examinations as $exam)
<tr>
<td>{{ $exam->name }}</td>
<td>{{ $exam->examType->name }}</td>
<td>{{ $exam->start_date->format('d M Y') }} - {{ $exam->end_date->format('d M Y') }}</td>
<td><span class="badge badge-info">{{ $exam->status }}</span></td>
<td><a class="text-teal-700 font-semibold" href="{{ route('examinations.show',$exam) }}">Manage</a></td>
</tr>
@endforeach
</tbody></table></div><div class="p-4">{{ $examinations->links() }}</div></div>
@endsection