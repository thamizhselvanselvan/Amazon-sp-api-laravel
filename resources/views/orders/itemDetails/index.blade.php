@extends('adminlte::page')
@section('title', 'Orders Details')

@section('content_header')
<h1 class="m-0 text-dark">Order Item Details</h1>
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
            <!-- <a href="">
                <x-adminlte-button label="Sync Order Details" theme="primary" icon="fas fa-file-import" />
            </a> -->
        </h2>
        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Amazon Order Id</th>
                    <th>Asin</th>
                    <th>Item Id</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Quantity Ordered</th>
                    <th>Quantity Shipped</th>
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
        ajax: "{{ url('orders/item-details') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'order_identifier',
                name: 'order_identifier'
            },
            {
                data: 'asin',
                name: 'asin'
            },
            {
                data: 'order_item_identifier',
                name: 'order_item_identifier'
            },
            {
                data: 'title',
                name: 'title'
            },
            {
                data: 'item_price',
                name: 'item_price',
                orderable: false,
                searchable: false
            },
            {
                data: 'quantity_ordered',
                name: 'quantity_ordered'
            },
            {
                data: 'quantity_shipped',
                name: 'quantity_shipped'
            },


        ]
    });
</script>

@stop