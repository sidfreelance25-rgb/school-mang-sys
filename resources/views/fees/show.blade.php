@extends('layouts.app')
@section('title',$invoice->invoice_no)
@section('heading',$invoice->invoice_no)
@section('subheading',$invoice->student->user->name)
@section('content')
<div class="grid gap-4 lg:grid-cols-3">
<div class="panel p-5 lg:col-span-2 space-y-2">
<div><strong>Title:</strong> {{ $invoice->title }}</div>
<div><strong>Amount:</strong> ₹{{ number_format($invoice->amount,2) }}</div>
<div><strong>Discount:</strong> ₹{{ number_format($invoice->discount,2) }}</div>
<div><strong>Paid:</strong> ₹{{ number_format($invoice->paid_amount,2) }}</div>
<div><strong>Balance:</strong> ₹{{ number_format($invoice->balance,2) }}</div>
<div><strong>Status:</strong> {{ ucfirst($invoice->status) }}</div>
<h3 class="display text-xl font-semibold pt-4">Payments</h3>
<div class="table-wrap"><table class="data"><thead><tr><th>Receipt</th><th>Date</th><th>Amount</th><th>Method</th><th></th></tr></thead><tbody>
@foreach($invoice->payments as $payment)
<tr>
<td>{{ $payment->receipt_no }}</td>
<td>{{ $payment->payment_date->format('d M Y') }}</td>
<td>₹{{ number_format($payment->amount,2) }}</td>
<td>{{ strtoupper($payment->payment_method) }}</td>
<td><a class="text-teal-700 font-semibold" href="{{ route('fees.receipt',$payment) }}">Receipt</a></td>
</tr>
@endforeach
</tbody></table></div>
</div>
@if(auth()->user()->hasAnyRole(\App\Enums\UserRole::Admin,\App\Enums\UserRole::Accountant) && $invoice->balance > 0)
<form method="POST" action="{{ route('fees.payments.store',$invoice) }}" class="panel p-5 space-y-3 h-fit">@csrf
<h3 class="font-semibold">Record Payment</h3>
<input class="form-input" type="number" step="0.01" name="amount" max="{{ $invoice->balance }}" required>
<input class="form-input" type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required>
<select class="form-input" name="payment_method">@foreach(['cash','card','upi','bank_transfer','cheque'] as $m)<option value="{{ $m }}">{{ strtoupper($m) }}</option>@endforeach</select>
<input class="form-input" name="transaction_ref" placeholder="Reference">
<textarea class="form-input" name="notes" rows="2"></textarea>
<button class="btn-brand">Save Payment</button>
</form>
@endif
</div>
@endsection