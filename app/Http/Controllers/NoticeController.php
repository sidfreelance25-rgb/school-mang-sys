<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public function index(Request $request): View
    {
        $notices = Notice::query()
            ->with('creator')
            ->when($request->string('type')->toString(), fn ($q, $type) => $q->where('type', $type))
            ->latest('publish_date')
            ->paginate(15)
            ->withQueryString();

        return view('notices.index', compact('notices'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'type' => ['required', Rule::in(['general', 'academic', 'holiday', 'event', 'circular'])],
            'audience' => ['required', Rule::in(['all', 'students', 'teachers', 'parents', 'staff'])],
            'is_important' => ['nullable', 'boolean'],
            'publish_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:publish_date'],
        ]);

        Notice::query()->create([
            ...$data,
            'is_important' => (bool) ($data['is_important'] ?? false),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('notices.index')->with('success', 'Notice published.');
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        $notice->delete();

        return back()->with('success', 'Notice deleted.');
    }
}
