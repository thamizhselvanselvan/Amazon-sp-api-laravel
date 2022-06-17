@extends('adminlte::page')

@section('title', 'Outwarding')

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
</style>
@stop

@section('content_header')
<div class="row justify-content-center">
    <h1 class="m-0 text-dark">Inventory Outwarding's</h1>
</div>
<div class="col">
    <a href="{{ route('outwardings.index') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-long-arrow-alt-left"></i> Back
    </a>
    <a href="#" class="btn btn-primary btn-sm" id="print">
        <i class="fas fa-print"></i> Print
    </a>

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
    <div class="col-5">
        <h4 style="font-family:Times New Roman ;">Shipment ID : {{ $outview->ship_id }} </h4>
    </div>
    <div class="col-5">
        <h4></h4>
    </div>
    <div class="col-2">
        <h4>{!! $bar_code !!}</h4>
    </div>
</div>

<h4 style="font-family:Times New Roman;">Warehouse : {{ $outview->warehouses->name }} </h4>
<h4 style="font-family:Times New Roman;">Destination : {{ $outview->vendors->name }}</h4>
<h4 style="font-family:Times New Roman;">Currency : {{ $currency_array[$outview->currency] }} </h4>

<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr>
            <th>ASIN</th>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Storage </th>
        </tr>
    </thead>
    <tbody>
        @php
        $data = json_decode($outview['items'], true);
        $data = (count($data) > 0) ? $data : [];
        @endphp
        @foreach ($data as $key => $val)

        <tr>
            <td>{{$val['asin']}}</td>
            <td>{{$val['item_name']}}</td>
            <td>{{$val['quantity']}}</td>
            <td>{{$val['price']}}</td>
            <td>{{$loc[$key]['rack_id']}}-{{$loc[$key]['shelve_id']}}-{{$loc[$key]['bin_id']}}</td>
            @endforeach
        
        </tr>
    </tbody>
</table>
@stop


@section('js')
<script type="text/javascript">
    $('#print').on('click', function() {
        window.print();

    });
</script>
@stop