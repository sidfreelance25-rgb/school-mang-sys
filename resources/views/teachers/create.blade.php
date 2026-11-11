@extends('layouts.app')
@section('title','Add Teacher')
@section('heading','Add Teacher')
@section('content')
@include('teachers._form',['teacher'=>null])
@endsection
