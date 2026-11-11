@extends('layouts.app')
@section('title','Admit Student')
@section('heading','Admit Student')
@section('subheading','Register a new student')
@section('content')
@include('students._form', ['student' => null])
@endsection