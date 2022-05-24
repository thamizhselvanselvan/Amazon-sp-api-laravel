@extends('adminlte::page')

@section('title', 'Inwardings')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
    <h1 class="m-0 text-dark">Inventory Inwarding</h1>
    <div class="row">
        <div class="col">
            <a href="{{ route('shipments.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
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
            
            <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr>
                    <th> ID</th>
                    <th>Shipment ID</th>
                    <th>Warehouse</th>
                    <th>Source</th>
                    <th>Currency</th>
                    <th>Items</th>
                    <th>Inwarding Date</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
            
        </div>
    </div>
@stop
