@extends('adminlte::page')

@section('title', 'Notification')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')


@stop

@section('content')

{{-- tabs switcher ~start --}}
<div class="row  pt-4 justify-content-center">    
    <a class="btn btn-lg btn-app bg-secondary" style="width:130px" href ="{{url('business/orders/details')}}">        
        <i class="fa fa-clock-o"></i> Order Pending
    </a>
    <a class="btn btn-lg btn-app bg-success" style="width:130px" href ="{{url('business/booked/details')}}">        
        <i class="fa fa-check"></i> Order booked
    </a>
    <a class="btn btn-lg btn-app bg-info" style="width:130px" href ="{{url('business/orders/confirm')}}">        
        <i class="fa fa-check-circle-o"></i> Order Confirmation
    </a>
    <a class="btn btn-lg btn-app bg-warning" style="width:130px" href ="{{url('business/ship/confirmation')}}">        
        <i class="fa fa-bell "></i> shipment Notification
    </a>      
</div>
{{-- tabs switcher ~end --}}


<div class="row ">
    <div class="col ">
        <div style="margin: 0.1rem 0; text-align: center" >
            <h3>Shipment Notification</h3>
        </div>
    </div>
</div>

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
        <div class="alert_display">
            @if (request('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{request('success')}}</strong>
            </div>
            @endif
        </div>
        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr class='text-bold bg-info'>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th>PayloadID</th>
                    <th>Shipment ID</th>
                    <!-- <th>Shipment Type</th> -->
                    <th>Operation</th>
                    <th>Order Date</th>
                    <th>Notice Date</th>
                    <th>Shipment Date</th>
                    <th>Delivery Date</th>
                    <th>Status </th>
                </tr>
            </thead>
            <tbody>
          
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script type="text/javascript">
    $(function() {

        $.extend($.fn.dataTable.defaults, {
            pageLength: 50,

        });

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('business.orders.shipment.list') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'order_id',
                    name: 'order_id'
                },
                 {
                    data: 'payload',
                    name: 'payload'
                },
                 {
                    data: 'shipment_id',
                    name: 'shipment_id'
                },
                //  {
                //     data: 'shipment_type',
                //     name: 'shipment_type'
                // },
                
                 {
                    data: 'operation',
                    name: 'operation'
                },
                 
                 {
                    data: 'order_date',
                    name: 'order_date'
                },
                 {
                    data: 'notice_date',
                    name: 'notice_date'
                },
                
                 {
                    data: 'shipment_date',
                    name: 'shipment_date'
                },
                 {
                    data: 'delivery_date',
                    name: 'delivery_date'
                },

                 {
                    data: 'status',
                    name: 'status'
                },
                 
            ]
        });

    });
</script>
@stop