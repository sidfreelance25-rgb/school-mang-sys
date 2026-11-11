@extends('layouts.app')
@section('title','Notices')
@section('heading','Notices & Announcements')
@section('content')
@if(auth()->user()->canManageSchool())
<form method="POST" action="{{ route('notices.store') }}" class="panel p-4 mb-4 grid gap-3 md:grid-cols-2">@csrf
<input class="form-input md:col-span-2" name="title" required>
<textarea class="form-input md:col-span-2" name="body" rows="4" required></textarea>
<select class="form-input" name="type">@foreach(['general','academic','holiday','event','circular'] as $type)<option value="{{ $type }}">{{ ucfirst($type) }}</option>@endforeach</select>
<select class="form-input" name="audience">@foreach(['all','students','teachers','parents','staff'] as $aud)<option value="{{ $aud }}">{{ ucfirst($aud) }}</option>@endforeach</select>
<input class="form-input" type="date" name="publish_date" value="{{ now()->format('Y-m-d') }}" required>
<input class="form-input" type="date" name="expiry_date">
<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_important" value="1"> Important</label>
<button class="btn-brand">Publish Notice</button>
</form>
@endif
<div class="space-y-3">
@forelse($notices as $notice)
<div class="panel p-5">
<div class="flex flex-wrap items-start justify-between gap-2">
<div>
<div class="display text-xl font-semibold">{{ $notice->title }}</div>
<div class="text-xs text-slate-500">{{ $notice->publish_date->format('d M Y') }} · {{ ucfirst($notice->type) }} · {{ ucfirst($notice->audience) }}</div>
</div>
<div class="flex gap-2 items-center">
@if($notice->is_important)<span class="badge badge-warning">Important</span>@endif
@if(auth()->user()->canManageSchool())
<form method="POST" action="{{ route('notices.destroy',$notice) }}" onsubmit="return confirm('Delete notice?')">@csrf @method('DELETE')<button class="text-rose-600 text-sm font-semibold">Delete</button></form>
@endif
</div>
</div>
<p class="mt-3 text-slate-700 whitespace-pre-line">{{ $notice->body }}</p>
</div>
@empty
<div class="panel p-8 text-center text-slate-500">No notices published.</div>
@endforelse
</div>
<div class="mt-4">{{ $notices->links() }}</div>
@endsection