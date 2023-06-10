@extends('adminlte::page')

@section('title', 'SNT Inward')

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
    <div class="row">
        <div class="col-2">
            <div style="margin-top:-0.1rem">
                <a href="{{ route('shipntrack.export') }}">
                    <x-adminlte-button label="Back" class="btn-sm" theme="primary" icon="fa fa-arrow-left" />
                </a>
            </div>
        </div>
        <div class="col-2"></div>
        <div class="col-6">

            <h1 class="m-0 text-dark">Shipntrack Export Manifest</h1>
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
                @if ($message = Session::get('error'))
                    <div class=" alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif

                <div class="alert_display success">
                    @if (request('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ request('success') }}</strong>
                        </div>
                    @endif
                </div>

            </div>

            <div id="showTable" class="">
                <table class='table table-bordered yajra-datatable table-striped text-center'>
                    <thead>
                        <tr class="table-info">
                            <th>ID</th>
                            <th>Manifest id</th>
                            <th>AWB</th>
                            <th>International awb number</th>
                            <th>Destination</th>
                            <th>Inscan manifest id</th>
                            <th>Order id</th>
                            <th>Purchase tracking id</th>
                            <th>Forwarder 1</th>
                            <th>Forwarder 1 awb</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>{{ $val->id }}</td>
                                <td>{{ $val->manifest_id}}</td>
                                <td>{{ $val->awb}}</td>
                                <td>{{ $val->international_awb_number }}</td>
                                <td>{{ $val->destination}}</td>
                                <td>{{ $val->inscan_manifest_id}}</td>
                                <td>{{ $val->order_id}}</td>
                                <td>{{ $val->purchase_tracking_id}}</td>
                                <td>{{ $val->CourierPartner1->user_name}}</td>
                                <td>{{ $val->forwarder_1_awb}}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </div>
@stop

