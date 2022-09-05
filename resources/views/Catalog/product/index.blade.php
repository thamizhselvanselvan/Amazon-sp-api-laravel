@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')

<div class="row">
    <h1 class="m-0 text-dark">Amazon Data</h1>
    <div class="col d-flex justify-content-end">
        <h2 class=" ml-2">
            <a href="{{ route('catalog.amazon.product') }}">
                <x-adminlte-button label="Fetch Catalog From Amazon" class="btn-sm" theme="primary"
                    icon="fas fa-file-export" id="exportUniversalTextiles" />
            </a>
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
                                    <input type="radio" class="destination-priority" name="priority" value="1">
                                </div>
                                <div class="col-2">
                                    <label for="P2">P2</label>
                                    <input type="radio" class="destination-priority" name="priority" value="2">
                                </div>
                                <div class="col-2 ">
                                    <label for="P3">P3</label>
                                    <input type="radio" class="destination-priority" name="priority" value="3">
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

        <div class="modal fade" id="catalogPriceExport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
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
                                    <input type="radio" class="destination-priority" name="priority" value="1">
                                </div>
                                <div class="col-2">
                                    <label for="P2">P2</label>
                                    <input type="radio" class="destination-priority" name="priority" value="2">
                                </div>
                                <div class="col-2 ">
                                    <label for="P3">P3</label>
                                    <input type="radio" class="destination-priority" name="priority" value="3">
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
    @stop

    @section('content')
    @csrf
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
        </div>
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
                console.log(response);
                let files = '';
                $.each(response, function(index, response) {
                    let file_name = Object.keys(response)[0];
                    let file_time = response[file_name];

                    files += "<li class='p-0 m-0'>";
                    files += "<a href='/catalog/download/csv-file/" + file_name +
                        "' class='p-0 m-0'> Catalog " + file_name + "</a> ";
                    files += file_time;
                    files += "</li>";
                });
                $('.catalogFiles').html(files);
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

                $('.catalogPricing').empty();
                let files = '';
                $.each(result, function(index, result) {
                    let data = result;
                    $.each(data, function(key, data) {
                        files += "<li class='p-0 m-0'>";
                        files += "<a href='/catalog/download/price/" + index + '/' +
                            key +
                            "' class='p-0 m-0'> Catalog Price " + index + '&nbsp;' +
                            key + "</a> ";
                        files += data;
                        files += "</li>";
                    });
                });
                $('.catalogPricing').append(files);
            },
        });
    });
    </script>
    @stop
