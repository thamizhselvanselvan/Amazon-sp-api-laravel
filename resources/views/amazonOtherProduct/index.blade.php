@extends('adminlte::page')

@section('title', 'Amazon.com product')

@section('content_header')
    <h1 class="m-0 text-dark">Amazon.com Products</h1>
@stop

@section('css')
    <style>
        .hide_show a {
            margin: 8px;
            cursor: pointer;

        }
    </style>
@endsection
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
            <h2 class="row mb-4 ">
                <div class="col">
                    <x-adminlte-button label='Export Catalog' class="product_export_modal_open" theme="primary"
                        icon="fas fa-file-export" />
                    <a href='asin_upload'>
                        <x-adminlte-button label='Export By ASIN' class="" theme="primary"
                            icon="fas fa-file-import" />
                    </a>
                    <x-adminlte-button label='Download' class="file_download_modal_btn" theme="success"
                        icon="fas fa-download" />
                </div>
                <div class="col"></div>
                <div class="col-3 align-self-end progress_bar">
                    <x-adminlte-progress class='border border-success' id="progresBar" theme="pink" value="0"
                        animated with-label />
                </div>
            </h2>
            <!-- Header Modal -->
            <div class="modal fade" id="productExport" tabindex="-1" role="dialog"
                aria-labelledby="productExportModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="productExportModalLabel">Select Headers</h5>
                            <button type="button" class="close modal_close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body " id="checkboxlist">
                            <div class="form-check d-flex row">
                                <div class="col-12">
                                    <input class="form-check-input all" type="checkbox" value="all" id="all">
                                    <h6>Select All</h6>
                                </div>

                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="hit"
                                        name='options[]' id="hit">
                                    <h6>Hit</h6>
                                </div>

                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="asin"
                                        name='options[]' id="asin">
                                    <h6>Asin</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="sku"
                                        name='options[]' id="sku">
                                    <h6>Sku</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="hs_code"
                                        name='options[]' id="hs_code">
                                    <h6>HS Code</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="gst"
                                        name='options[]' id="gst">
                                    <h6>GST</h6>
                                </div>

                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="update_time"
                                        name='options[]' id="update_time">
                                    <h6>Update Time</h6>
                                </div>
                            </div>

                            <div class="form-check d-flex row">
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="availability"
                                        name='options[]' id="availability">
                                    <h6>Availability</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="price"
                                        name='options[]' id="price">
                                    <h6>Price</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="list_price"
                                        name='options[]' id="list_price">
                                    <h6>List Price</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="price1"
                                        name='options[]' id="price_1">
                                    <h6>Price 1</h6>
                                </div>

                                <div class="col-2">
                                    <input class="form-check-input header_options " type="checkbox" value="price_inr"
                                        name='options[]' id="price_inr">
                                    <h6>Price INR</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="list_price_inr"
                                        name='options[]' id="list_price_inr">
                                    <h6>List Price INR</h6>
                                </div>
                            </div>

                            <div class="form-check d-flex row">
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="price_aed"
                                        name='options[]' id="price_aed">
                                    <h6>Price AED</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="list_price_aed"
                                        name='options[]' id="list_price_aed">
                                    <h6>List Price AED</h6>
                                </div>

                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="image_t"
                                        name='options[]' id="image_t">
                                    <h6>Image T</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="id"
                                        name='options[]' id="id">
                                    <h6>ID</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="title"
                                        name='options[]' id="title">
                                    <h6>Title</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox"
                                        value="shipping_weight" name='options[]' id="shipping_weight">
                                    <h6>Shipping Weight</h6>
                                </div>
                            </div>

                            <div class="form-check d-flex row">

                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="image_p"
                                        name='options[]' id="image_p">
                                    <h6>Image P</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="image_d"
                                        name='options[]' id="image_d">
                                    <h6>Image D</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="all_category"
                                        name='options[]' id="all_category">
                                    <h6>All Category</h6>
                                </div>

                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="category"
                                        name='options[]' id="category">
                                    <h6>Category</h6>
                                </div>

                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="description"
                                        name='options[]' id="description">
                                    <h6>Description</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="flipkart"
                                        name='options[]' id="flipkart">
                                    <h6>Flipkart</h6>
                                </div>
                            </div>

                            <div class="form-check d-flex row">
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="amazon"
                                        name='options[]' id="amazon">
                                    <h6>Amazon</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="length"
                                        name='options[]' id="length">
                                    <h6>Length</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="height"
                                        name='options[]' id="height">
                                    <h6>Height</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="width"
                                        name='options[]' id="width">
                                    <h6>Width</h6>
                                </div>

                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="weight"
                                        name='options[]' id="weight">
                                    <h6>Weight</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="upc"
                                        name='options[]' id="upc">
                                    <h6>UPC</h6>
                                </div>
                            </div>

                            <div class="form-check d-flex row">
                                <div class="col-2 ">
                                    <input class="form-check-input header_options" type="checkbox" value="latency"
                                        name='options[]' id="latency">
                                    <h6>Latency</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="uae_latency"
                                        name='options[]' id="uae">
                                    <h6>UAE Latency</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="b2c_latency"
                                        name='options[]' id="b2c_latency">
                                    <h6>B2C Latency</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="ean"
                                        name='options[]' id="ean">
                                    <h6>EAN</h6>
                                </div>

                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="color"
                                        name='options[]' id="color">
                                    <h6>Color</h6>
                                </div>
                                <div class="ml-1 mr-3">
                                    <input class="form-check-input header_options" type="checkbox" value="manufacturer"
                                        name='options[]' id="manufacturer">
                                    <h6>Manufacturer</h6>
                                </div>
                            </div>

                            <div class="form-check d-flex row">
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="model"
                                        name='options[]' id="model">
                                    <h6>Model</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="mpn"
                                        name='options[]' id="mpn">
                                    <h6>MPN</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="creation_time"
                                        name='options[]' id="creationg_time">
                                    <h6>Creation Time</h6>
                                </div>

                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox" value="page"
                                        name='options[]' id="page">
                                    <h6>Page</h6>
                                </div>
                                <div class="col-2">
                                    <input class="form-check-input header_options" type="checkbox"
                                        value="detail_page_url" name='options[]' id="detail_page_url">
                                    <h6>Detail page URL</h6>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary modal_close"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id='exportToCsv'>Export All Catalog</button>
                            <!-- <button type="button" class="btn btn-primary" id='exportbyAsin'>Export By Asin</button> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Header Modal -->
            <!-- Download Modal -->
            <div class="modal fade" id="file_download_modal" tabindex="-1" role="dialog"
                aria-labelledby="FileDownloadModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Download Amazon Other Products</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="file_download_display">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                id='file_download_modal_close'>Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--end of Download model-->

            <!-- show and hide columns-->
            <Strong> Hide And Show Columns</Strong>
            <div class="hide_show">
                <a class="toggle-vis" data-column="1">HIT</a>
                <a class="toggle-vis" data-column="2">Asin</a>
                <a class="toggle-vis" data-column="3">SKU</a>
                <a class="toggle-vis" data-column="4">HS Code & GST</a>
                <a class="toggle-vis" data-column="5">Update Time</a>
                <a class="toggle-vis" data-column="6">Availability</a>
                <a class="toggle-vis" data-column="7">Price</a>
                <a class="toggle-vis" data-column="8">List Price</a>
                <a class="toggle-vis" data-column="9">Price1 & Price INR</a>
                <a class="toggle-vis" data-column="10">List Price INR</a>
                <a class="toggle-vis" data-column="11">Price AED</a>
                <a class="toggle-vis" data-column="12">List Price AED</a>
                <a class="toggle-vis" data-column="13">Shipping Weight</a>
                <a class="toggle-vis" data-column="14">Image URL</a>
                <a class="toggle-vis" data-column="15">ID</a>
                <a class="toggle-vis" data-column="16">Title</a>
                <a class="toggle-vis" data-column="17">Image P & Image D</a>
                <a class="toggle-vis" data-column="18">Category</a>
                <a class="toggle-vis" data-column="19">All Category</a>
                <a class="toggle-vis" data-column="20">Description</a>
                <a class="toggle-vis" data-column="21">Height, Length & Width</a>
                <a class="toggle-vis" data-column="22">Weight</a>
                <a class="toggle-vis" data-column="23">Flipkart & Amazon</a>
                <a class="toggle-vis" data-column="24">UPC</a>
                <a class="toggle-vis" data-column="25">Manufacturer</a>
                <a class="toggle-vis" data-column="26">Latency</a>
                <a class="toggle-vis" data-column="27">UAE latency & B2C latency</a>
                <a class="toggle-vis" data-column="28">EAN</a>
                <a class="toggle-vis" data-column="29">Color</a>
                <a class="toggle-vis" data-column="30">Model MPN</a>
                <a class="toggle-vis" data-column="31">Detail Page URL</a>
                <a class="toggle-vis" data-column="32">Creation Time</a>
                <a class="toggle-vis" data-column="33">Page</a>
            </div>

            <!-- end of show and hide columns-->
            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>HIT</th>
                        <th>ASIN</th>
                        <th>SKU</th>
                        <th>HS code / GST</th>
                        <th>Update Time</th>
                        <th>Availability</th>
                        <th>Price</th>
                        <th>List Price</th>
                        <th>Price1 / Price INR</th>
                        <th>List Price INR</th>
                        <th>Price AED </th>
                        <th>List price AED</th>
                        <th>Shipping weight</th>
                        <th>Image</th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Image P / Image D</th>
                        <th>Category </th>
                        <th>All category </th>
                        <th>Description </th>
                        <th>Height Length width</th>
                        <th>Weight</th>
                        <th>Flipkart/ Amazon</th>
                        <th>UPC</th>
                        <th>Manufacturer</th>
                        <th>Latency</th>
                        <th>UAE Latency / B2C Latency</th>
                        <th>EAN</th>
                        <th>Color</th>
                        <th>Model / MPN</th>
                        <th>Detail Page URL</th>
                        <th>Creation Time</th>
                        <th>Page</th>
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
        // pusher
        $(document).ready(function() {
            $('.progress_bar').hide();
            let current_url = "{{ Route::current()->getName() }}";
            let p_channel = window.Echo.private("test-broadcast");

            let pBar = new _AdminLTE_Progress('progresBar');
            let v = pBar.setValue(0);
            p_channel.listen(".test-broadcast1", function(data) {

                console.log(data.message);
                if (data.message == 100) {
                    setTimeout(function() {

                        $('.progress_bar').hide();
                        fileDownloadModal();
                        return false;
                    }, 2000);
                }
                let v = pBar.setValue(data.message);
                // console.log(data);
                $('.progress_bar').show();
            });

            $('.progress_bar').hide();

        });
        //end of pusher

        $(".file_download_modal_btn").on('click', function(e) {

            fileDownloadModal();
        });

        function fileDownloadModal() {

            let self = $(this);
            let file_display = $('.file_download_display');
            let file_modal = $("#file_download_modal");

            $.ajax({
                url: "/other_file_download",
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert('Error');
                    }
                    if (response.success) {
                        file_modal.modal('show');
                        let html = '<ul>';
                        if (response.files_lists == '') {
                            html += "<span class ='p-0 m-0'>File Is Downloading, Please Wait... </span>";
                        } else {

                            $.each(response.files_lists, function(index, value) {

                                let file_name = Object.keys(value)[0];
                                let file_time = value[file_name];

                                html += "<li class='p-0 m-0'>";
                                html += "<a href='/other-product/download/" + file_name +
                                    "' class='p-0 m-0'> Part " + parseInt(index + 1) + "</a> ";
                                html += file_time;
                                html += "</li>";

                            });
                            html += '</ul>';
                        }
                        file_display.html(html);
                    }
                }
            });
        }

        let yajra_table = $('.yajra-datatable').DataTable({

            processing: true,
            serverSide: true,

            ajax: "{{ url('other-product/amazon_com') }}",
            pageLength: 100,
            lengthMenu: [50, 100, 200, 500],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'hit',
                    name: 'hit'
                },
                {
                    data: 'asin',
                    name: 'asin'
                },
                {
                    data: 'sku',
                    name: 'sku'
                },
                {
                    data: 'hs_code_gst',
                    name: 'hs_code_gst',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'update_time',
                    name: 'update_time'
                },
                {
                    data: 'availability',
                    name: 'availability'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'list_price',
                    name: 'list_price'
                },
                {
                    data: 'price1_price_inr',
                    name: 'price1_price_inr',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'list_price_inr',
                    name: 'list_price_inr'
                },
                {
                    data: 'price_aed',
                    name: 'price_aed'
                },
                {
                    data: 'list_price_aed',
                    name: 'list_price_aed'
                },
                {
                    data: 'shipping_weight',
                    name: 'shipping_weight'
                },
                {
                    data: 'image_t',
                    name: 'image_t'
                },
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'image_p_image_d',
                    name: 'image_p_image_d',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'all_category',
                    name: 'all_category'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'height_length_width',
                    name: 'height_length_width',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'weight',
                    name: 'weight'
                },
                {
                    data: 'flipkart_amazon',
                    name: 'flipkart_amazon',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'upc',
                    name: 'upc'
                },
                {
                    data: 'manufacturer',
                    name: 'manufacturer'
                },
                {
                    data: 'latency',
                    name: 'latency'
                },
                {
                    data: 'uae_latency_b2c_latency',
                    name: 'uae_latency_b2c_latency',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'ean',
                    name: 'ean'
                },
                {
                    data: 'color',
                    name: 'color'
                },
                {
                    data: 'model_mpn',
                    name: 'model_mpn',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'detail_page_url',
                    name: 'detail_page_url'
                },
                {
                    data: 'creation_time',
                    name: 'creation_time'
                },
                {
                    data: 'page',
                    name: 'page'
                },
            ]
        });

        $('.modal_close').on('click', function() {
            $('#productExport').modal('hide');
        });

        $('.all').change(function() {
            if ($('.all').is(':checked')) {
                $(".header_options").prop("checked", true);
                // $(".header_options").attr("disabled", true);
            } else {
                $(".header_options").prop("checked", false);
                // $(".header_options").removeAttr("disabled");
            }
        });

        $('.header_options').change(function() {
            let select_header = [];
            let count = 0;
            $("input[name='options[]']:checked").each(function() {
                count++;
            });
            // alert(count);
            if (count === 41) {
                $(".all").prop("checked", true);
            } else {
                $(".all").prop("checked", false);
            }
        });

        $(".product_export_modal_open").on('click', function() {
            $('#productExport').modal('show');
        });
        $("#file_download_modal_close").on('click', function() {
            $('#file_download_modal').modal('hide');
        });

        $('#exportToCsv').on('click', function() {

            exportCatalog('All');
        });

        $('#exportbyAsin').on('click', function() {

            exportCatalog('Asin');
        });

        function exportCatalog(type) {
            $('#productExport').modal('hide');
            $('.progress_bar').show();

            let select_header = [];
            let count = 0;
            $("input[name='options[]']:checked").each(function() {
                if (count == 0) {

                    select_header += $(this).val();
                } else {
                    select_header += '-' + $(this).val();
                }
                count++;
            });
            if (count == 0) {
                alert('Please Select Header');
                $('#productExport').modal('show');
                $('.progress_bar').hide();
            } else {
                $.ajax({
                    method: 'post',
                    url: '/other-product/export',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "_method": 'post',
                        'selected': select_header,
                        'type': type
                    },
                    success: function(response) {
                        // $('.progress_bar').hide();
                        // yajra_table.ajax.reload();
                    }
                })
            }
        }

        $('a.toggle-vis').on('click', function(e) {
            e.preventDefault();
            var column = yajra_table.column($(this).attr('data-column'));
            column.visible(!column.visible());
        });

        // yajra_table.columns([1, 4, 5, 8, 9, 10, 11, 12, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 28, 27, 29, 30, 31, 32, 33]).visible(false);
    </script>
@stop
