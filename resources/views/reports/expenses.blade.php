@extends('layouts.app')
@section('title','Expense Report')
@section('heading','Expense Report')
@section('content')
<form method="GET" class="panel p-4 mb-4 flex gap-2"><input class="form-input w-40" type="date" name="from" value="{{ request('from') }}"><input class="form-input w-40" type="date" name="to" value="{{ request('to') }}"><button class="btn-soft">Filter</button><button type="button" class="btn-brand" onclick="window.print()">Print</button></form>
<div class="stat-card mb-4 inline-block"><div class="text-sm text-slate-500">Page Total</div><div class="text-2xl font-bold">₹{{ number_format($total,2) }}</div></div>
<div class="panel print-area"><div class="table-wrap"><table class="data"><thead><tr><th>Date</th><th>Title</th><th>Category</th><th>Amount</th></tr></thead><tbody>
@foreach($expenses as $expense)<tr><td>{{ $expense->expense_date->format('d M Y') }}</td><td>{{ $expense->title }}</td><td>{{ $expense->category->name }}</td><td>₹{{ number_format($expense->amount,2) }}</td></tr>@endforeach
</tbody></table></div><div class="p-4 no-print">{{ $expenses->links() }}</div></div>
@endsection