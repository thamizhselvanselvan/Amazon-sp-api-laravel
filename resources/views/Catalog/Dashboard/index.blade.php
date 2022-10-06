@extends('adminlte::page')
@section('title', 'Dashboard')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
    <div class='row'>
        <div class="col-7 text-right">
            <h1 class="mt-0 text-dark font-weight-bold"> Listing Priority Dashboard</h1>
        </div>
        <div class="col-5">
            <h5 class="text-right"><b>Last Update : </b>{{ $FileTime }}</h5>

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

    @php
        $country = ['India Dashboard Count', 'USA Dashboard Count'];
        $header = ['Sold Listing', 'Inventory Listing', 'Unlisted'];
        
    @endphp

    @foreach ($json_arrays as $key1 => $record_array)
        <h3>{{ $country[$key1] }}</h3>
        <div class="row">
            @foreach ($record_array->priority_wise_asin as $key2 => $records)
                <div class="col">
                    <div class="info-box bg-success">

                        <div class="info-box-content text-center">
                            <h4 class="info-box-number text-center">{{ $header[$key2 - 1] }} (P{{ $key2 }})</h4>

                            <div class="info-box-text">
                                <div class="row">
                                    <div class="col-2"> </div>
                                    <div class="col-2 text-left">
                                        <h5> ASIN </h5>
                                    </div>
                                    <div class="col-6 text-right">
                                        <h5> {{ $records }}</h5>
                                    </div>
                                    <div class="col-2"> </div>
                                </div>
                            </div>
                            <div class="info-box-text">
                                <div class="row">
                                    <div class="col-2"> </div>
                                    <div class="col-2 text-left">
                                        <h5> Catalog </h5>
                                    </div>
                                    <div class="col-6 text-right">
                                        <h5> {{ $record_array->catalog->$key2 }}</h5>
                                    </div>
                                    <div class="col-2"> </div>
                                </div>
                            </div>
                            <div class="info-box-text">
                                <div class="row">
                                    <div class="col-2"> </div>
                                    <div class="col-2 text-left">
                                        <h5> Delist </h5>
                                    </div>
                                    <div class="col-6 text-right">
                                        <h5> {{ $record_array->delist_asin->$key2 }}</h5>
                                    </div>
                                    <div class="col-2"> </div>
                                </div>
                            </div>
                            <div class="info-box-text">
                                <div class="row">
                                    <div class="col-2"> </div>
                                    <div class="col-2 text-left">
                                        <h5> Price </h5>
                                    </div>
                                    <div class="col-6 text-right">
                                        <h5> {{ $record_array->catalog_price->$key2 }}</h5>
                                    </div>
                                    <div class="col-2"> </div>
                                </div>
                            </div>
                            <div class="info-box-text">
                                <div class="row">
                                    <div class="col-2"> </div>
                                    <div class="col-2 text-left">
                                        <h5> Unavailable </h5>
                                    </div>
                                    <div class="col-6 text-right">
                                        <h5> {{ $record_array->asin_unavailable->$key2 }}</h5>
                                    </div>
                                    <div class="col-2"> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

@stop
