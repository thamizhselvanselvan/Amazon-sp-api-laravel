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

    .product_label {
        font-size: 15px;
        font-weight: 600 !important;
    }

    .pop_over {
        width: 80px;
        cursor: pointer;
    }

    .pop_over_data {
        color: #2c3e50;
        background: #fff;
        width: 260px;
        padding: 10px;
        z-index: 10000;
    }

    .yajra-datatable {
        font-size: 14px;
    }

    table.dataTable tbody tr:hover {
        color: white;
        background-color: #20262E;  /* Not working */
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
                    <option value="{{ $store->seller_id }}" {{ $request_store_id == $store->seller_id ? "selected" : '' }}>{{ $store->store_name }}</option>
                @endforeach
        </x-adminlte-select>
    
    </div>

    <div class="col-3">
        <h2>
            <x-adminlte-button type="button" label="Update" theme="primary" icon="fas fa-refresh" id="update_price" />
        </h2>
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

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr class="table-info">
                        <th>ID</th>
                        <th>ASIN</th>
                        <th>SKU</th>
                        <th title="Current Store Price">SP</th>
                        <th title="Current BB Price">BB</th>
                        <th title="Current BB Seller Name/ID">BB Seller</th>
                        <th title="Next Highest Seller">NHS</th>
                        <th title="Next Highest Seller Name/ID">NHS Name</th>
                        <th title="Next Lowest Price">NLS</th>
                        <th title="Next Lowest Seller Name/ID">NLS Name</th>
                        <th>Base Price</th>
                        <th>Ceil Price</th>
                        <th>Push Price</th>
                        {{-- <th>Action</th> --}}
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

        $(document).ready(function() {

            if ($('#store_select').val() == '') {
                $('#update_price').hide();
            }

            $(document).on('click', ".pop_over", function(e) {

                let pop_over = $('.pop_over_data');

                $('.pop_over_data').each(function() {

                    if(!$(this).hasClass('d-none')) {
                        $(this).addClass('d-none');
                    }

                });

                $(this).find('.pop_over_data').toggleClass('d-none');
            });

            $(document).on('click', function(e) {
                let container = $('.pop_over');
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    $('.pop_over_data').addClass('d-none');
                }
            });

        });

        $('#store_select').on('change', function() {

            let p = $(this).val();
            window.location = "/stores/listing/price/" + $(this).val();
        });

        $('#update_price').on('click', function() {

            let id = $('#store_select').val();
            //window.location = "/stores/listing/price/update/" + id;

            $.ajax({
                url: "/stores/listing/price/updated",
                method: "POST",
                data: { id: id, 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    console.log(response);
                }
            });
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
                    "_token": "{{ csrf_token() }}",
                },
                data: function(d) {
                    d.store_id = $('#store_select').val();
                },
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
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
                    data: 'current_store_price',
                    name: 'current_store_price',
                    // orderable: false,
                    // searchable: false
                },
                {
                    data: 'destination_bb_price',
                    name: 'destination_bb_price'
                },
                {
                    data: 'destination_bb_seller',
                    name: 'destination_bb_seller'
                },
                {
                    data: 'highest_seller_price',
                    name: 'highest_seller_price'
                },
                {
                    data: 'highest_seller_name',
                    name: 'highest_seller_name'
                },
                {
                    data: 'lowest_seller_price',
                    name: 'lowest_seller_price'
                },
                {
                    data: 'lowest_seller_name',
                    name: 'lowest_seller_name'
                },
                {
                    data: 'base_price',
                    name: 'base_price',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'ceil_price',
                    name: 'ceil_price',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'push_price',
                    name: 'push_price'
                },
                // {
                //     data: 'action',
                //     name: 'action',
                //     orderable: false,
                //     searchable: false
                // }
            ]
        });

    });
</script>
@stop