@extends('layouts.app')
@section('title','Edit Teacher')
@section('heading','Edit Teacher')
@section('content')
@include('teachers._form',['teacher'=>$teacher])
@endsection
