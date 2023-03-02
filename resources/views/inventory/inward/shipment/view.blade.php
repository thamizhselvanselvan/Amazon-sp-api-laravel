@extends('adminlte::page')

@section('title', 'Inwardings')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;
    }

    .table th {
        padding: 2;
        padding-left: 5px;
    }

    footer {
        display: none;
    }
</style>
@stop

@section('content_header')
<div class="row justify-content-center">
    <h1 class="m-0 text-dark">Inventory Inwarding's</h1>
</div>
<div class="col">
    <a href="{{ route('shipments.index') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-long-arrow-alt-left"></i> Back
    </a>
    <a href="#" class="btn btn-primary btn-sm" id="printinv">
        <i class="fas fa-print"></i> Print
    </a>


</div>
@stop


@section('content')
@php

@endphp



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

        <div class="row">
            <div class="col-5">
                <h4 style="font-family:Times New Roman ;">Shipment ID : {{ $id }} </h4>
            </div>
            <div class="col-5">
                <h4></h4>
            </div>
            <div class="col-2">
                <h4>{!! $bar_code !!}</h4>
            </div>
        </div>


        <h4 style="font-family:Times New Roman;"> Warehouse : {{ $warehouse_name }} </h4>
        <h4 style="font-family:Times New Roman;"> Source : {{implode('|',array_unique($vendor_name)); }} </h4>
        <h4 style="font-family:Times New Roman ;">Currency : {{ $currency_array[$currency_id]}} </h4>
        <h6></h6>
    </div>

    <table class="table table-bordered yajra-datatable table-striped">
        <thead>
            <tr class="table-info">
                <th>ASIN</th>
                <th>Item Name</th>
                <th>Inventory ID</th>
                <th>Tag</th>
                <th>Quantity</th>
                <th>Price</th>

            </tr>
        </thead>
        <tbody>
            @foreach($view as $val)
            <tr>
                <td>{{$val['asin']}}</td>
                <td>{{$val['item_name']}}</td>
                <td>{{$val['id']}}</td>
                @if(isset($val->tags))
                <td>{{$val->tags->name}}</td>
                @else
                <td>NA</td>
                @endif

                <td>{{$val['quantity']}}</td>
                <td>{{$val['price']}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
</div>
@stop

@section('js')
<script type="text/javascript">
    $('#printinv').on('click', function() {
        window.print();

    });
</script>
@stop