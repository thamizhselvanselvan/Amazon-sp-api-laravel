@extends('adminlte::page')
@section('title', 'Import')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class='row'>
    <div class="col-12 text-center">
        <h1 class="m-0 text-dark font-weight-bold"> Dashboard</h1>
    </div>
    <h2 class='text-right col'>

    </h2>
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
@php
$country = ['INDIA', 'USA'];
@endphp

@foreach ($json_arrays as $key1 => $record_array)

<h3>{{$country[$key1]}}</h3>

<div class="row">
    <div class="col">
        <div class="info-box bg-success">
            <div class="info-box-content text-center">
                <h4 class="info-box-number text-center">Process Type</h4>
                <h5 class="info-box-text">ASIN</h5>
                <h5 class="info-box-text">Catalog</h5>
                <h5 class="info-box-text">Delist</h5>
                <h5 class="info-box-text">Price</h5>
            </div>
        </div>
    </div>
    @foreach ($record_array->priority_wise_asin as $key2 => $records)
        <div class="col">
            <div class="info-box bg-success">
                <div class="info-box-content text-center">
                    <h4 class="info-box-number text-center">Priority {{$key2}}</h4>
                    <h5 class="info-box-text">{{$records}}</h5>
                    <h5 class="info-box-text">{{ $record_array->catalog->$key2 }}</h5>
                    <h5 class="info-box-text">{{ $record_array->delist_asin->$key2 }}</h5>
                    <h5 class="info-box-text">{{ $record_array->catalog_price->$key2 }}</h5>
                </div>
            </div>
        </div>
    @endforeach
    
</div>
@endforeach

@stop