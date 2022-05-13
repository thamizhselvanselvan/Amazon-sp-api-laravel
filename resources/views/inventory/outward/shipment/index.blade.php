@extends('adminlte::page')

@section('title', 'Outward Shipment')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
    <h1 class="m-0 text-dark">Outward Shipment</h1>

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
 
            <h2 class="mb-4">
            <a href="{{ route('outwardings.create') }}">
                    <x-adminlte-button label="Create Shipment" theme="primary" icon="fas fa-plus" />
                </a>
            </h2> 

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Shipment ID</th>
                        <th>Destination</th>
                        <!-- <th>Action</th> -->
                       
                    </tr>
                </thead>
                
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop

