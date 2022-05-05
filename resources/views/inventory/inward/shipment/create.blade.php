@extends('adminlte::page')

@section('title', 'create Shipment')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop
@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Create Shipment</h1>
</div>
<div class="row">
    <div class="col-2">
        <x-adminlte-input label="" name=""  id="asin"type="text" placeholder=" Enter Asin" />
    </div>
</div>

@stop