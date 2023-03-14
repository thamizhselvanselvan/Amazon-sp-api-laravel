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
        background-color: #20262E;
        /* Not working */
    }
</style>
@stop

@section('content_header')

<div class="row">
    <div style="margin-top: 0.4rem;">
        <h3 class="m-0 text-dark font-weight-bold">
            Select Region: &nbsp;
        </h3>
    </div>
    <div class="col-1.5 region">
        <div style="margin-top: -1.2rem;" class="">
            <x-adminlte-select name="region_select" id="region_select" label="">
                <option value="">Select Region</option>
                <option value="IN">IN</option>
                <option value="AE">AE</option>
                <option value="SA">SA</option>
            </x-adminlte-select>
        </div>
    </div>

    &nbsp; &nbsp; &nbsp;
    <div style="margin-top: 0.4rem;">
        <h3 class="m-0 text-dark font-weight-bold select_store d-none">
            Select Store: &nbsp;
        </h3>
    </div>
    <div class="col-1.5 region d-none">
        <div style="margin-top: -1.2rem;">
            <x-adminlte-select name="store_select" id="store_select" label="">
                <option value="">Select Store</option>

            </x-adminlte-select>
        </div>
    </div>
    <div class="col text-right buttons d-none">
        <h2>
            {{-- <x-adminlte-button type="button" label="Update" theme="primary" icon="fas fa-refresh" id="update_price" /> --}}
            <x-adminlte-button type="button" label="Export CSV" theme="primary" icon="fas fa-file-export" id="store_data_export" />
            <x-adminlte-button type="button" label="Show Exported CSV" theme="success" icon="fas fa-eye " id="show_exported_csv" />
        </h2>
    </div>
</div>

