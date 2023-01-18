@extends('adminlte::page')
@section('title', 'BuyBox Stores')

@section('css')
<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

<div class="row">
    <h1 class="m-0 text-dark">BuyBox Stores</h1>
</div>
<div class="row">
    <div class="col">

        <div class="alert_display">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div style="margin-top: 1.0rem;">
        <div class="col d-flex">

            <h2 class="ml-2">
                <a href="{{route('buybox.export.all')}}">
                    <x-adminlte-button label="Export All" theme="primary" class="btn-sm" icon="fas fa-file-export" id="exportall" />
                </a>
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Download Exportred ASIN" theme="primary" class="btn-sm" icon="fas fa-download" id="exportasin" data-toggle="modal" data-target=".exportasindownload" />
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Upload New ASIN" theme="info" class="btn-sm" icon="fas fa-upload" id="new_asin" data-toggle="modal" data-target=".uploadasintoexport" />
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Download Upload ASIN" theme="info" class="btn-sm" icon="fas fa-download" id="new_asin" data-toggle="modal" data-target=".downloaduploadedasin" />
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Single ASIN latency Update" theme="success" class="btn-sm" icon="fas fa-upload" id="single_asin" data-toggle="modal" data-target=".Latency-Update-modal" />
            </h2>
        </div>

        <!-- Export All -->
        <div class="modal fade Latency-Update-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="new_asin">Single ASIN Latency Update</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form class="row" id="multi-file-upload" method="POST" action="{{route('buybox.latency.update')}}" accept-charset="utf-8" enctype="multipart/form-data">
                            @csrf
                            <div class="col-12">
                                <x-adminlte-select name="image" label="Select Store" name="store">
                                    <option value=''>Select Store</option>
                                    @foreach ($stores as $store)
                                    <option value="{{ $store->seller_id }}">{{$store->store_name }}</option>
                                    @endforeach

                                </x-adminlte-select>
                            </div>
                            <div class="col-12">
                                <x-adminlte-input label="Enter ASIN" name="asin" id="asin" type="text" placeholder="asin" />
                            </div>
                            <div class="col-12">
                                <x-adminlte-input label="Add Latency" name="latency" id="Latency" type="text" placeholder="Latency..." />
                            </div>
                            <div class="col-3 text-left">
                                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close" aria-hidden="true"></i> Close</button>
                                <x-adminlte-button label="Update" theme="primary" class="add_ btn-sm" icon="fas fa-upload" type="submit" id="order_upload" />
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Export download all Modal -->
        <div class="modal exportasindownload" id="downloacliqdModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Download Exported ASIN's Data </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body asin_expo">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- upload ASIN modal -->
        <div class="modal uploadasintoexport" id="downloacliqdModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload ASIN's</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body asin_expo">
                        <h4>Under Development</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- download uploaded ASIN modal -->
        <div class="modal downloaduploadedasin" id="downloacliqdModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload ASIN's</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body asin_expo">
                        <h4>Under Development</h4>
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
    //entire asin export download
    $('#exportasin').click(function() {

        $.ajax({
            url: "{{route('buybox.export.all.download')}}",
            method: "GET",
            data: {
                "catalog": "aws-products/exports",
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {

                if (response == '') {
                    $('.asin_expo').empty();
                    $('.asin_expo').append('File Downloading..');
                    return false;
                } else {
                    $('.asin_expo').empty();
                    let files = '';
                    $.each(response, function(index, result) {

                        files += "<li class='p-0 m-0'>";
                        files += "<a href='/buybox/all/export/download/local/" + index + "'>" +
                            index + '&nbsp; ' + "</a>";
                        files += result;

                        files += "</li>";

                    });
                    $('.asin_expo').append(files);

                }

            },
            error: function(response) {
                console.log(response);
            },
        });
    });
</script>
@stop