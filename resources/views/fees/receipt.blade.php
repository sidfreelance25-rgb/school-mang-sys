@extends('layouts.app')
@section('title','Fee Receipt')
@section('heading','Fee Receipt')
@section('content')
<div class="panel print-area mx-auto max-w-2xl p-8">
<div class="flex justify-between mb-6">
<div>
<div class="display text-2xl font-semibold">{{ \App\Models\SchoolSetting::current()->name }}</div>
<div class="text-slate-500">Fee Payment Receipt</div>
</div>
<button onclick="window.print()" class="btn-brand no-print">Print</button>
</div>
<div class="space-y-2 text-sm">
<div><strong>Receipt No:</strong> {{ $payment->receipt_no }}</div>
<div><strong>Date:</strong> {{ $payment->payment_date->format('d M Y') }}</div>
<div><strong>Student:</strong> {{ $payment->student->user->name }} ({{ $payment->student->admission_no }})</div>
<div><strong>Invoice:</strong> {{ $payment->feeInvoice->invoice_no }} · {{ $payment->feeInvoice->title }}</div>
<div><strong>Amount Paid:</strong> ₹{{ number_format($payment->amount,2) }}</div>
<div><strong>Method:</strong> {{ strtoupper($payment->payment_method) }}</div>
<div><strong>Received By:</strong> {{ $payment->receiver->name ?? 'N/A' }}</div>
</div>
<p class="mt-8 text-xs text-slate-500">This is a computer generated receipt.</p>
</div>
@endsection