@extends('layouts.app')
@section('title','Fees')
@section('heading','Fee Management')
@section('content')
<div class="grid gap-4 xl:grid-cols-3 mb-4">
<form method="POST" action="{{ route('fees.categories.store') }}" class="panel p-4 space-y-2">@csrf
<h3 class="font-semibold">Fee Category</h3>
<input class="form-input" name="name" required>
<input class="form-input" name="code" required>
<textarea class="form-input" name="description" rows="2"></textarea>
<button class="btn-brand">Save</button>
</form>
<form method="POST" action="{{ route('fees.structures.store') }}" class="panel p-4 space-y-2">@csrf
<h3 class="font-semibold">Fee Structure</h3>
<select class="form-input" name="fee_category_id">@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select>
<select class="form-input" name="school_class_id">@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select>
<select class="form-input" name="academic_session_id">@foreach($sessions as $session)<option value="{{ $session->id }}">{{ $session->name }}</option>@endforeach</select>
<input class="form-input" type="number" step="0.01" name="amount" required>
<select class="form-input" name="frequency"><option value="monthly">Monthly</option><option value="quarterly">Quarterly</option><option value="yearly">Yearly</option><option value="one_time">One time</option></select>
<button class="btn-brand">Save Structure</button>
</form>
<form method="POST" action="{{ route('fees.invoices.store') }}" class="panel p-4 space-y-2">@csrf
<h3 class="font-semibold">Create Invoice</h3>
<select class="form-input" name="student_id">@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->admission_no }})</option>@endforeach</select>
<select class="form-input" name="fee_structure_id"><option value="">Optional structure</option>@foreach($structures as $structure)<option value="{{ $structure->id }}">{{ $structure->feeCategory->name }} · {{ $structure->schoolClass->name }} · ₹{{ $structure->amount }}</option>@endforeach</select>
<select class="form-input" name="academic_session_id">@foreach($sessions as $session)<option value="{{ $session->id }}">{{ $session->name }}</option>@endforeach</select>
<input class="form-input" name="title" placeholder="Tuition Fee - April" required>
<input class="form-input" type="number" step="0.01" name="amount" required>
<input class="form-input" type="number" step="0.01" name="discount" value="0">
<input class="form-input" type="date" name="due_date">
<button class="btn-brand">Create Invoice</button>
</form>
</div>
<form method="GET" class="mb-4 flex gap-2"><input class="form-input w-56" name="search" value="{{ request('search') }}" placeholder="Search invoices"><select class="form-input w-36" name="status"><option value="">All</option>@foreach(['unpaid','partial','paid'] as $st)<option value="{{ $st }}" @selected(request('status')==$st)>{{ ucfirst($st) }}</option>@endforeach</select><button class="btn-soft">Filter</button></form>
<div class="panel"><div class="table-wrap"><table class="data"><thead><tr><th>Invoice</th><th>Student</th><th>Amount</th><th>Paid</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($invoices as $invoice)
<tr>
<td>{{ $invoice->invoice_no }}<div class="text-xs text-slate-500">{{ $invoice->title }}</div></td>
<td>{{ $invoice->student->user->name }}</td>
<td>₹{{ number_format($invoice->amount,2) }}</td>
<td>₹{{ number_format($invoice->paid_amount,2) }}</td>
<td><span class="badge {{ $invoice->status==='paid'?'badge-success':($invoice->status==='partial'?'badge-warning':'badge-danger') }}">{{ ucfirst($invoice->status) }}</span></td>
<td><a class="text-teal-700 font-semibold" href="{{ route('fees.show',$invoice) }}">Open</a></td>
</tr>
@endforeach
</tbody></table></div><div class="p-4">{{ $invoices->links() }}</div></div>
@endsection