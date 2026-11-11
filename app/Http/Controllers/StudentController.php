<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\AcademicSession;
use App\Models\ParentProfile;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentPromotion;
use App\Models\User;
use App\Support\NumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = Student::query()
            ->with(['user', 'schoolClass', 'section', 'academicSession'])
            ->when($request->string('search')->toString(), function ($query, string $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('admission_no', 'like', "%{$search}%")
                        ->orWhere('roll_no', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->integer('class_id'), fn ($q, $id) => $q->where('school_class_id', $id))
            ->when($request->integer('section_id'), fn ($q, $id) => $q->where('section_id', $id))
            ->when($request->string('status')->toString(), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('students.index', [
            'students' => $students,
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'sections' => Section::query()->with('schoolClass')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('students.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data): void {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password'] ?? 'password'),
                'role' => UserRole::Student,
                'email_verified_at' => now(),
            ]);

            $photo = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo')->store('uploads/students', 'public');
            }

            $student = Student::query()->create([
                'user_id' => $user->id,
                'admission_no' => $data['admission_no'] ?: NumberGenerator::admissionNumber(),
                'admission_date' => $data['admission_date'],
                'academic_session_id' => $data['academic_session_id'],
                'school_class_id' => $data['school_class_id'],
                'section_id' => $data['section_id'],
                'roll_no' => $data['roll_no'] ?? null,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'blood_group' => $data['blood_group'] ?? null,
                'religion' => $data['religion'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'photo' => $photo,
                'status' => 'active',
            ]);

            if (! empty($data['parent_id'])) {
                $student->parents()->syncWithoutDetaching([
                    $data['parent_id'] => ['is_primary' => true],
                ]);
            }

            if ($request->hasFile('document')) {
                $path = $request->file('document')->store('uploads/documents', 'public');
                StudentDocument::query()->create([
                    'student_id' => $student->id,
                    'title' => $data['document_title'] ?? 'Admission Document',
                    'file_path' => $path,
                    'file_type' => $request->file('document')->getClientOriginalExtension(),
                ]);
            }
        });

        return redirect()->route('students.index')->with('success', 'Student admitted successfully.');
    }

    public function show(Request $request, Student $student): View
    {
        $user = $request->user();

        if ($user->isStudent() && $user->student?->id !== $student->id) {
            abort(403);
        }

        if ($user->isParent()) {
            $childIds = $user->parentProfile?->students()->pluck('students.id') ?? collect();
            if (! $childIds->contains($student->id)) {
                abort(403);
            }
        }

        $student->load([
            'user', 'schoolClass', 'section', 'academicSession', 'parents.user',
            'documents', 'feeInvoices.payments', 'attendances' => fn ($q) => $q->latest('date')->limit(30),
            'examResults.examSchedule.subject', 'examResults.examSchedule.examination',
        ]);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $student->load(['user', 'parents']);

        return view('students.edit', array_merge($this->formData(), compact('student')));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $this->validated($request, $student);

        DB::transaction(function () use ($request, $data, $student): void {
            $student->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'is_active' => $data['status'] === 'active',
            ]);

            if (! empty($data['password'])) {
                $student->user->update(['password' => Hash::make($data['password'])]);
            }

            $photo = $student->photo;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo')->store('uploads/students', 'public');
            }

            $student->update([
                'admission_date' => $data['admission_date'],
                'academic_session_id' => $data['academic_session_id'],
                'school_class_id' => $data['school_class_id'],
                'section_id' => $data['section_id'],
                'roll_no' => $data['roll_no'] ?? null,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'blood_group' => $data['blood_group'] ?? null,
                'religion' => $data['religion'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'photo' => $photo,
                'status' => $data['status'],
                'leaving_date' => $data['leaving_date'] ?? null,
                'leaving_reason' => $data['leaving_reason'] ?? null,
            ]);

            if (! empty($data['parent_id'])) {
                $student->parents()->sync([
                    $data['parent_id'] => ['is_primary' => true],
                ]);
            }
        });

        return redirect()->route('students.show', $student)->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->user()->delete();

        return redirect()->route('students.index')->with('success', 'Student removed successfully.');
    }

    public function promoteForm(): View
    {
        return view('students.promote', [
            'students' => Student::query()->active()->with(['user', 'schoolClass', 'section'])->orderBy('admission_no')->get(),
            'sessions' => AcademicSession::query()->orderByDesc('start_date')->get(),
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'sections' => Section::query()->with('schoolClass')->orderBy('name')->get(),
        ]);
    }

    public function promote(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'to_session_id' => ['required', 'exists:academic_sessions,id'],
            'to_class_id' => ['required', 'exists:school_classes,id'],
            'to_section_id' => ['required', 'exists:sections,id'],
            'status' => ['required', Rule::in(['promoted', 'retained'])],
        ]);

        foreach ($data['student_ids'] as $studentId) {
            $student = Student::query()->findOrFail($studentId);

            StudentPromotion::query()->create([
                'student_id' => $student->id,
                'from_session_id' => $student->academic_session_id,
                'to_session_id' => $data['to_session_id'],
                'from_class_id' => $student->school_class_id,
                'to_class_id' => $data['to_class_id'],
                'from_section_id' => $student->section_id,
                'to_section_id' => $data['to_section_id'],
                'status' => $data['status'],
                'promoted_by' => $request->user()->id,
            ]);

            $student->update([
                'academic_session_id' => $data['to_session_id'],
                'school_class_id' => $data['to_class_id'],
                'section_id' => $data['to_section_id'],
            ]);
        }

        return redirect()->route('students.index')->with('success', 'Students promoted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'sessions' => AcademicSession::query()->orderByDesc('start_date')->get(),
            'classes' => SchoolClass::query()->orderBy('sort_order')->get(),
            'sections' => Section::query()->with('schoolClass')->orderBy('name')->get(),
            'parents' => ParentProfile::query()->with('user')->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student?->user_id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => [$student ? 'nullable' : 'nullable', 'string', 'min:6'],
            'admission_no' => ['nullable', 'string', 'max:50', Rule::unique('students', 'admission_no')->ignore($student?->id)],
            'admission_date' => ['required', 'date'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'roll_no' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'religion' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['active', 'transferred', 'left', 'alumni'])],
            'leaving_date' => ['nullable', 'date'],
            'leaving_reason' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:parents,id'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'document' => ['nullable', 'file', 'max:4096'],
            'document_title' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
