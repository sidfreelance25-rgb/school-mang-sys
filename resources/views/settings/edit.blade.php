@extends('layouts.app')
@section('title','Settings')
@section('heading','School Settings')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ route('settings.update') }}" class="panel p-5 mb-4 grid gap-4 md:grid-cols-2">@csrf @method('PUT')
<div class="md:col-span-2"><label class="form-label">School Name</label><input class="form-input" name="name" value="{{ old('name',$school->name) }}" required></div>
<div class="md:col-span-2"><label class="form-label">Address</label><input class="form-input" name="address" value="{{ old('address',$school->address) }}"></div>
<div><label class="form-label">City</label><input class="form-input" name="city" value="{{ old('city',$school->city) }}"></div>
<div><label class="form-label">State</label><input class="form-input" name="state" value="{{ old('state',$school->state) }}"></div>
<div><label class="form-label">Postal Code</label><input class="form-input" name="postal_code" value="{{ old('postal_code',$school->postal_code) }}"></div>
<div><label class="form-label">Phone</label><input class="form-input" name="phone" value="{{ old('phone',$school->phone) }}"></div>
<div><label class="form-label">Email</label><input class="form-input" type="email" name="email" value="{{ old('email',$school->email) }}"></div>
<div><label class="form-label">Website</label><input class="form-input" name="website" value="{{ old('website',$school->website) }}"></div>
<div><label class="form-label">Attendance Start Hour</label><input class="form-input" type="number" name="attendance_start_hour" value="{{ old('attendance_start_hour',$school->attendance_start_hour) }}"></div>
<div><label class="form-label">Grading System</label><select class="form-input" name="grading_system"><option value="percentage" @selected($school->grading_system==='percentage')>Percentage</option><option value="gpa" @selected($school->grading_system==='gpa')>GPA</option></select></div>
<div class="md:col-span-2"><label class="form-label">Working Days</label><div class="flex flex-wrap gap-3">@foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="working_days[]" value="{{ $day }}" @checked(in_array($day, $school->working_days ?? [], true))> {{ ucfirst($day) }}</label>@endforeach</div></div>
<div class="md:col-span-2"><label class="form-label">Fee Notes</label><textarea class="form-input" name="fee_notes" rows="3">{{ old('fee_notes',$school->fee_notes) }}</textarea></div>
<div><label class="form-label">Logo</label><input class="form-input" type="file" name="logo" accept="image/*"></div>
<button class="btn-brand">Save Settings</button>
</form>
<div class="grid gap-4 lg:grid-cols-2">
<form method="POST" action="{{ route('settings.users.store') }}" class="panel p-5 space-y-3">@csrf
<h3 class="font-semibold">Create User</h3>
<input class="form-input" name="name" placeholder="Name" required>
<input class="form-input" type="email" name="email" placeholder="Email" required>
<input class="form-input" name="phone" placeholder="Phone">
<select class="form-input" name="role">@foreach($roles as $role)<option value="{{ $role->value }}">{{ $role->label() }}</option>@endforeach</select>
<input class="form-input" type="password" name="password" placeholder="Password" required>
<button class="btn-brand">Create User</button>
</form>
<div class="panel"><div class="table-wrap"><table class="data"><thead><tr><th>User</th><th>Role</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($users as $user)
<tr>
<td>{{ $user->name }}<div class="text-xs text-slate-500">{{ $user->email }}</div></td>
<td>{{ $user->role->label() }}</td>
<td><span class="badge {{ $user->is_active?'badge-success':'badge-danger' }}">{{ $user->is_active?'Active':'Inactive' }}</span></td>
<td><form method="POST" action="{{ route('settings.users.toggle',$user) }}">@csrf<button class="text-teal-700 font-semibold">Toggle</button></form></td>
</tr>
@endforeach
</tbody></table></div><div class="p-4">{{ $users->links() }}</div></div>
</div>
@endsection