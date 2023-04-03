@extends('adminlte::page')

@section('title', 'Confirmation')

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
            <h3>Order's Confirmation</h3>
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
                    <th>Order Date</th>
                    <th>Received  ID</th>
                    <th>confirm ID</th>
                    <!-- <th>Shipment Type</th> -->
                    <th>Notice Date</th>
                    <th>Amount</th>
                    <th>Tax</th>
                    <th>Shipping Amount</th>
                    <th>Total Amount</th>
                    <th>Quantity</th>
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
            ajax: "{{ route('business.orders.confirm.list') }}",
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
                    data: 'order_date',
                    name: 'order_date'
                },
                 {
                    data: 'payload',
                    name: 'payload'
                },
                 {
                    data: 'confirm_id',
                    name: 'confirm_id'
                },
                //  {
                //     data: 'shipment_type',
                //     name: 'shipment_type'
                // },
                 {
                    data: 'notice_date',
                    name: 'notice_date'
                },
                 {
                    data: 'amount',
                    name: 'amount'
                },
                 {
                    data: 'tax',
                    name: 'tax'
                },
                 {
                    data: 'shipping_amount',
                    name: 'shipping_amount'
                },
                 {
                    data: 'total_amount',
                    name: 'total_amount'
                },
             
                 {
                    data: 'quantity',
                    name: 'quantity'
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