@extends('layouts.app')
@section('title','Edit Student')
@section('heading','Edit Student')
@section('subheading',$student->user->name)
@section('content')
@include('students._form', ['student' => $student])
@endsection