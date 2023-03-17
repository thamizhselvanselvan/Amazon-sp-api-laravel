@extends('adminlte::page')

@section('title', 'Dashboard')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<h1 class="m-0 text-dark">Admin Dashboard</h1>
@stop
@section('content')
@include('sweetalert::alert')


@stop