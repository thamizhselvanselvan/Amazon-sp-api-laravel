@extends('adminlte::page')

@section('title', 'SP API Price Push')

@section('css')

@stop
@section('content_header')
    <div class="row">
        <div class="col">

            <h1 class="m-0 text-dark">SP API Price Push</h1>
        </div>
    </div>
@stop

@section('content')
    <table class="table table-striped yajra-datatable table-bordered text-center table-sm">

        <thead class="table-info">
            <th>Id</th>
            <th>Store ID</th>
            <th>Product SKU</th>
            <th>Push Price</th>
            <th>Base Price </th>
            <th>Latency</th>
        </thead>

    </table>
@stop


@section('js')
    <script>
        let yajra_table = $('.yajra-datatable').DataTable({

            processing: true,
            serverSide: true,
            ajax: "{{ url('/buybox/sp_api_push') }}",
            pageLength: 50,
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'store_id',
                    name: 'store_id'
                },
                {
                    data: 'product_sku',
                    name: 'product_sku',
                },
                {
                    data: 'push_price',
                    name: 'push_price',
                },
                {
                    data: 'base_price',
                    name: 'base_price',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'latency',
                    name: 'latency',
                    orderable: false,
                    searchable: false
                },
            
            ],
        });
    </script>
@stop