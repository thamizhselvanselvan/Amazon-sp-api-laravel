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
            
            <div >
                <h4>Currency :- {{ $view->currency }} </h4>
                <h4>Shipment ID :- {{ $view->ship_id }} </h4>
                <h4>warehouse :- {{ $view->warehouses->name }} </h4>
                <h4>warehouse :- {{ $view->vendors->name }} </h4>
            </div>

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <!-- <th>sl</th> -->
                        <th>asin</th>
                        <th>item name</th>
                        <th>quantity</th>
                        <th>Price</th>
                      
                    </tr>
                </thead>
                <tbody>
                    @php
                        $data = json_decode($view['items'], true);
                        
                        $data = (count($data) > 0) ? $data : [];
                       
                    @endphp
                    @foreach ($data as $key => $val)

                        <tr>
                            <!-- <td>01</td> -->
                            <td>{{$val['asin']}}</td>
                            <td>{{$val['item_name'][0]}}</td>
                            <td>{{$val['quantity'][0]}}</td>
                            <td>{{$val['price'][0]}}</td>
                        </tr>
                        
                    @endforeach
                </tbody>
            </table>
          
        </div>
    </div>
@stop
