@extends('adminlte::page')
@section('title', 'Catalog & Price')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
<style>
    footer {
        display: none;
    }
</style>
@stop

@section('content_header')

<div class="row">
    <h1 class="m-0 text-dark">Cliqnshop Catalog </h1>
</div>

<div class="row">
    <div style="margin-top: 1.0rem;">
        <div class="col d-flex">

            <h2 class="ml-2">
                <x-adminlte-button label="Cliqnshop Catalog Export" theme="primary" class="btn-sm" icon="fas fa-file-export" id="exportcliqnshopCatalog" />
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Download Cliqnshop Catalog" theme="primary" class="btn-sm" icon="fas fa-download" id="catalogcliqnshopdownload" data-toggle="modal" data-target="#downloacliqdModal" />
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Upload New ASIN" theme="info" class="btn-sm" icon="fas fa-upload" id="new_asin" data-toggle="modal" data-target="#cliqnshop_new_asin_modal" />
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Download Upload ASIN Catalog" theme="info" class="btn-sm" icon="fas fa-download" id="new_asin_cat" data-toggle="modal" data-target="#uploaded_asin_catalog" />
            </h2>
        </div>


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



        <div class="modal" id="uploaded_asin_catalog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Download Uploaded asin Catalog </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body new_upload_catalog">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>





        <div class="modal fade" id="cliqnshop_new_asin_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="new_asin">Add New Asins To Cliqnshop</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                        please download <strong>CSV Templete</strong> and upload the data in csv format only.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <a href=" {{ route('cliqnshop.catalog.csv.templete') }} ">
                            <x-adminlte-button label="Download Template" theme="info" icon="fas fa-file-download" class="btn-sm ml-2" />
                        </a>
                        <form class="row" id="multi-file-upload" method="POST" action="{{ route('cliqnshop.catalog.csv.import') }}" accept-charset="utf-8" enctype="multipart/form-data">
                            @csrf

                            <div class="col-12">
                                <x-adminlte-input label="Choose CSV File" name="cliqnshop_csv" id="files" type="file" />
                            </div>
                            <div class="col-2.5">
                                <div class="text-center">
                                    <x-adminlte-button label="Upload" theme="primary" class="add_ btn-sm" icon="fas fa-upload" type="submit" id="order_upload" />
                                </div>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close" aria-hidden="true"></i> Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
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
        <div class="alert_display">
            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

@stop

@section('js')
<script type="text/javascript">
    $('#exportcliqnshopCatalog').on('click', function() {
        window.location.href = '/catalog/cliqnshop/export';
    });

    $('#catalogcliqnshopdownload').click(function() {

        $.ajax({
            url: "/catalog/cliqnshop/get-file",
            method: "GET",
            data: {
                "catalog": "Cliqnshop/catalog",
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {

                if (response == '') {
                    $('.catalogcliqnshop').empty();
                    $('.catalogcliqnshop').append('File Downloading..');
                    return false;
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

    $('#new_asin_cat').click(function() {

        $.ajax({
            url: "{{route('uploaded.asin.catalog.export.cliqnshop')}}",
            method: "GET",
            data: {
                "catalog": "Cliqnshop/\imported_cat",
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {

                if (response == '') {
                    $('.new_upload_catalog').empty();
                    $('.new_upload_catalog').append('Please Upload The File And Wait For Fe Minuts Catalog Is Exporting..');
                    return false;
                } else {
                    $('.new_upload_catalog').empty();
                    let files = '';
                    $.each(response, function(index, result) {

                        files += "<li class='p-0 m-0'>";
                        files += "<a href='/uploaded/catalog/cliqnshop/download/" + index + "'>" +
                            index + '&nbsp; ' + "</a>";
                        files += result;

                        files += "</li>";

                    });
                    $('.new_upload_catalog').append(files);

                }

            },
            error: function(response) {
                console.log(response);
            },
        });
    });
</script>
@stop