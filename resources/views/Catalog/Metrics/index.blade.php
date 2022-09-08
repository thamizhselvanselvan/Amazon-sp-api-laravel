@extends('adminlte::page')
@section('title', 'Import')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

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

<!-- <div class="row">
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
</div> -->

<!-- <div class="row">
    <div class="col">
        <h2> IN Asin Priority Wise</h2>
        <div class="row">
            <div class="col">
                <div class="small-box bg-gradient-success text-center">
                    <div class="inner">
                        <div class="row">
                            <h3 class="ml-2">{{ $total_asin }}</h3>
                            <p class="mt-2 ml-4">Priority 1</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="small-box bg-gradient-warning text-center">
                    <div class="inner">
                        <div class="row">
                            <h3 class="ml-2">{{ $total_asin }}</h3>
                            <p class="mt-2 ml-4">Priority 2</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="small-box bg-gradient-info text-center">
                    <div class="inner">
                        <div class="row">
                            <h3 class="ml-2">{{ $total_asin }}</h3>
                            <p class="mt-2 ml-4">Priority 3</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <h2>USA Asin Priority Wise</h2>
        <div class="row">
        <div class="col">
                <div class="small-box bg-gradient-success text-center">
                    <div class="inner">
                        <div class="row">
                            <h3 class="ml-2">{{ $total_asin }}</h3>
                            <p class="mt-2 ml-4">Priority 1</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="small-box bg-gradient-warning text-center">
                    <div class="inner">
                        <div class="row">
                            <h3 class="ml-2">{{ $total_asin }}</h3>
                            <p class="mt-2 ml-4">Priority 2</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="small-box bg-gradient-info text-center">
                    <div class="inner">
                        <div class="row">
                            <h3 class="ml-2">{{ $total_asin }}</h3>
                            <p class="mt-2 ml-4">Priority 3</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<table class="table table-striped table-bordered table-sm text-center">
    <thead>
        <tr class="bg-info">
            <th>Source</th>
            <th>Priority</th>
            <th>Total</th>
            <th>Catalog</th>
            <th>Delist</th>
            <th>Unavailable</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i = 1
        @endphp
        @foreach ($priority_wise as $key => $priority)
        @foreach ($priority as $key2 => $value)
        <tr>
            <td>{{$key2}}</td>
            <td>Priority {{$i}}</td>
            <td>{{$value}}</td>
            <td>{{$Total_catalog[$key][$key2]}}</td>
            <td></td>
            <td></td>
        </tr>
        @if($i == 3)
        @php     
        $i = 0
        @endphp
        @endif
        @php  
            $i = $i+1
        @endphp
        
        @endforeach
        @endforeach
    </tbody>
</table>

@stop
