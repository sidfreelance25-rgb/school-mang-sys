@extends('layouts.app')
@section('title','Academic Sessions')
@section('heading','Academic Sessions')
@section('content')
<div class="grid gap-4 lg:grid-cols-2">
<form method="POST" action="{{ route('academic.sessions.store') }}" class="panel p-5 space-y-3">@csrf
<h3 class="font-semibold">Add Session</h3>
<input class="form-input" name="name" placeholder="2025-26" required>
<div class="grid grid-cols-2 gap-3"><input class="form-input" type="date" name="start_date" required><input class="form-input" type="date" name="end_date" required></div>
<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_current" value="1"> Set as current</label>
<button class="btn-brand">Save Session</button>
</form>
<div class="panel"><div class="table-wrap"><table class="data"><thead><tr><th>Name</th><th>Period</th><th>Current</th><th></th></tr></thead><tbody>
@foreach($sessions as $session)
<tr>
<td>{{ $session->name }}</td>
<td>{{ $session->start_date->format('d M Y') }} - {{ $session->end_date->format('d M Y') }}</td>
<td>@if($session->is_current)<span class="badge badge-success">Current</span>@endif</td>
<td>@unless($session->is_current)<form method="POST" action="{{ route('academic.sessions.current',$session) }}">@csrf<button class="text-teal-700 font-semibold">Make current</button></form>@endunless</td>
</tr>
@endforeach
</tbody></table></div></div>
</div>
@endsection