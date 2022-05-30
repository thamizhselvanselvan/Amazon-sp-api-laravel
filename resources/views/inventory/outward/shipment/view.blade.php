@extends('adminlte::page')

@section('title', 'Outwarding')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
    <h1 class="m-0 text-dark">Inventory Outwarding</h1>
    <div class="row">
        <div class="col">
            <a href="{{ route('outwardings.index') }}" class="btn btn-primary btn-sm">
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
            <h4 style="font-family:Times New Roman;">Shipment ID : {{ $outview->ship_id }} </h4>
            <h4 style="font-family:Times New Roman;">Warehouse : {{ $outview->warehouses->name }} </h4>
            <h4 style="font-family:Times New Roman;">Destination : {{ $outview->vendors->name }} </warehouse>
            <h4 style="font-family:Times New Roman;">Currency : {{ $outview->currency }} </h4>
            </div>

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                       
                        <th>ASIN</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                      
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
                        </tr>
                        
                    @endforeach
                </tbody>
            </table>
          
        </div>
    </div>
@stop
