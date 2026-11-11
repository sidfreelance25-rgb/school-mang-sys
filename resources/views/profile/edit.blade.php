@extends('layouts.app')

@section('title', 'Profile')
@section('heading', 'Profile')
@section('subheading', 'Update your account settings')

@section('content')
<div class="space-y-4 max-w-3xl">
    <div class="panel p-5">
        @include('profile.partials.update-profile-information-form')
    </div>
    <div class="panel p-5">
        @include('profile.partials.update-password-form')
    </div>
    <div class="panel p-5">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
