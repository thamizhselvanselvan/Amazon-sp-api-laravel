@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<div class='row'>
    <div class="col-12 text-center">
        <h1 class="m-0 text-dark font-weight-bold"> Metrics</h1>
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

<div class="row">
    <div class="col">
        <div class="small-box bg-gradient-success text-center">
            <div class="inner">
                <div class="row">
                    <h3 class="ml-2">{{ $total_asin }}</h3>
                    <p class="mt-2 ml-4">Total ASIN</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="small-box bg-gradient-info text-center">
            <div class="inner">
                <div class="row">
                    <h3 class="ml-2">{{$total_catalog}}</h3>
                    <p class="mt-2 ml-4"> Total Catalog</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