<div class="modal fade" id="show_exported_csv_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Stores Exported CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <div class="modal-body" style="font-size:15px">

                <div class="show_exported_csv_modal_body">
                    <!-- 
                    @foreach ($new_files as $file)
                    <a href="{{ url('') }}{{ $file }}">{{ basename($file) }}</a>
                    @endforeach -->

                </div>
            </div>

        </div>
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
                    <th title="Excel Formula Price">Excel</th>
                    <th title="Base from Excel Price">Base</th>
                    <th title="Ceil from Excel Price">Ceil</th>
                    <th title="Next Highest Price">NHS</th>
                    <th title="Next Lowest Price">NLS</th>
                    <th title="SP API Push Price">Push Price</th>
                    <th title="Current BB Seller Name/ID">BB Seller</th>
                    <th title="Next Highest Seller Name/ID">Highest Name</th>
                    <th title="Next Lowest Seller Name/ID">Lowest Name</th>
                    <th>Action</th>
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

        $('#region_select').on('change', function() {
            let region = $(this).val();
            if (region != '') {
                $('.region').removeClass("d-none")
                $('.select_store').removeClass("d-none")
            }

            $.ajax({
                url: "/stores/region/fetch",
                method: "GET",
                data: {
                    "region": region,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    $('#store_select').empty();
                    let store_data = '<option >Select Store</option>';
                    $.each(response, function(i, response) {
                        store_data += "<option value='" + response.seller_id + "'>" + response.store_name + "</option>";
                    });
                    $('#store_select').append(store_data);
                },
                error: function(response) {
                    console.log(response);
                },
            });
        });


        $('#store_select').on('change', function() {

            $('.table').DataTable().ajax.reload();
            $('.buttons').removeClass("d-none")
        });

        $(document).ready(function() {

            if ($('#store_select').val() == '') {
                //$('#update_price').hide();
            }

            $(document).on('click', ".pop_over", function(e) {

                let pop_over = $('.pop_over_data');

                $('.pop_over_data').each(function() {

                    if (!$(this).hasClass('d-none')) {
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

        $("#show_exported_csv").on("click", function() {

            $("#show_exported_csv_modal").modal("show");

        });

        $("#store_data_export").on("click", function() {
            let self = $(this);

            let store_id = $('#store_select').val();
            
            if (store_id == '') {
                alert("please select a store");
                return false;
            } else if (store_id == 'Select Store') {
                alert("please select a store ");
                return false;
            }
            
            self.prop('disabled', true);

            $.ajax({
                url: "/stores/listing/price/store_data_export",
                method: "POST",
                data: {
                    store_id: store_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    self.prop("disabled", false);

                    if (response == "success") {
                        alert("Exporting the data ...");
                    }

                    if (response == "error") {
                        alert("Please select any store id ");
                    }
                }
            });

        });
        //show exports and Download
        $('#show_exported_csv').on('click', function() {

            $.ajax({
                url: "/stores/price/file/get",
                method: "GET",
                data: {
                    "path": "public/product_push",
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    if (response == '') {
                        $('.show_exported_csv_modal_body').empty();
                        $('.show_exported_csv_modal_body').append('File Downloading or No File Found..');
                        return false;
                    } else {
                        $('.show_exported_csv_modal_body').empty();
                        let files = '';
                        $.each(response, function(index, result) {

                            files += "<li class='p-0 m-0'>";
                            files += "<a href='/stores/price/file/download/" + index + "'>" +
                                index + '&nbsp; ' + "</a>";
                            files += result;

                            files += "</li>";

                        });
                        $('.show_exported_csv_modal_body').append(files);
                    }
                },
                error: function(response) {
                    console.log(response);
                },
            });
        });
        // $('#store_select').on('change', function() {

        //     let p = $(this).val();
        //     window.location = "/stores/listing/price/" + $(this).val();
        // });

        $('.price_process').on('click', function() {
            let self = $(this);
            let asin = self.attr("asin");
            let productsku = self.attr("productsku");
            let pushprice = self.attr("pushprice");
            let storeid = self.attr("storeid");
            let id = self.attr("data-id");
            let base_price = self.attr("base_price");

            alert("Work In Progress");
            return false;

            self.prop('disabled', true);

            $.ajax({
                url: "/stores/listing/price/price_push_update",
                method: "POST",
                data: {
                    id: id,
                    asin: asin,
                    productsku: productsku,
                    pushprice: pushprice,
                    storeid: storeid,
                    base_price: base_price,
                    "_token": "{{ csrf_token() }}",
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'asin',
                        name: 'asin',
                    },
                    {
                        data: 'product_sku',
                        name: 'product_sku',

                    },
                    {
                        data: 'current_store_price',
                        name: 'current_store_price',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'bb_winner_price',
                        name: 'bb_winner_price',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'app_360_price',
                        name: 'app_360_price',
                        orderable: false,
                        searchable: false
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
                        data: 'highest_seller_price',
                        name: 'highest_seller_price',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'lowest_seller_price',
                        name: 'lowest_seller_price',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'push_price',
                        name: 'push_price',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'bb_winner_id',
                        name: 'bb_winner_id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'highest_seller_name',
                        name: 'highest_seller_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'lowest_seller_name',
                        name: 'lowest_seller_name',
                        orderable: false,
                        searchable: false
                    }
                ]
                // if (response.hasOwnProperty("failed")) {
                //     alert("price updated failed");
                // }
                // }
            });
        });

        $.extend($.fn.dataTable.defaults, {
            pageLength: 100,
            orderable: false,
            searchable: false
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
            columns: [{
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
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'bb_winner_price',
                    name: 'bb_winner_price',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'app_360_price',
                    name: 'app_360_price',
                    orderable: false,
                    searchable: false
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
                    data: 'highest_seller_price',
                    name: 'highest_seller_price',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'lowest_seller_price',
                    name: 'lowest_seller_price',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'push_price',
                    name: 'push_price',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'bb_winner_id',
                    name: 'bb_winner_id'
                },
                {
                    data: 'highest_seller_name',
                    name: 'highest_seller_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'lowest_seller_name',
                    name: 'lowest_seller_name',
                    orderable: false,
                    searchable: false
                }
                ,
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });


    });
</script>
@stop