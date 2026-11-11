<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Support\NumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeeController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = FeeInvoice::query()
            ->with(['student.user', 'academicSession'])
            ->when($request->string('status')->toString(), fn ($q, $status) => $q->where('status', $status))
            ->when($request->string('search')->toString(), function ($query, string $search): void {
                $query->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('student.user', fn ($user) => $user->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('student', fn ($student) => $student->where('admission_no', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('fees.index', [
            'invoices' => $invoices,
            'categories' => FeeCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'structures' => FeeStructure::query()->with(['feeCategory', 'schoolClass', 'academicSession'])->latest()->get(),
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'sessions' => AcademicSession::query()->orderByDesc('start_date')->get(),
            'students' => Student::query()->active()->with('user')->orderBy('admission_no')->get(),
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:fee_categories,code'],
            'description' => ['nullable', 'string'],
        ]);

        FeeCategory::query()->create($data + ['is_active' => true]);

        return back()->with('success', 'Fee category created.');
    }

    public function storeStructure(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fee_category_id' => ['required', 'exists:fee_categories,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'frequency' => ['required', 'in:monthly,quarterly,yearly,one_time'],
        ]);

        FeeStructure::query()->updateOrCreate(
            [
                'fee_category_id' => $data['fee_category_id'],
                'school_class_id' => $data['school_class_id'],
                'academic_session_id' => $data['academic_session_id'],
            ],
            [
                'amount' => $data['amount'],
                'frequency' => $data['frequency'],
            ]
        );

        return back()->with('success', 'Fee structure saved.');
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'fee_structure_id' => ['nullable', 'exists:fee_structures,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'title' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        FeeInvoice::query()->create([
            'invoice_no' => NumberGenerator::invoiceNumber(),
            'student_id' => $data['student_id'],
            'fee_structure_id' => $data['fee_structure_id'] ?? null,
            'academic_session_id' => $data['academic_session_id'],
            'title' => $data['title'],
            'amount' => $data['amount'],
            'paid_amount' => 0,
            'discount' => $data['discount'] ?? 0,
            'due_date' => $data['due_date'] ?? null,
            'status' => 'unpaid',
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Fee invoice created.');
    }

    public function show(FeeInvoice $fee): View
    {
        $fee->load(['student.user', 'payments.receiver', 'academicSession', 'feeStructure.feeCategory']);

        return view('fees.show', ['invoice' => $fee]);
    }

    public function storePayment(Request $request, FeeInvoice $fee): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.$fee->balance],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,card,upi,bank_transfer,cheque'],
            'transaction_ref' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $fee, $request): void {
            FeePayment::query()->create([
                'receipt_no' => NumberGenerator::receiptNumber(),
                'fee_invoice_id' => $fee->id,
                'student_id' => $fee->student_id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'transaction_ref' => $data['transaction_ref'] ?? null,
                'notes' => $data['notes'] ?? null,
                'received_by' => $request->user()->id,
            ]);

            $fee->paid_amount = (float) $fee->paid_amount + (float) $data['amount'];
            $fee->refreshStatus();
        });

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function receipt(FeePayment $payment): View
    {
        $payment->load(['student.user', 'feeInvoice', 'receiver']);

        return view('fees.receipt', compact('payment'));
    }
}
