@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')

<div class="row">

    <h1 class="m-0 text-dark">Amazon Data</h1>
    <div class="col d-flex justify-content-end">

        <div>
            <x-adminlte-select name="country" id="country" class="float-right mt-1 catalogcountry">
                <option value="NULL">select country</option>
                @foreach ($sources as $source)
                <option value="{{$source->source}}">{{$source->source}}</option>
                @endforeach
            </x-adminlte-select>
            <p class="countrymsg" id="countrymsg"></p>
        </div>

        <h2 class=" ml-2">
            <a href="{{ route('catalog.amazon.product') }}">
                <x-adminlte-button label="Fetch Catalog From Amazon" class="btn-sm" theme="primary"
                    icon="fas fa-file-export" id="exportUniversalTextiles" />
            </a>
        </h2>
        <h2 class="ml-2">
            <x-adminlte-button label="Export Catalog" theme="primary" class="btn-sm" icon="fas fa-file-export"
                id="exportCatalog" />
        </h2>
        <h2 class="ml-2">

            <x-adminlte-button label="Download Catalog" theme="primary" class="btn-sm" icon="fas fa-download"
                id="catalogdownload" data-toggle="modal" data-target="#downloadModal" />

        </h2>
        <h2 class="ml-2">
            <x-adminlte-button label="Export Catalog Price" class="btn-sm" theme="primary" icon="fas fa-file-export"
                id="export_catalog_price" />
        </h2>

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
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- <h2 class="ml-2">
            <a href="#{{ route('catalog.export') }}">
                <x-adminlte-button label="Export Catalog" theme="primary" icon="fas fa-file-export"
                    id="exportCatalog" />
            </a>
        </h2> -->
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
            <div class="modal fade" id="file_download_modal" tabindex="-1" role="dialog"
                aria-labelledby="FileDownloadModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Download Catalog Price</h5>
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
            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Asin</th>
                        <th>Source</th>
                        <th>Name</th>
                        <th>Dimension</th>
                        <th>Weight</th>
                        <th>Price</th>

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
    $('#country').on('change', function() {
        let country_code = $(this).val();
        yajraTable(country_code);
    });

    $(document).ready(function() {
        $('#exportCatalog').on('click', function() {
            let country_code = $('#country').val();
            if (country_code == 'NULL') {
                var id = document.getElementById('country');
                var text = 'Country must filled out';
                document.getElementById('countrymsg').innerHTML = text;
                document.getElementById('countrymsg').style.color = "red";
            } else {
                $.ajax({
                    url: "{{ url('catalog/export') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "country_code": country_code,
                    },
                });
            }
        });
    });

    function yajraTable(country_code) {
        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: "{{ url('catalog/product') }}",
                data: {
                    "country_code": country_code,
                    "_token": "{{ csrf_token() }}",
                },
            },
            pageLength: 200,
            lengthMenu: [50, 100, 200, 500],
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
                    data: 'source',
                    name: 'source'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'item_dimensions',
                    name: 'item_dimensions'
                },
                {
                    data: 'weight',
                    name: 'weight'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
            ]
        });
    }
    $('#export_catalog_price').on('click', function() {
        let country_code = $('#country').val();
        if (country_code == 'NULL' || country_code == 'AE') {
            alert('Please Select Correct Country');
        } else {
            $.ajax({
                url: "/catalog/price/export",
                type: "post",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "_method": 'POST',
                    "country_code": country_code
                },
                success: function(response) {}
            });
        }
    });

    $('#catalogdownload').click(function() {

        $.ajax({
            url: "/catalog/get-file",
            method: "GET",
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                console.log(response);
                let files = '';
                $.each(response, function(index, response) {
                    let file_name = Object.keys(response)[0];
                    let file_time = response[file_name];
                    // alert(file_time);

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
    </script>
    @stop
