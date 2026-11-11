@extends('layouts.app')
@section('title','Edit Parent')
@section('heading','Edit Parent')
@section('content')
@include('parents._form',['parent'=>$parent])
@endsection
