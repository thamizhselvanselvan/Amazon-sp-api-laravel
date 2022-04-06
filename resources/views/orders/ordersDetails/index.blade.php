@extends('adminlte::page')
@section('title', 'Orders Details')

@section('content_header')
<h1 class="m-0 text-dark">Order Details</h1>
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
            <a href="{{route('getOrder.details')}}">
                <x-adminlte-button label="Sync Order Details" theme="primary" icon="fas fa-file-import" />
            </a>
        </h2>
        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Amazon Order Id</th>
                    <th>Purchase Date</th>
                    <th>Last Update Date</th>
                    <th>Order Status</th>
                    <th>Price</th>
                    <th>Number Of Items</th>
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
    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "",
        // ajax: "{{ url('orders/list') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'amazon_order_identifier'
            },
            {
                data: 'purchase_date',
                name: 'purchase_date'
            },
            {
                data: 'last_update_date',
                name: 'last_update_date'
            },
            {
                data: 'order_status',
                name: 'order_status'
            },
            {
                data: 'order_total',
                name: 'order_total',
                orderable: false,
                searchable: false
            },
            {
                data: 'number_of_items_shipped',
                name: 'number_of_items_shipped'
            },


        ]
    });
</script>

@stop