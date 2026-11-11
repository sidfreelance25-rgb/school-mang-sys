@extends('layouts.app')
@section('title',$parent->user->name)
@section('heading',$parent->user->name)
@section('subheading','Parent profile')
@section('content')
<div class="mb-4"><a href="{{ route('parents.edit',$parent) }}" class="btn-brand">Edit</a></div>
<div class="grid gap-4 lg:grid-cols-2">
<div class="panel p-5 space-y-2">
<div>{{ $parent->user->email }}</div>
<div>{{ $parent->user->phone }}</div>
<div>{{ $parent->occupation ?: 'Occupation not set' }}</div>
<div>{{ $parent->address }}</div>
</div>
<div class="panel p-5">
<h3 class="font-semibold mb-3">Children</h3>
@foreach($parent->students as $child)
<div class="mb-3 rounded-xl border border-slate-200 p-3">
<div class="font-semibold">{{ $child->user->name }}</div>
<div class="text-sm text-slate-500">{{ $child->admission_no }} · {{ $child->full_class }}</div>
<a class="text-sm font-semibold text-teal-700" href="{{ route('students.show',$child) }}">Open profile</a>
</div>
@endforeach
</div>
</div>
@endsection