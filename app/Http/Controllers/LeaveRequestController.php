<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $leaves = LeaveRequest::query()
            ->with(['user', 'reviewer'])
            ->when(! $user->canManageSchool(), fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->paginate(20);

        return view('leaves.index', compact('leaves'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'leave_type' => ['required', 'in:casual,sick,emergency,other'],
            'reason' => ['nullable', 'string'],
        ]);

        LeaveRequest::query()->create([
            ...$data,
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Leave request submitted.');
    }

    public function review(Request $request, LeaveRequest $leave): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        $leave->update([
            'status' => $data['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Leave request '.$data['status'].'.');
    }
}
