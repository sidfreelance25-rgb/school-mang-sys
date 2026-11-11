@extends('layouts.app')
@section('title','Parents')
@section('heading','Parents')
@section('subheading','Guardian profiles and linked children')
@section('content')
<div class="mb-4 flex justify-between gap-3"><form method="GET" class="flex gap-2"><input class="form-input w-64" name="search" value="{{ request('search') }}"><button class="btn-soft">Search</button></form><a href="{{ route('parents.create') }}" class="btn-brand">Add Parent</a></div>
<div class="panel"><div class="table-wrap"><table class="data"><thead><tr><th>Name</th><th>Contact</th><th>Children</th><th></th></tr></thead><tbody>
@foreach($parents as $parent)
<tr>
<td>{{ $parent->user->name }}<div class="text-xs text-slate-500">{{ $parent->relation }}</div></td>
<td>{{ $parent->user->phone }}<div class="text-xs text-slate-500">{{ $parent->user->email }}</div></td>
<td>{{ $parent->students->pluck('user.name')->join(', ') ?: '-' }}</td>
<td><a class="text-teal-700 font-semibold" href="{{ route('parents.show',$parent) }}">View</a></td>
</tr>
@endforeach
</tbody></table></div><div class="p-4">{{ $parents->links() }}</div></div>
@endsection