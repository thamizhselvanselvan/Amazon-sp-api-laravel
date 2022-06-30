@extends('adminlte::page')

@section('title', 'ASIN Upload')

@section('content_header')

<div class="row">
    <h1 class="m-0 text-dark">Upload ASIN</h1>
    <h2 class="text-right col">
        <x-adminlte-button label='Export Catalog By Asin' class="product_export_modal_open" theme="primary" icon="fas fa-file-export" />
    </h2>
</div>
@stop

@section('content')
<div class="alert_display">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>

                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>

<div class="modal fade" id="productExport" tabindex="-1" role="dialog" aria-labelledby="productExportModalLabel" aria-hidden="true">
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
                        <input class="form-check-input header_options" type="checkbox" value="hit" name='options[]' id="hit">
                        <h6>Hit</h6>
                    </div>

                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="asin" name='options[]' id="asin">
                        <h6>Asin</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="sku" name='options[]' id="sku">
                        <h6>Sku</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="hs_code" name='options[]' id="hs_code">
                        <h6>HS Code</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="gst" name='options[]' id="gst">
                        <h6>GST</h6>
                    </div>

                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="update_time" name='options[]' id="update_time">
                        <h6>Update Time</h6>
                    </div>
                </div>
                <div class="form-check d-flex row">
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="availability" name='options[]' id="availability">
                        <h6>Availability</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="price" name='options[]' id="price">
                        <h6>Price</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="list_price" name='options[]' id="list_price">
                        <h6>List Price</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="price1" name='options[]' id="price_1">
                        <h6>Price 1</h6>
                    </div>

                    <div class="col-2">
                        <input class="form-check-input header_options " type="checkbox" value="price_inr" name='options[]' id="price_inr">
                        <h6>Price INR</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="list_price_inr" name='options[]' id="list_price_inr">
                        <h6>List Price INR</h6>
                    </div>
                </div>
                <div class="form-check d-flex row">
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="price_aed" name='options[]' id="price_aed">
                        <h6>Price AED</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="list_price_aed" name='options[]' id="list_price_aed">
                        <h6>List Price AED</h6>
                    </div>

                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="image_t" name='options[]' id="image_t">
                        <h6>Image T</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="id" name='options[]' id="id">
                        <h6>ID</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="title" name='options[]' id="title">
                        <h6>Title</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="shipping_weight" name='options[]' id="shipping_weight">
                        <h6>Shipping Weight</h6>
                    </div>
                </div>
                <div class="form-check d-flex row">

                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="image_p" name='options[]' id="image_p">
                        <h6>Image P</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="image_d" name='options[]' id="image_d">
                        <h6>Image D</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="all_category" name='options[]' id="all_category">
                        <h6>All Category</h6>
                    </div>

                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="category" name='options[]' id="category">
                        <h6>Category</h6>
                    </div>

                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="description" name='options[]' id="description">
                        <h6>Description</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="flipkart" name='options[]' id="flipkart">
                        <h6>Flipkart</h6>
                    </div>
                </div>
                <div class="form-check d-flex row">
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="amazon" name='options[]' id="amazon">
                        <h6>Amazon</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="length" name='options[]' id="length">
                        <h6>Length</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="height" name='options[]' id="height">
                        <h6>Height</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="width" name='options[]' id="width">
                        <h6>Width</h6>
                    </div>

                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="weight" name='options[]' id="weight">
                        <h6>Weight</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="upc" name='options[]' id="upc">
                        <h6>UPC</h6>
                    </div>
                </div>
                <div class="form-check d-flex row">
                    <div class="col-2 ">
                        <input class="form-check-input header_options" type="checkbox" value="latency" name='options[]' id="latency">
                        <h6>Latency</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="uae_latency" name='options[]' id="uae">
                        <h6>UAE Latency</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="b2c_latency" name='options[]' id="b2c_latency">
                        <h6>B2C Latency</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="ean" name='options[]' id="ean">
                        <h6>EAN</h6>
                    </div>

                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="color" name='options[]' id="color">
                        <h6>Color</h6>
                    </div>
                    <div class="ml-1 mr-3">
                        <input class="form-check-input header_options" type="checkbox" value="manufacturer" name='options[]' id="manufacturer">
                        <h6>Manufacturer</h6>
                    </div>
                </div>
                <div class="form-check d-flex row">
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="model" name='options[]' id="model">
                        <h6>Model</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="mpn" name='options[]' id="mpn">
                        <h6>MPN</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="creation_time" name='options[]' id="creationg_time">
                        <h6>Creation Time</h6>
                    </div>

                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="page" name='options[]' id="page">
                        <h6>Page</h6>
                    </div>
                    <div class="col-2">
                        <input class="form-check-input header_options" type="checkbox" value="detail_page_url" name='options[]' id="detail_page_url">
                        <h6>Detail page URL</h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal_close" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary" id='exportToCsv'>Export All Catalog</button> -->
                <button type="button" class="btn btn-primary" id='exportbyAsin'>Export By Asin</button>
            </div>
        </div>
    </div>
</div>


<div class="col-sm-6">
    <div class="form-group">
        <div class="custom-control custom-radio select-text" data-type="text-box">
            <input class="custom-control-input" type="radio" id="txt-box" name="customRadio" checked>
            <label for="txt-box" class="custom-control-label">Text Box</label>
        </div>
        <div class="custom-control custom-radio select-text" data-type="file-box">
            <input class="custom-control-input" type="radio" id="txt-file" name="customRadio">
            <label for="txt-file" class="custom-control-label">Upload txt file</label>
        </div>
    </div>
</div>

<div class="row m-3 ">
    <div class="col-12 text-box-input">
        <form class="row" action="asin_save" method="POST" enctype="multipart/form-data">
            @csrf
            <label>Enter ASIN</label>
            <textarea class="form-control" rows="3" placeholder="Enter ASIN ..." name="textarea"></textarea>
            <div class="text-right m-2">
                <a href='asin_save'>
                    <x-adminlte-button label='Submit' class="btn-sm" theme="primary" icon="fas fa-file-upload" type="submit" />
                </a>
            </div>
        </form>
    </div>

    <div class="col-12 text-center txt-file-upload d-none">
        <!-- <strong>Upload .TXT File</strong> -->
        <form class="row" action="add-bulk-asin" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="col-3"></div>
            <div class="col-6 text-left">
                <x-adminlte-input label="Upload ASIN txt File" name="asin" id="asin" type="file" />
            </div>
            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Upload File" theme="primary" class="add_asin btn-sm" icon="fas fa-file-upload" type="submit" />
                </div>
            </div>
        </form>
    </div>
</div>

@stop

@section('js')
<script type="text/javascript">
    $(".select-text").on('click', function() {
        let self = $(this);
        let val = self.attr('data-type');
        let text_box = $(".text-box-input");
        let file_box = $(".txt-file-upload");


        if (val == "text-box") {

            text_box.removeClass("d-none");
            file_box.addClass("d-none");

        } else {
            text_box.addClass("d-none");
            file_box.removeClass("d-none");
        }
    })

    $(".product_export_modal_open").on('click', function() {

        $('#productExport').modal('show');
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
    
    $('#exportbyAsin').on('click', function() {
        
        exportCatalog('Asin');
    });

    function exportCatalog(type)
    {
        // alert('click');
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
        if(count == 0){
            alert('Please Select Header');
            $('#productExport').modal('show');
            $('.progress_bar').hide();
        }else{
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
</script>
@stop