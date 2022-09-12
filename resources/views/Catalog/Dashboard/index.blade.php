@extends('adminlte::page')
@section('title', 'Import')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class='row'>
    <div class="col-12 text-center">
        <h1 class="mt-0 text-dark font-weight-bold"> Dashboard</h1>
    </div>
</div>
<h5 class="text-right"><b>Last Update : </b>{{$FileTime}}</h5>
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
    @foreach ($record_array->priority_wise_asin as $key2 => $records)
    <div class="col">
        <div class="info-box bg-success">

            <div class="info-box-content text-center">
                <h4 class="info-box-number text-center">Priority {{$key2}}</h4>

                <div class="info-box-text">
                    <div class="row">
                        <div class="col-4"> </div>
                        <div class="col-2 text-left">
                            <h5> ASIN </h5>
                        </div>
                        <div class="col-2 text-right">
                            <h5> {{$records}}</h5>
                        </div>
                        <div class="col-4"> </div>
                    </div>
                </div>
                <div class="info-box-text">
                    <div class="row">
                        <div class="col-4"> </div>
                        <div class="col-2 text-left">
                            <h5> Catalog </h5>
                        </div>
                        <div class="col-2 text-right">
                            <h5> {{ $record_array->catalog->$key2 }}</h5>
                        </div>
                        <div class="col-4"> </div>
                    </div>
                </div>
                <div class="info-box-text">
                    <div class="row">
                        <div class="col-4"> </div>
                        <div class="col-2 text-left">
                            <h5> Delist </h5>
                        </div>
                        <div class="col-2 text-right">
                            <h5> {{ $record_array->delist_asin->$key2 }}</h5>
                        </div>
                        <div class="col-4"> </div>
                    </div>
                </div>
                <div class="info-box-text">
                    <div class="row">
                        <div class="col-4"> </div>
                        <div class="col-2 text-left">
                            <h5> Price </h5>
                        </div>
                        <div class="col-2 text-right">
                            <h5> {{ $record_array->catalog_price->$key2 }}</h5>
                        </div>
                        <div class="col-4"> </div>
                    </div>
                </div>
                <!-- <div class="info-box-text">
                    <div class="row">
                        <div class="col-4"> </div>
                        <div class="col-2 text-left">
                            <h5> BB_Price </h5>
                        </div>
                        <div class="col-2 text-right">
                            <h5> {{ $record_array->bb_price->$key2 }}</h5>
                        </div>
                        <div class="col"> </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
    @endforeach
</div>
@endforeach

@stop