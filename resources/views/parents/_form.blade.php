@php $isEdit=(bool)$parent; @endphp
<form method="POST" action="{{ $isEdit?route('parents.update',$parent):route('parents.store') }}" class="panel p-5 space-y-4">
@csrf @if($isEdit)@method('PUT')@endif
<div class="grid gap-4 md:grid-cols-2">
<div><label class="form-label">Name</label><input class="form-input" name="name" value="{{ old('name',$parent->user->name ?? '') }}" required></div>
<div><label class="form-label">Email</label><input class="form-input" type="email" name="email" value="{{ old('email',$parent->user->email ?? '') }}" required></div>
<div><label class="form-label">Phone</label><input class="form-input" name="phone" value="{{ old('phone',$parent->user->phone ?? '') }}"></div>
<div><label class="form-label">Password</label><input class="form-input" type="password" name="password"></div>
<div><label class="form-label">Occupation</label><input class="form-input" name="occupation" value="{{ old('occupation',$parent->occupation ?? '') }}"></div>
<div><label class="form-label">Relation</label><input class="form-input" name="relation" value="{{ old('relation',$parent->relation ?? 'guardian') }}"></div>
<div class="md:col-span-2"><label class="form-label">Address</label><input class="form-input" name="address" value="{{ old('address',$parent->address ?? '') }}"></div>
<div><label class="form-label">Alternate Phone</label><input class="form-input" name="alternate_phone" value="{{ old('alternate_phone',$parent->alternate_phone ?? '') }}"></div>
<div class="md:col-span-2"><label class="form-label">Link Children</label><select class="form-input" name="student_ids[]" multiple size="6">@foreach($students as $student)<option value="{{ $student->id }}" @selected(collect(old('student_ids',$parent?->students->pluck('id')->all() ?? []))->contains($student->id))>{{ $student->user->name }} ({{ $student->admission_no }})</option>@endforeach</select></div>
</div>
<button class="btn-brand">{{ $isEdit?'Update Parent':'Create Parent' }}</button>
</form>