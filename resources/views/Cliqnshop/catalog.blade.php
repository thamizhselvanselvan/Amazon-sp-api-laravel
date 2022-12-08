@extends('adminlte::page')
@section('title', 'Catalog & Price')

@section('css')
<link rel="stylesheet" href="/css/styles.css">

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
                "catalog": "Cliqnshop",
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {

                if (response == '') {
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
</script>
@stop