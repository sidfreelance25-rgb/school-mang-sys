@extends('layouts.app')
@section('title',$student->user->name)
@section('heading',$student->user->name)
@section('subheading',$student->admission_no.' · '.$student->full_class)
@section('content')
<div class="mb-4 flex flex-wrap gap-2">
@if(auth()->user()->canManageSchool())
<a href="{{ route('students.edit',$student) }}" class="btn-brand">Edit</a>
<form method="POST" action="{{ route('students.destroy',$student) }}" onsubmit="return confirm('Delete this student?')">@csrf @method('DELETE')<button class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white">Delete</button></form>
@endif
</div>
<div class="grid gap-4 lg:grid-cols-3">
<div class="panel p-5 lg:col-span-2 space-y-4">
<div class="grid gap-3 sm:grid-cols-2">
<div><div class="text-xs uppercase text-slate-500">Email</div><div class="font-semibold">{{ $student->user->email }}</div></div>
<div><div class="text-xs uppercase text-slate-500">Phone</div><div class="font-semibold">{{ $student->user->phone ?: '-' }}</div></div>
<div><div class="text-xs uppercase text-slate-500">DOB</div><div class="font-semibold">{{ optional($student->date_of_birth)->format('d M Y') ?: '-' }}</div></div>
<div><div class="text-xs uppercase text-slate-500">Status</div><div class="font-semibold">{{ ucfirst($student->status) }}</div></div>
<div class="sm:col-span-2"><div class="text-xs uppercase text-slate-500">Address</div><div class="font-semibold">{{ $student->address ?: '-' }}</div></div>
</div>
<h3 class="display text-xl font-semibold">Parents</h3>
@forelse($student->parents as $parent)
<div class="rounded-xl border border-slate-200 px-3 py-2">{{ $parent->user->name }} · {{ $parent->relation }} · {{ $parent->user->phone }}</div>
@empty<div class="text-slate-500 text-sm">No parent linked.</div>@endforelse
<h3 class="display text-xl font-semibold">Fee History</h3>
<div class="table-wrap"><table class="data"><thead><tr><th>Invoice</th><th>Amount</th><th>Paid</th><th>Status</th></tr></thead><tbody>
@foreach($student->feeInvoices as $invoice)
<tr><td>{{ $invoice->invoice_no }}</td><td>₹{{ number_format($invoice->amount,2) }}</td><td>₹{{ number_format($invoice->paid_amount,2) }}</td><td>{{ ucfirst($invoice->status) }}</td></tr>
@endforeach
</tbody></table></div>
</div>
<div class="space-y-4">
<div class="panel p-5">
@if($student->photo)<img src="{{ asset('storage/'.$student->photo) }}" class="mb-3 h-40 w-40 rounded-xl object-cover" alt="photo">@endif
<div class="text-sm text-slate-500">Session</div>
<div class="font-semibold">{{ $student->academicSession->name }}</div>
</div>
<div class="panel p-5">
<h3 class="font-semibold">Recent Attendance</h3>
<ul class="mt-2 space-y-1 text-sm">
@foreach($student->attendances as $att)
<li class="flex justify-between"><span>{{ $att->date->format('d M Y') }}</span><span class="badge {{ $att->status==='present'?'badge-success':($att->status==='absent'?'badge-danger':'badge-warning') }}">{{ ucfirst($att->status) }}</span></li>
@endforeach
</ul>
</div>
</div>
</div>
@endsection