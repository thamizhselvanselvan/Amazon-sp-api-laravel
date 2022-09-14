@extends('adminlte::page')
@section('title', 'System Setting')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class='row'>
    <div class="col-6 text-right">
        <h1 class="mt-0 text-dark font-weight-bold"> System Setting </h1>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col">
        <div class="alert_display">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- <div class="card "> -->
    <form action="">
        @csrf
        <div class="row">
            <div class="col"></div>
            <div class="col">
                <x-adminlte-input label="Key" type="text" name="key" placeholder="Enter key" ></x-adminlte-input>
                <x-adminlte-input label="value" type="text" name="key_value" placeholder="Enter value" ></x-adminlte-input>
                <x-adminlte-button label="Add System Setting" theme="Secondary" icon="fas fa-add"></x-adminlte-button>
            </div>
            <div class="col"></div>
        </div>
    </form>
<!-- </div> -->
@stop