@extends('layouts.app')
@section('title','Expenses')
@section('heading','School Expenses')
@section('content')
<div class="mb-4 grid gap-3 sm:grid-cols-2">
<div class="stat-card"><div class="text-sm text-slate-500">This Month</div><div class="text-2xl font-bold">₹{{ number_format($monthlyTotal,2) }}</div></div>
<div class="stat-card"><div class="text-sm text-slate-500">This Year</div><div class="text-2xl font-bold">₹{{ number_format($yearlyTotal,2) }}</div></div>
</div>
<div class="grid gap-4 lg:grid-cols-2 mb-4">
<form method="POST" action="{{ route('expenses.categories.store') }}" class="panel p-4 space-y-2">@csrf
<h3 class="font-semibold">Category</h3>
<input class="form-input" name="name" required>
<input class="form-input" name="code" required>
<button class="btn-brand">Save</button>
</form>
<form method="POST" action="{{ route('expenses.store') }}" class="panel p-4 space-y-2">@csrf
<h3 class="font-semibold">Record Expense</h3>
<select class="form-input" name="expense_category_id">@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select>
<input class="form-input" name="title" required>
<textarea class="form-input" name="description" rows="2"></textarea>
<input class="form-input" type="number" step="0.01" name="amount" required>
<input class="form-input" type="date" name="expense_date" value="{{ now()->format('Y-m-d') }}" required>
<select class="form-input" name="payment_method">@foreach(['cash','card','upi','bank_transfer','cheque'] as $m)<option value="{{ $m }}">{{ strtoupper($m) }}</option>@endforeach</select>
<button class="btn-brand">Save Expense</button>
</form>
</div>
<div class="panel"><div class="table-wrap"><table class="data"><thead><tr><th>Date</th><th>Title</th><th>Category</th><th>Amount</th><th></th></tr></thead><tbody>
@foreach($expenses as $expense)
<tr>
<td>{{ $expense->expense_date->format('d M Y') }}</td>
<td>{{ $expense->title }}</td>
<td>{{ $expense->category->name }}</td>
<td>₹{{ number_format($expense->amount,2) }}</td>
<td><form method="POST" action="{{ route('expenses.destroy',$expense) }}" onsubmit="return confirm('Delete expense?')">@csrf @method('DELETE')<button class="text-rose-600 font-semibold">Delete</button></form></td>
</tr>
@endforeach
</tbody></table></div><div class="p-4">{{ $expenses->links() }}</div></div>
@endsection