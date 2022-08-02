@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')

<div class="row">

    <h1 class="m-0 text-dark">Amazon Data</h1>
    <div class="col d-flex justify-content-end">

        <div>
            <x-adminlte-select name="country" id="country" class="float-right mt-1 ">
                <option value="NULL">select country</option>
                @foreach ($sources as $source)
                <option value="{{$source->source}}">{{$source->source}}</option>
                @endforeach
            </x-adminlte-select>
            <p class="countrymsg" id="countrymsg"></p>
        </div>

        <h2 class=" ml-2">
            <a href="{{ route('catalog.amazon.product') }}">
                <x-adminlte-button label="Fetch Catalog From Amazon" theme="primary" icon="fas fa-file-export"
                    id="exportUniversalTextiles" />
            </a>
        </h2>
        <h2 class="ml-2">
            <!-- <a href="{{ route('catalog.export') }}"> -->
            <x-adminlte-button label="Export Catalog" theme="primary" icon="fas fa-file-export" id="exportCatalog" />
            <!-- </a> -->
        </h2>
        <h2 class="ml-2">

            <x-adminlte-button label="Download Catalog" theme="primary" icon="fas fa-download" id="catalogdownload"
                data-toggle="modal" data-target="downloadModal" />

        </h2>

        <div class="modal" id="downloadModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Download Catalog</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <a href="download/csv-file">
                            <x-adminlte-button label="Download Catalog" theme="primary" icon="fas fa-download"
                                id="DownloadCatalog" />
                        </a>
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
        <div class="row">



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
    // alert(country_code);
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

$(document).ready(function() {

    $('#country').on('change', function() {
        if ($('#country').val() != 'NULL') {
            var id = document.getElementById('country');
            id.style = 'none';
            document.getElementById('countrymsg').innerHTML = '';
        }
    });

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
                    "country_code": country_code,
                    "_token": "{{ csrf_token() }}",
                },
            });
        }
    });

    $('#catalogdownload').on('click', function() {
        let country_code = $('#country').val();
        alert(country_code);
        if (country_code == 'NULL') {
            var id = document.getElementById('country');
            var text = 'Country must filled out';
            document.getElementById('countrymsg').innerHTML = text;
            document.getElementById('countrymsg').style.color = "red";
        } else {

            $.ajax({
                method: 'GET',
                url: "{{ url('catalog/download/csv-file') }}",
                data: {
                    "country_code": country_code,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    console.log(response);
                    // arr += response;
                    // window.location.href = '/invoice/zip-download/' + arr;
                    // alert('Export pdf successfully');
                },
            });
        }
    });
});
</script>
@stop
