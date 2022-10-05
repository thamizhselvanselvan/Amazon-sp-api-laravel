@extends('adminlte::page')
@section('title', 'Mosh Catalog')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <h1 class="m-0 text-dark">Amazon Data</h1>
        <div class="col d-flex justify-content-end">
            {{-- <h2 class=" ml-2">
                <a href="{{ route('catalog.amazon.product') }}">
                    <x-adminlte-button label="Fetch Catalog From Amazon" class="btn-sm" theme="primary"
                        icon="fas fa-file-export" id="exportUniversalTextiles" />
                </a>
            </h2> --}}
            <h2 class="ml-2">
                <x-adminlte-button label="Cliqnshop Catalog Export" theme="primary" class="btn-sm" icon="fas fa-file-export"
                    id="exportcliqnshopCatalog" />
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Download Cliqnshop Catalog" theme="primary" class="btn-sm" icon="fas fa-download"
                    id="catalogcliqnshopdownload" data-toggle="modal" data-target="#downloacliqdModal" />
            </h2>

            <h2 class="ml-2">
                <x-adminlte-button label="Export Catalog" theme="primary" class="btn-sm" icon="fas fa-file-export"
                    id="exportCatalog" data-toggle="modal" data-target="#catalogExport" />
            </h2>

            <h2 class="ml-2">
                <x-adminlte-button label="Download Catalog" theme="primary" class="btn-sm" icon="fas fa-download"
                    id="catalogdownload" data-toggle="modal" data-target="#downloadModal" />
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Export Catalog Price" class="btn-sm" theme="primary" icon="fas fa-file-export"
                    id="export_catalog_price" data-toggle="modal" data-target="#catalogPriceExport" />
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Download Catalog Price" class="btn-sm" theme="primary" icon="fas fa-download"
                    id="download_catalog_price" data-toggle="modal" data-target="#file_download_modal" />
            </h2>

            <div class="modal" id="downloacliqdModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Download Cliqnshop Catalog </h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body catalogcliqnshop">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="catalogExport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Export Catalog</h5>
                            <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="font-size:15px">
                            <form action="{{ route('catalog.export') }}">
                                <h5>Select Source</h5>
                                <div class="row border">
                                    <div class="col-2">
                                        <label for="IN">IN</label>
                                        <input type="radio" name="source" value="IN">
                                    </div>
                                    <div class="col-2">
                                        <label for="US">US</label>
                                        <input type="radio" name="source" value="US">
                                    </div>
                                </div><br>

                                <h5>Select Priority</h5>
                                <div class="row border">
                                    <div class="col-2">
                                        <label for="P1">P1</label>
                                        <input type="radio" class="destination-priority" name="priority"
                                            value="1">
                                    </div>
                                    <div class="col-2">
                                        <label for="P2">P2</label>
                                        <input type="radio" class="destination-priority" name="priority"
                                            value="2">
                                    </div>
                                    <div class="col-2 ">
                                        <label for="P3">P3</label>
                                        <input type="radio" class="destination-priority" name="priority"
                                            value="3">
                                    </div>
                                </div>

                                <div class="col-12 float-left mt-2">
                                    <x-adminlte-button label="Export" theme="success" class="btn btn-sm "
                                        icon="fas fa-file-export " type="submit" />
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="catalogPriceExport" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Export Catalog Price</h5>
                            <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="font-size:15px">
                            <form action="{{ route('catalog.price.export') }}" method="GET">
                                <h5>Select Source</h5>
                                <div class="row border">
                                    <div class="col-2">
                                        <label for="IN">IN</label>
                                        <input type="radio" name="source" value="IN">
                                    </div>
                                    <div class="col-2">
                                        <label for="US">US</label>
                                        <input type="radio" name="source" value="US">
                                    </div>
                                </div><br>

                                <h5>Select Priority</h5>
                                <div class="row border">
                                    <div class="col-2">
                                        <label for="P1">P1</label>
                                        <input type="radio" class="destination-priority" name="priority"
                                            value="1">
                                    </div>
                                    <div class="col-2">
                                        <label for="P2">P2</label>
                                        <input type="radio" class="destination-priority" name="priority"
                                            value="2">
                                    </div>
                                    <div class="col-2 ">
                                        <label for="P3">P3</label>
                                        <input type="radio" class="destination-priority" name="priority"
                                            value="3">
                                    </div>
                                </div>

                                <div class="col-12 float-left mt-2">
                                    <x-adminlte-button label="Export" theme="success" class="btn btn-sm "
                                        icon="fas fa-file-export " type="submit" />
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="downloadModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Download Catalog Zip</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body catalogFiles">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                        </div>
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
        </div>
    </div>
    <div class="modal" id="file_download_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Download Catalog Price</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body catalogPricing">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8 ">
            <div class="card ">
                <div class="card-header text-center mt-0 pt-0 mb-0 pb-0">
                    <h3>Search Catalog And Price</h3>
                </div>
                <div class="card-body ">
                    <label for="Select Source" class="mt-0">Select Source</label><br>
                    <div class="row ">
                        <div class="col-1">
                            <input type="radio" class="Asin-source" name="source" value="IN" />
                            <label for="IN">IN</label>
                        </div>
                        <div class="col-1">
                            <input type="radio" class="Asin-source" name="source" value="US" />
                            <label for="US">US</label>
                        </div>

                    </div>
                    <x-adminlte-textarea label="Enter ASIN" type="text-area" class="Asins" name="catalog_asins"
                        placeholder="Enter Asin" rows="4" />
                    <b>
                        <p class="text-danger" id="error"></p>
                    </b>
                    <x-adminlte-button label="Search" type="submit" theme="primary" icon="fas fa-search text-danger"
                        class="search-catalog btn-sm float-right mt-2 " />
                </div>
            </div>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="row">
        <table class="table table-bordered table-striped text-center table-sm ">
            <thead class="">
                <tr class="bg-info thead"></tr>

            </thead>
            <tbody class="search-data">
            </tbody>
        </table>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $('#country').on('change', function() {
            let country_code = $(this).val();
            if (country_code != 'NULL') {
                yajraTable(country_code);
            }
        });

        $(document).ready(function() {
            $('#country').change(function() {
                document.getElementById('countrymsg').innerHTML = '';
            });
        });

        $('#catalogdownload').click(function() {

            $.ajax({
                url: "/catalog/get-file",
                method: "GET",
                data: {
                    "catalog": "catalog",
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response == '') {
                        $('.catalogFiles').append('File Downloading..');
                    } else {

                        $('.catalogFiles').empty();
                        console.log(response);
                        let files = '';
                        $.each(response, function(index, response) {
                            let data = response;
                            $.each(data, function(key, data) {

                                files += "<li class='p-0 m-0'>";
                                files += "<a href='/catalog/download/csv-file/" +
                                    index + "/" + key +
                                    "' class='p-0 m-0'> Catalog " + index + '&nbsp;' +
                                    key + "</a> ";
                                files += data;
                                files += "</li>";
                            });
                            // let file_name = Object.keys(response)[0];
                            // let file_time = response[file_name];
                        });
                        $('.catalogFiles').html(files);
                    }

                },
            });
        });

        $('#download_catalog_price').click(function() {
            $.ajax({
                url: "/catalog/get-file",
                data: {
                    "method": "GET",
                    "catalog": "catalog_price",
                    "_token": "{{ csrf_token() }}",
                },
                success: function(result) {

                    if (result == '') {
                        $('.catalogPricing').append('File Downloading..');
                    } else {

                        $('.catalogPricing').empty();
                        let files = '';
                        $.each(result, function(index, result) {
                            let data = result;
                            $.each(data, function(key, data) {
                                files += "<li class='p-0 m-0'>";
                                files += "<a href='/catalog/download/price/" + index +
                                    '/' +
                                    key +
                                    "' class='p-0 m-0'> Catalog Price " + index +
                                    '&nbsp;' +
                                    key + "</a> ";
                                files += data;
                                files += "</li>";
                            });
                        });
                        $('.catalogPricing').append(files);
                    }
                },

            });
        });

        // START CATALOG BULK SEARCH

        $('.search-catalog').on('click', function() {

            $('.display-data').addClass('d-block');
            let catalog_asins = $('.Asins').val();
            let source = $('input[name="source"]:checked').val();

            if (!$('input[name="source"]:checked').val()) {
                alert('Please choose source');
                return false;
            } else if (catalog_asins == '') {
                document.getElementById('error').innerHTML = 'Please enter Asin *';
                $('.thead').empty();
                $('.search-data').empty();

                return false;
            } else {
                document.getElementById('error').innerHTML = '';
                $.ajax({
                    method: 'post',
                    url: "{{ route('catalog.asin.search') }}",
                    data: {
                        "catalog_asins": catalog_asins,
                        "source": source,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(result) {
                        let data = '';
                        let head = "";
                        let str_replace = '';
                        $('.thead').empty();
                        $.each(result[0], function(key, record) {
                            head += " <td>" + key + "</td> ";
                            str_replace = head.replace(/_+/g, ' ').toUpperCase();

                        });
                        $('.thead').append(str_replace);

                        $.each(result, function(key1, record1) {
                            data += '<tr>';
                            $.each(record1, function(key2, value) {
                                data += "<td>" + value + "</td>"
                            });
                            data += '</tr>';
                        });
                        $('.search-data').html(data);
                    },
                    error: function(result) {
                        // alert('Data not found!');
                    }
                });
            }
        });

        // END CATALOG BULK SEARCH

        $('#exportcliqnshopCatalog').on('click', function() {
            window.location.href = '/catalog/cliqnshop/export';
        });

        $('#catalogcliqnshopdownload').click(function() {

            $.ajax({
                url: "/catalog/cliqnshop/get-file",
                method: "GET",
                data: {
                    "catalog": "Cliqnshop",
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    if (response == '') {
                        $('.catalogcliqnshop').append('File Downloading..');
                    } else {
                        $('.catalogcliqnshop').empty();
                        let files = '';
                        $.each(response, function(index, result) {

                            files += "<li class='p-0 m-0'>";
                            files += "<a href='/catalog/cliqnshop/download/" + index + "'>" +
                                index + '&nbsp; ' + "</a>";
                            files += result;

                            files += "</li>";

                        });
                        $('.catalogcliqnshop').append(files);

                    }

                },
                error: function(response) {
                    console.log(response);
                },
            });
        });
    </script>
@stop
