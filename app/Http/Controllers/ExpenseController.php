<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $expenses = Expense::query()
            ->with(['category', 'recorder'])
            ->when($request->integer('category_id'), fn ($q, $id) => $q->where('expense_category_id', $id))
            ->when($request->date('from'), fn ($q, $from) => $q->whereDate('expense_date', '>=', $from))
            ->when($request->date('to'), fn ($q, $to) => $q->whereDate('expense_date', '<=', $to))
            ->latest('expense_date')
            ->paginate(20)
            ->withQueryString();

        $monthlyTotal = Expense::query()
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $yearlyTotal = Expense::query()
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return view('expenses.index', [
            'expenses' => $expenses,
            'categories' => ExpenseCategory::query()->orderBy('name')->get(),
            'monthlyTotal' => $monthlyTotal,
            'yearlyTotal' => $yearlyTotal,
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:expense_categories,code'],
        ]);

        ExpenseCategory::query()->create($data);

        return back()->with('success', 'Expense category created.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,card,upi,bank_transfer,cheque'],
        ]);

        Expense::query()->create([
            ...$data,
            'recorded_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Expense recorded.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('success', 'Expense deleted.');
    }
}
