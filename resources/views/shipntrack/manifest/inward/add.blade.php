@extends('adminlte::page')

@section('title', 'SNT Inward')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<div class="row">
    <div class="col-0.5">
        <a href="{{route('shipntrack.inward')}}">
            <x-adminlte-button label="Back" class="btn-sm" theme="primary" icon="fas fa-arrow-left" />
        </a>
    </div>
    <div class="col text-center">
        <h1 class="m-0 text-dark">SNT Inward Shipment </h1>
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
<div class="row">
    <div class="col-2">
        <div class="form-group">
            <x-adminlte-select name="mode" label="Source-Destination" id="mode">
                <option value="0">Source-Destination</option>
                @foreach ($destinations as $destination)
                <option value={{$destination['id']}}_{{$destination['destination']}}_{{$destination['process_id']}}>
                    {{ $destination['source'] . '-' . $destination['destination'] }}
                </option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>
    <div class="col-2">
        <div class="form-group awb type d-none">
            <x-adminlte-input label='Enter Tracking ID :' type='text' name='awb' id="awb" placeholder='Enter Tracking ID here..' required />
        </div>
    </div>

    <div class="col text-right">
        <div style="margin-top: 1.8rem;">
            <x-adminlte-button label="Create Manifest" theme="primary" icon="fas fa-plus" id="create" class="btn-sm d-none create_shipmtn_btn" />
        </div>
    </div>
</div>

<br>
<table class="table table-bordered yajra-datatable table-striped d-none" id="report_table">
    <thead>
        <tr class="table-info table  ">
            <th>AWB</th>
            <th>Booking Date</th>
            <th>Consignor</th>
            <th>Consignee</th>
            <th>Order ID</th>
            <th>Tracking ID</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="table_body">
    </tbody>
</table>
@stop
