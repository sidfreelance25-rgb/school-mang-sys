@extends('layouts.app')
@section('title','Fee Report')
@section('heading','Fee Collection Report')
@section('content')
<div class="mb-4 grid gap-3 sm:grid-cols-2">
<div class="stat-card"><div class="text-sm text-slate-500">Collected</div><div class="text-2xl font-bold">₹{{ number_format($collected,2) }}</div></div>
<div class="stat-card"><div class="text-sm text-slate-500">Outstanding</div><div class="text-2xl font-bold">₹{{ number_format($outstanding,2) }}</div></div>
</div>
<button class="btn-brand mb-4 no-print" onclick="window.print()">Print</button>
<div class="grid gap-4 lg:grid-cols-2">
<div class="panel print-area"><h3 class="p-4 font-semibold">Recent Payments</h3><div class="table-wrap"><table class="data"><thead><tr><th>Date</th><th>Receipt</th><th>Student</th><th>Amount</th></tr></thead><tbody>
@foreach($payments as $payment)<tr><td>{{ $payment->payment_date->format('d M Y') }}</td><td>{{ $payment->receipt_no }}</td><td>{{ $payment->student->user->name }}</td><td>₹{{ number_format($payment->amount,2) }}</td></tr>@endforeach
</tbody></table></div><div class="p-4 no-print">{{ $payments->links() }}</div></div>
<div class="panel print-area"><h3 class="p-4 font-semibold">Pending Invoices</h3><div class="table-wrap"><table class="data"><thead><tr><th>Invoice</th><th>Student</th><th>Balance</th></tr></thead><tbody>
@foreach($pending as $invoice)<tr><td>{{ $invoice->invoice_no }}</td><td>{{ $invoice->student->user->name }}</td><td>₹{{ number_format($invoice->balance,2) }}</td></tr>@endforeach
</tbody></table></div></div>
</div>
@endsection