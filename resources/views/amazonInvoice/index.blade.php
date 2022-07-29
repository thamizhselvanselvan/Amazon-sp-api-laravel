@extends('adminlte::page')

@section('title', 'Amazon Invoice')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark">Amazon Invoice Management</h1>
    <div class="col text-right">
        <a href='{{route("amazon.invoice.upload")}}'>
            <x-adminlte-button label='Upload Amazon Invoice' class="" theme="primary" icon="fas fa-file-import" />
        </a>
    </div>
</div>
@stop

@section('css')

@endsection

@section('content')
<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr class="length">
            <th>Id</th>
            <th>AWB</th>
            <th>Order Id</th>
            <th>Booking Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

@stop

@section('js')

<script type="text/javascript">
    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('/amazon/invoice') }}",
        columns: [{
                data: 'id',
                name: 'id',
            },
            {
                data: 'awb',
                name: 'awb',
            },
            {
                data: 'amazon_order_identifier',
                name: 'amazon_order_identifier',
                orderable: false
            },
            {
                data: 'booking_date',
                name: 'booking_date',
                orderable: false,
                searchable: false
            },
            {
                data: 'status',
                name: 'status',
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },

        ]
    });
</script>
@stop