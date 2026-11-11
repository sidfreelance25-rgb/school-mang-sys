@extends('layouts.app')
@section('title',$homework->title)
@section('heading',$homework->title)
@section('content')
<div class="panel p-5 mb-4">
<p class="text-slate-600">{{ $homework->description }}</p>
<div class="mt-3 text-sm text-slate-500">Due {{ $homework->due_date->format('d M Y') }} · {{ $homework->subject->name }} · {{ $homework->schoolClass->name }}</div>
@if($homework->attachment)<a class="mt-3 inline-block text-teal-700 font-semibold" href="{{ asset('storage/'.$homework->attachment) }}">Download attachment</a>@endif
</div>
@if(auth()->user()->hasAnyRole(\App\Enums\UserRole::Admin,\App\Enums\UserRole::Principal,\App\Enums\UserRole::Teacher))
<div class="panel"><div class="table-wrap"><table class="data"><thead><tr><th>Student</th><th>Status</th><th>Update</th></tr></thead><tbody>
@foreach($homework->submissions as $submission)
<tr>
<td>{{ $submission->student->user->name }}</td>
<td><span class="badge badge-info">{{ ucfirst($submission->status) }}</span></td>
<td>
<form method="POST" enctype="multipart/form-data" action="{{ route('homework.submissions.update',$submission) }}" class="flex flex-wrap gap-2">@csrf @method('PATCH')
<select class="form-input w-36" name="status">@foreach(['pending','submitted','late','reviewed'] as $st)<option value="{{ $st }}" @selected($submission->status===$st)>{{ ucfirst($st) }}</option>@endforeach</select>
<input class="form-input w-40" name="remarks" value="{{ $submission->remarks }}">
<button class="btn-soft">Save</button>
</form>
</td>
</tr>
@endforeach
</tbody></table></div></div>
@endif
@endsection