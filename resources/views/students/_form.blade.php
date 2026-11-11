@php $isEdit = (bool) $student; @endphp
<form method="POST" enctype="multipart/form-data" action="{{ $isEdit ? route('students.update',$student) : route('students.store') }}" class="panel p-5 space-y-5">
@csrf
@if($isEdit) @method('PUT') @endif
<div class="grid gap-4 md:grid-cols-2">
<div><label class="form-label">Full name</label><input class="form-input" name="name" value="{{ old('name', $student->user->name ?? '') }}" required></div>
<div><label class="form-label">Email</label><input class="form-input" type="email" name="email" value="{{ old('email', $student->user->email ?? '') }}" required></div>
<div><label class="form-label">Phone</label><input class="form-input" name="phone" value="{{ old('phone', $student->user->phone ?? '') }}"></div>
<div><label class="form-label">Password {{ $isEdit ? '(optional)' : '' }}</label><input class="form-input" type="password" name="password"></div>
<div><label class="form-label">Admission No</label><input class="form-input" name="admission_no" value="{{ old('admission_no', $student->admission_no ?? '') }}" placeholder="Auto if blank" @if($isEdit) readonly @endif></div>
<div><label class="form-label">Admission Date</label><input class="form-input" type="date" name="admission_date" value="{{ old('admission_date', optional($student->admission_date ?? null)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required></div>
<div><label class="form-label">Academic Session</label><select class="form-input" name="academic_session_id" required>@foreach($sessions as $session)<option value="{{ $session->id }}" @selected(old('academic_session_id', $student->academic_session_id ?? '')==$session->id)>{{ $session->name }}</option>@endforeach</select></div>
<div><label class="form-label">Class</label><select class="form-input" name="school_class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('school_class_id', $student->school_class_id ?? '')==$class->id)>{{ $class->name }}</option>@endforeach</select></div>
<div><label class="form-label">Section</label><select class="form-input" name="section_id" required>@foreach($sections as $section)<option value="{{ $section->id }}" @selected(old('section_id', $student->section_id ?? '')==$section->id)>{{ $section->display_name }}</option>@endforeach</select></div>
<div><label class="form-label">Roll No</label><input class="form-input" name="roll_no" value="{{ old('roll_no', $student->roll_no ?? '') }}"></div>
<div><label class="form-label">Gender</label><select class="form-input" name="gender"><option value="">Select</option>@foreach(['male','female','other'] as $g)<option value="{{ $g }}" @selected(old('gender', $student->gender ?? '')==$g)>{{ ucfirst($g) }}</option>@endforeach</select></div>
<div><label class="form-label">Date of Birth</label><input class="form-input" type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($student->date_of_birth ?? null)->format('Y-m-d')) }}"></div>
<div><label class="form-label">Blood Group</label><input class="form-input" name="blood_group" value="{{ old('blood_group', $student->blood_group ?? '') }}"></div>
<div><label class="form-label">Religion</label><input class="form-input" name="religion" value="{{ old('religion', $student->religion ?? '') }}"></div>
<div class="md:col-span-2"><label class="form-label">Address</label><input class="form-input" name="address" value="{{ old('address', $student->address ?? '') }}"></div>
<div><label class="form-label">City</label><input class="form-input" name="city" value="{{ old('city', $student->city ?? '') }}"></div>
<div><label class="form-label">State</label><input class="form-input" name="state" value="{{ old('state', $student->state ?? '') }}"></div>
<div><label class="form-label">Parent / Guardian</label><select class="form-input" name="parent_id"><option value="">None</option>@foreach($parents as $parent)<option value="{{ $parent->id }}" @selected(old('parent_id', $student?->parents->first()?->id)==$parent->id)>{{ $parent->user->name }}</option>@endforeach</select></div>
@if($isEdit)
<div><label class="form-label">Status</label><select class="form-input" name="status">@foreach(['active','transferred','left','alumni'] as $status)<option value="{{ $status }}" @selected(old('status',$student->status)==$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
<div><label class="form-label">Leaving Date</label><input class="form-input" type="date" name="leaving_date" value="{{ old('leaving_date', optional($student->leaving_date)->format('Y-m-d')) }}"></div>
<div class="md:col-span-2"><label class="form-label">Leaving Reason</label><input class="form-input" name="leaving_reason" value="{{ old('leaving_reason', $student->leaving_reason) }}"></div>
@endif
<div><label class="form-label">Photograph</label><input class="form-input" type="file" name="photo" accept="image/*"></div>
@unless($isEdit)
<div><label class="form-label">Document Title</label><input class="form-input" name="document_title" value="{{ old('document_title') }}"></div>
<div><label class="form-label">Document File</label><input class="form-input" type="file" name="document"></div>
@endunless
</div>
<div class="flex gap-2"><button class="btn-brand">{{ $isEdit ? 'Update Student' : 'Admit Student' }}</button><a href="{{ route('students.index') }}" class="btn-soft">Cancel</a></div>
</form>