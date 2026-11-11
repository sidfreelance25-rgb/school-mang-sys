@php $isEdit=(bool)$teacher; @endphp
<form method="POST" action="{{ $isEdit?route('teachers.update',$teacher):route('teachers.store') }}" class="panel p-5 space-y-4">
@csrf @if($isEdit)@method('PUT')@endif
<div class="grid gap-4 md:grid-cols-2">
<div><label class="form-label">Name</label><input class="form-input" name="name" value="{{ old('name',$teacher->user->name ?? '') }}" required></div>
<div><label class="form-label">Email</label><input class="form-input" type="email" name="email" value="{{ old('email',$teacher->user->email ?? '') }}" required></div>
<div><label class="form-label">Phone</label><input class="form-input" name="phone" value="{{ old('phone',$teacher->user->phone ?? '') }}"></div>
<div><label class="form-label">Password</label><input class="form-input" type="password" name="password"></div>
<div><label class="form-label">Employee ID</label><input class="form-input" name="employee_id" value="{{ old('employee_id',$teacher->employee_id ?? '') }}" placeholder="Auto if blank" @if($isEdit) readonly @endif></div>
<div><label class="form-label">Qualification</label><input class="form-input" name="qualification" value="{{ old('qualification',$teacher->qualification ?? '') }}"></div>
<div><label class="form-label">Specialization</label><input class="form-input" name="specialization" value="{{ old('specialization',$teacher->specialization ?? '') }}"></div>
<div><label class="form-label">Joining Date</label><input class="form-input" type="date" name="joining_date" value="{{ old('joining_date', optional($teacher->joining_date ?? null)->format('Y-m-d')) }}"></div>
<div><label class="form-label">Gender</label><select class="form-input" name="gender"><option value="">Select</option>@foreach(['male','female','other'] as $g)<option value="{{ $g }}" @selected(old('gender',$teacher->gender ?? '')==$g)>{{ ucfirst($g) }}</option>@endforeach</select></div>
<div><label class="form-label">Date of Birth</label><input class="form-input" type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($teacher->date_of_birth ?? null)->format('Y-m-d')) }}"></div>
<div class="md:col-span-2"><label class="form-label">Address</label><input class="form-input" name="address" value="{{ old('address',$teacher->address ?? '') }}"></div>
<div><label class="form-label">Emergency Contact</label><input class="form-input" name="emergency_contact" value="{{ old('emergency_contact',$teacher->emergency_contact ?? '') }}"></div>
<div><label class="form-label">Salary</label><input class="form-input" type="number" step="0.01" name="salary" value="{{ old('salary',$teacher->salary ?? '') }}"></div>
@unless($isEdit)
<div><label class="form-label">Assign Classes</label><select class="form-input" name="class_ids[]" multiple size="4">@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select></div>
<div><label class="form-label">Assign Subjects</label><select class="form-input" name="subject_ids[]" multiple size="4">@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select></div>
@endunless
</div>
<button class="btn-brand">{{ $isEdit?'Update Teacher':'Add Teacher' }}</button>
</form>