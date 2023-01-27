@extends('adminlte::page')

@section('title', 'Stores')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 1;
        padding-left: 1px;
    }

    .table th {
        padding: 2;
        padding-left: 5px;
    }
</style>
@stop

@section('content_header')

<div class="row">
    <div style="margin-top: 0.4rem;">
        <h3 class="m-0 text-dark font-weight-bold">
            Select Store: &nbsp;
        </h3>

    </div>
    <div style="margin-top: -1.2rem;">
        <x-adminlte-select name="store_select" id="store_select" label="">
            <option value="">Select Store</option>
            @foreach($stores as $store)
            <option value="{{$store->seller_id}}">{{$store->store_name}}</option>
            @endforeach
        </x-adminlte-select>
    </div>
    <div class="col-3">
        <h2>
            <x-adminlte-button type="button" label="Update" theme="primary" icon="fas fa-refresh" id="update_price" />
    </div>
    </h2>


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

        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr class="table-info">
                    <th>ID</th>
                    <th>ASIN</th>
                    <th>Product SKU</th>
                    <th>Store ID</th>
                    <th>Availability</th>
                    <th>Push Price</th>
                    <th>Base Price</th>
                    <th>Latency</th>
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

        $('#update_price').on('click', function() {
            let id = $('#store_select').val();
            window.location = "/stores/listing/price/update/" + id;
        });

        $(document).ready(function() {
            if ($('#store_select').val() == '') {
                $('#update_price').hide();
            }
        });

        $('#store_select').on('change', function() {
            let p = $(this).val();
            window.location = "/stores/listing/price/" + $(this).val();
        });

        $.extend($.fn.dataTable.defaults, {
            pageLength: 100,
        });


        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,

            ajax: {
                url: "{{ url($url) }}",
                type: 'get',
                headers: {
                    'content-type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.store_id = $('#store_select').val();
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'asin',
                    name: 'asin'
                },
                {
                    data: 'product_sku',
                    name: 'product_sku'
                },
                {
                    data: 'store_id',
                    name: 'store_id'
                },
                {
                    data: 'availability',
                    name: 'availability'
                },
                {
                    data: 'push_price',
                    name: 'push_price'
                },
                {
                    data: 'base_price',
                    name: 'base_price'
                },
                {
                    data: 'latency',
                    name: 'latency'
                },

            ]
        });

    });
</script>
@stop