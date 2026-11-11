@extends('layouts.app')
@section('title','Leave Requests')
@section('heading','Leave Requests')
@section('content')
<form method="POST" action="{{ route('leaves.store') }}" class="panel p-4 mb-4 grid gap-3 md:grid-cols-2">@csrf
<input class="form-input" type="date" name="from_date" required>
<input class="form-input" type="date" name="to_date" required>
<select class="form-input" name="leave_type">@foreach(['casual','sick','emergency','other'] as $type)<option value="{{ $type }}">{{ ucfirst($type) }}</option>@endforeach</select>
<textarea class="form-input md:col-span-2" name="reason" rows="2" placeholder="Reason"></textarea>
<button class="btn-brand">Submit Leave</button>
</form>
<div class="panel"><div class="table-wrap"><table class="data"><thead><tr><th>Staff</th><th>Dates</th><th>Type</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($leaves as $leave)
<tr>
<td>{{ $leave->user->name }}</td>
<td>{{ $leave->from_date->format('d M Y') }} - {{ $leave->to_date->format('d M Y') }}</td>
<td>{{ ucfirst($leave->leave_type) }}</td>
<td><span class="badge {{ $leave->status==='approved'?'badge-success':($leave->status==='rejected'?'badge-danger':'badge-warning') }}">{{ ucfirst($leave->status) }}</span></td>
<td>
@if(auth()->user()->canManageSchool() && $leave->status==='pending')
<form method="POST" action="{{ route('leaves.review',$leave) }}" class="flex gap-2">@csrf
<button name="status" value="approved" class="text-emerald-700 font-semibold">Approve</button>
<button name="status" value="rejected" class="text-rose-700 font-semibold">Reject</button>
</form>
@endif
</td>
</tr>
@endforeach
</tbody></table></div><div class="p-4">{{ $leaves->links() }}</div></div>
@endsection