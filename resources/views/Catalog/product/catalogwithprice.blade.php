@extends('adminlte::page')
@section('title', 'Catalog With Price')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <h1 class="m-0 text-dark">Catalog With Price</h1>
        <div class="col text-right">
            <a href="{{ route('catalog.with.price.download.template') }}">
                <x-adminlte-button label="Download Template" theme="primary" class="btn-sm" icon="fas fa-download" />
            </a>

            <x-adminlte-button label="Download" theme="primary" class="btn-sm" icon="fas fa-download" id="catalogdownload"
                data-toggle="modal" data-target="#downloadModal" />

            <div class="modal" id="downloadModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Download Catalog With Price </h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body catalog_with_price_files text-left">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">

            <input type="radio" name="Asin-destination" id="text-area" checked />
            <label for="Text-area" class="ml-1">Text-Area</label>
            <input type="radio" name="Asin-destination" id="bulk-import" class="ml-2" />
            <label for="Text-area" class="ml-1">Bulk Asin Import</label>

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
        </div>
    </div>

    <div class="row">
        <div class="col-2"></div>
        <div class="col-8 ">
            <div class="card ">
                <div class="card-header text-center mt-0 pt-0 mb-0 pb-0">
                    <h3>Export Catalog With Price</h3>
                </div>
                <div class="card-body ">
                    <div class="text-area">
                        <form action="{{ route('catalog.with.price.export') }} " method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <label for="Select Source" class="mt-0">Select Source</label><br>
                            <div class="row ">
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="source" value="IN"
                                        id="IN" />
                                    <label for="IN">IN</label>
                                </div>
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="source" value="US"
                                        id="US" />
                                    <label for="US">US</label>
                                </div>

                            </div>

                            <label for="Select Source" class="mt-1">Select Prioriy</label>
                            <div class="row ">
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="priority" value="All">
                                    <label for="All">All</label>
                                </div>
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="priority" value="1">
                                    <label for="P1">P1</label>
                                </div>
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="priority" value="2">
                                    <label for="P2">P2</label>
                                </div>
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="priority" value="3">
                                    <label for="P3">P3</label>
                                </div>
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="priority" value="4">
                                    <label for="P4">P4</label>
                                </div>
                            </div>
                            <div class=" select_header d-none">
                                <label for="Select Source" class="mt-1">Select Headers</label>
                                <div class="mt-1 row ">

                                    <div class="col-2 ">
                                        <input class="select_all" type="checkbox" id="select_all">
                                        <label class="ml-1" for="ASIN">Select All</label>
                                    </div>
                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.asin" name='header[]'
                                            id="asin">
                                        <label class="ml-1" for="ASIN">ASIN</label>
                                    </div>

                                    {{-- <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.dimensions"
                                            name='header[]' id="dimensions">
                                        <label class="ml-1" for="Dimensions">Dimensions</label>
                                    </div> --}}

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.images" name='header[]'
                                            id="images">
                                        <label class="ml-1" for="Images">Images</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.item_classification"
                                            name='header[]' id="item_classification">
                                        <label class="ml-1" for="Classification">Item Classification</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.item_name"
                                            name='header[]' id="item_name">
                                        <label class="ml-1" for="Item Name">Item Name</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.brand" name='header[]'
                                            id="brand">
                                        <label class="ml-1" for="Brand">Brand</label>
                                    </div>

                                </div>

                                <div class="row ">

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.manufacturer"
                                            name='header[]' id="manufacturer">
                                        <label class="ml-1" for="Manufacturer">Manufacturer</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.color" name='header[]'
                                            id="color">
                                        <label class="ml-1" for="Color">Color</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.model_number"
                                            name='header[]' id="model_number">
                                        <label class="ml-1" for="Model ">Model Number</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.product_types"
                                            name='header[]' id="product_types">
                                        <label class="ml-1" for="Product Type">Product Type</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_ins.available"
                                            name='header[]' id="available">
                                        <label class="ml-1" for="Available">Available</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.available"
                                            name='header[]' id="available">
                                        <label class="ml-1" for="Available">Available</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.browse_classification"
                                            name='header[]' id="browse_classification">
                                        <label class="ml-1" for="Category">Category</label>
                                    </div>

                                </div>

                                <div class="row ">

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header" type="checkbox"
                                            value="pricing_ins.weight-cat.dimensions" name='header[]' id="weight">
                                        <label class="ml-1" for="Weight">Weight</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header" type="checkbox" value="volumetric_weight"
                                            name='header[]' id="volumetric_weight">
                                        <label class="ml-1" for="Volumetric Weight">Volumetric Weight</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header" type="checkbox" value="actual_weight"
                                            name='header[]' id="actual_weight">
                                        <label class="ml-1" for="Actual Weight">Actual Weight</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox"
                                            value="pricing_uss.weight-cat.dimensions" name='header[]' id="weight">
                                        <label class="ml-1" for="Weight">Weight</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="volumetric_weight"
                                            name='header[]' id="volumetric_weight">
                                        <label class="ml-1" for="Volumetric Weight">Volumetric Weight</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="actual_weight"
                                            name='header[]' id="actual_weight">
                                        <label class="ml-1" for="Actual Weight">Actual Weight</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header " type="checkbox" value="pricing_ins.in_price"
                                            name='header[]' id="in_price">
                                        <label class="ml-1" for="IND Price">IND Price</label>
                                    </div>
                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.us_price"
                                            name='header[]' id="us_price">
                                        <label class="ml-1" for="USA Price">USA Price</label>
                                    </div>
                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header " type="checkbox" value="pricing_ins.ind_to_uae"
                                            name='header[]' id="ind_to_uae">
                                        <label class="ml-1" for="IND To UAE">IND To UAE</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_ins.ind_to_sg"
                                            name='header[]' id="ind_to_sg">
                                        <label class="ml-1" for="IND To SG">IND To SG</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header " type="checkbox" value="pricing_ins.ind_to_sa"
                                            name='header[]' id="ind_to_sa">
                                        <label class="ml-1" for="IND To SA">IND To SA</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header " type="checkbox" value="pricing_ins.updated_at"
                                            name='header[]' id="updated_at">
                                        <label class="ml-1" for="Update Date">Update Date</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.usa_to_in_b2b"
                                            name='header[]' id="usa_to_in_b2b">
                                        <label class="ml-1" for="USA-IND B2B">USA-IND B2B</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.usa_to_in_b2c"
                                            name='header[]' id="usa_to_in_b2c">
                                        <label class="ml-1" for="USA To IND B2C">USA To IND B2C</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.usa_to_uae"
                                            name='header[]' id="usa_to_uae">
                                        <label class="ml-1" for="USA To UAE">USA To UAE</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.usa_to_sg"
                                            name='header[]' id="usa_to_sg">
                                        <label class="ml-1" for="USA To SG">USA To SG</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.updated_at"
                                            name='header[]' id="updated_at">
                                        <label class="ml-1" for="Update Date">Update Date</label>
                                    </div>
                                </div>
                            </div>

                            <x-adminlte-textarea label="Enter ASIN" name="text_area_asins" type="text" rows="8"
                                placeholder="Enter ASIN" />
                            <input type='hidden' name='form_type' value='text-area' id='text_area_upload' />
                            <x-adminlte-button label='Export' type='submit' theme='success' icon='fas fa-file-export'
                                class=" btn-sm float-right mt-2 " />

                        </form>
                    </div>
                    <div class="file-upload d-none">
                        <form action="{{ route('catalog.with.price.export') }} " method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <label for="Select Source" class="mt-0">Select Source</label><br>
                            <div class="row ">
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="source" value="IN"
                                        id="IN" />
                                    <label for="IN">IN</label>
                                </div>
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="source" value="US"
                                        id="US" />
                                    <label for="US">US</label>
                                </div>

                            </div>

                            <label for="Select Source" class="mt-1">Select Prioriy</label>
                            <div class="row ">
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="priority" value="All">
                                    <label for="All">All</label>
                                </div>
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="priority" value="1">
                                    <label for="P1">P1</label>
                                </div>
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="priority" value="2">
                                    <label for="P2">P2</label>
                                </div>
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="priority" value="3">
                                    <label for="P3">P3</label>
                                </div>
                                <div class="col-1">
                                    <input type="radio" class="catalog-with-price" name="priority" value="4">
                                    <label for="P4">P4</label>
                                </div>
                            </div>
                            <div class=" select_header d-none">
                                <label for="Select Source" class="mt-1">Select Headers</label>
                                <div class="mt-1 row ">

                                    <div class="col-2 ">
                                        <input class="select_all" type="checkbox" id="select_all">
                                        <label class="ml-1" for="ASIN">Select All</label>
                                    </div>
                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.asin" name='header[]'
                                            id="asin">
                                        <label class="ml-1" for="ASIN">ASIN</label>
                                    </div>

                                    {{-- <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.dimensions"
                                            name='header[]' id="dimensions">
                                        <label class="ml-1" for="Dimensions">Dimensions</label>
                                    </div> --}}

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.images" name='header[]'
                                            id="images">
                                        <label class="ml-1" for="Images">Images</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.item_classification"
                                            name='header[]' id="item_classification">
                                        <label class="ml-1" for="Classification">Item Classification</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.item_name"
                                            name='header[]' id="item_name">
                                        <label class="ml-1" for="Item Name">Item Name</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.brand" name='header[]'
                                            id="brand">
                                        <label class="ml-1" for="Brand">Brand</label>
                                    </div>
                                </div>

                                <div class="row ">
                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.manufacturer"
                                            name='header[]' id="manufacturer">
                                        <label class="ml-1" for="Manufacturer">Manufacturer</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.color" name='header[]'
                                            id="color">
                                        <label class="ml-1" for="Color">Color</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.model_number"
                                            name='header[]' id="model_number">
                                        <label class="ml-1" for="Model ">Model Number</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.product_types"
                                            name='header[]' id="product_types">
                                        <label class="ml-1" for="Product Type">Product Type</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_ins.available"
                                            name='header[]' id="available">
                                        <label class="ml-1" for="Available">Available</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.available"
                                            name='header[]' id="available">
                                        <label class="ml-1" for="Available">Available</label>
                                    </div>

                                    <div class="col-2 catalog">
                                        <input class="choose_header" type="checkbox" value="cat.browse_classification"
                                            name='header[]' id="browse_classification">
                                        <label class="ml-1" for="Category">Category</label>
                                    </div>
                                </div>

                                <div class="row ">


                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header" type="checkbox"
                                            value="pricing_ins.weight-cat.dimensions" name='header[]' id="weight">
                                        <label class="ml-1" for="Weight">Weight</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header" type="checkbox" value="volumetric_weight"
                                            name='header[]' id="volumetric_weight">
                                        <label class="ml-1" for="Volumetric Weight">Volumetric Weight</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header" type="checkbox" value="actual_weight"
                                            name='header[]' id="actual_weight">
                                        <label class="ml-1" for="Actual Weight">Actual Weight</label>
                                    </div>


                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox"
                                            value="pricing_uss.weight-cat.dimensions" name='header[]' id="weight">
                                        <label class="ml-1" for="Weight">Weight</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="volumetric_weight"
                                            name='header[]' id="volumetric_weight">
                                        <label class="ml-1" for="Volumetric Weight">Volumetric Weight</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="actual_weight"
                                            name='header[]' id="actual_weight">
                                        <label class="ml-1" for="Actual Weight">Actual Weight</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header " type="checkbox" value="pricing_ins.in_price"
                                            name='header[]' id="in_price">
                                        <label class="ml-1" for="IND Price">IND Price</label>
                                    </div>
                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.us_price"
                                            name='header[]' id="us_price">
                                        <label class="ml-1" for="USA Price">USA Price</label>
                                    </div>
                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header " type="checkbox" value="pricing_ins.ind_to_uae"
                                            name='header[]' id="ind_to_uae">
                                        <label class="ml-1" for="IND To UAE">IND To UAE</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_ins.ind_to_sg"
                                            name='header[]' id="ind_to_sg">
                                        <label class="ml-1" for="IND To SG">IND To SG</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header " type="checkbox" value="pricing_ins.ind_to_sa"
                                            name='header[]' id="ind_to_sa">
                                        <label class="ml-1" for="IND To SA">IND To SA</label>
                                    </div>

                                    <div class="col-2 india_price d-none">
                                        <input class="choose_header " type="checkbox" value="pricing_ins.updated_at"
                                            name='header[]' id="updated_at">
                                        <label class="ml-1" for="Update Date">Update Date</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.usa_to_in_b2b"
                                            name='header[]' id="usa_to_in_b2b">
                                        <label class="ml-1" for="USA-IND B2B">USA-IND B2B</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.usa_to_in_b2c"
                                            name='header[]' id="usa_to_in_b2c">
                                        <label class="ml-1" for="USA To IND B2C">USA To IND B2C</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.usa_to_uae"
                                            name='header[]' id="usa_to_uae">
                                        <label class="ml-1" for="USA To UAE">USA To UAE</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.usa_to_sg"
                                            name='header[]' id="usa_to_sg">
                                        <label class="ml-1" for="USA To SG">USA To SG</label>
                                    </div>

                                    <div class="col-2 usa_price d-none">
                                        <input class="choose_header" type="checkbox" value="pricing_uss.updated_at"
                                            name='header[]' id="updated_at">
                                        <label class="ml-1" for="Update Date">Update Date</label>
                                    </div>
                                </div>
                            </div>

                            <x-adminlte-input label="Enter ASIN" type="file" name="asin" />
                            <input type='hidden' name='form_type' value='file_upload'>

                            <x-adminlte-button label='Export' type='submit' theme='success' icon='fas fa-file-export'
                                class=" btn-sm float-right mt-2 " id="catalogwithprice" />

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-2"></div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $.ajax({
                method: 'get',
                url: "{{ route('catalog.export.file.management.monitor') }}",
                data: {
                    "module_type": "CATALOG_PRICE_EXPORT",
                    "_token": "{{ csrf_token() }}",
                },
                response: 'json',
                success: function(response) {
                    console.log(response);
                    if (response == '0000-00-00 00:00:00') {

                        $('#catalogwithprice').prop('disabled', true);
                        $('#catalogwithprice').attr("title", "File is exporting...");
                    }

                },
            });
        });
        $('.text-area').on('click', '#IN', function() {
            India_Headers();
        });

        $('.text-area').on('click', '#US', function() {
            Usa_Headers();
        });

        $('.file-upload').on('click', '#IN', function() {
            India_Headers();
        });

        $('.file-upload').on('click', '#US', function() {
            Usa_Headers();
        });

        $('#text-area').click(function() {
            $('.text-area').removeClass('d-none');
            $('.file-upload').addClass('d-none');
        })

        $('#bulk-import').click(function() {
            $('.file-upload').removeClass('d-none');
            $('.text-area').addClass('d-none');
        });

        function India_Headers() {
            $('.select_header').removeClass('d-none');
            $('.india_price').removeClass('d-none');
            $('.usa_price').addClass('d-none');
            $('.usa').addClass('d-none');
            $('.india').removeClass('d-none');
            $('.usa_price').children('.choose_header').prop('checked', false);

            if ($('.select_all').is(':checked')) {
                $('.select_all').prop('checked', false);
                $('.choose_header').prop('checked', false);
            }

            $('.select_all').change(function() {
                if ($('.select_all').is(':checked')) {

                    $('.catalog').children('.choose_header').prop('checked', true);
                    $('.india_price').children('.choose_header').prop('checked', true);
                } else {
                    $('.catalog').children('.choose_header').prop('checked', false);
                    $('.india_price').children('.choose_header').prop('checked', false);
                }
            });
        }

        function Usa_Headers() {
            $('.select_header').removeClass('d-none');
            $('.india_price').addClass('d-none');
            $('.usa_price').removeClass('d-none');
            $('.india').addClass('d-none');
            $('.usa').removeClass('d-none');
            $('.india_price').children('.choose_header').prop('checked', false);

            if ($('.select_all').is(':checked')) {
                $('.select_all').prop('checked', false);
                $('.choose_header').prop('checked', false);
            }

            $('.select_all').change(function() {
                if ($('.select_all').is(':checked')) {

                    $('.catalog').children('.choose_header').prop('checked', true);
                    $('.usa_price').children('.choose_header').prop('checked', true);
                } else {
                    $('.catalog').children('.choose_header').prop('checked', false);
                    $('.usa_price').children('.choose_header').prop('checked', false);
                }
            });
        }

        $('#catalogdownload').click(function() {

            $.ajax({
                // url: "/catalog/with-price",
                url: "{{ route('catalog.with.price.file.show') }}",
                method: "GET",
                data: {
                    "catalog_with_price": "catalog_with_price",
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response == '') {
                        $('.catalog_with_price_files').append('File Downloading..');
                    } else {

                        $('.catalog_with_price_files').empty();
                        let files = '';
                        $.each(response, function(index, response) {
                            let data = response;
                            files += "<ul class='pl-0 ml-0 mb-0'><b>Catalog with price " +
                                index + "</b>";
                            $.each(data, function(key, data) {

                                files += "<li class=' ml-4'>";
                                if (key != 'Priority') {

                                    files +=
                                        "<a href='/catalog/with-price/download/csv/" +
                                        index + "/" + key +
                                        "' class='p-0 m-0'>" + '&nbsp;' +
                                        key + "</a> ";
                                } else {
                                    files +=
                                        "<a href='/catalog/with-price/download/csv/" +
                                        index + "/" + key +
                                        "' class='p-0 m-0'>All </a> ";
                                }

                                files += data;
                                files += "</li>";
                            });
                            files += "</ul>";
                        });
                        $('.catalog_with_price_files').html(files);
                    }

                },
            });
        });
    </script>
@stop
