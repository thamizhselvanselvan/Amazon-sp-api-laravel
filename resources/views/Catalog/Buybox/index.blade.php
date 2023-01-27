@extends('adminlte::page')

@section('title', 'BuyBox Import')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .source {
            padding: 05px;
        }

        .priority {
            padding: 05px;
        }

        .text-area {
            padding: 5px;
        }

        .bulk {
            padding: 5px;
        }
    </style>
@stop

@section('content_header')
    <div class="row">
        <div class="col">
            <h1 class="m-0 text-dark"><b>BuyBox ASIN Import </b> </h1>

        </div>
        <h2 class="ml-2">
            <a href="{{ route('catalog.buybox.download.export.template') }}">
                <x-adminlte-button label="Download BuyBox Template" theme="primary" class="btn-sm"
                    icon="fas fa-file-download" />
            </a>
        </h2>

        {{-- <h2 class="ml-2">
            <x-adminlte-button label="Truncate BuyBox" theme="primary" class="btn-sm" icon="fa fa-remove text-danger"
                id="TruncateBuyBox" data-toggle="modal" data-target="#BuyBoxTruncate" />
        </h2> --}}
        <div class="modal fade" id="BuyBoxTruncate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><b>Truncate BuyBox</b></h5>
                        <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="font-size:15px">
                        <form action="{{ route('catalog.buybox.truncate') }}" method="POST">
                            @csrf
                            <h5>BuyBox Count According To Priority</h5>
                            <table class="table table-bordered table-striped table-sm text-center">
                                <thead>
                                    <tr>
                                        <th>Priority1</th>
                                        <th>Priority2</th>
                                        <th>Priority3</th>
                                        <th>Priority4</th>
                                    </tr>
                                </thead>
                                <tbody id="BuyBoxCount">

                                </tbody>
                            </table>

                            <h5>Select Source</h5>
                            <div class="row border">
                                <div class="col-2">
                                    <label for="AE">AE</label>
                                    <input type="radio" name="source" value="AE">
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
                                <div class="col-2 ">
                                    <label for="P4">P4</label>
                                    <input type="radio" class="destination-priority" name="priority" value="4">
                                </div>
                            </div>

                            <div class="col-12 float-left mt-2">
                                <x-adminlte-button label="Truncate" theme="success" class="btn btn-sm "
                                    icon="fa fa-remove text-danger" type="submit" id="buyboxTruncateDD" />
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <input type="radio" name="Asin-destination" id="text-area" checked>
            <label for="Text-area" class="ml-1">Text-Area</label>
            <input type="radio" name="Asin-destination" id="bulk-import" class="ml-2" />
            <label for="Text-area" class="ml-1">Bulk Asin Import</label>
        </div>
    </div>
@stop

@section('content')
    <div class="loader d-none">
        <div class="sub-loader position-relative ">
            <div class="lds-hourglass"></div>
            <p>Loading...</p>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="col">
                <div class="col">
                    <div class="alert alert-warning alert-block info-msg d-none">
                        <!-- <button type="button" class="close" data-dismiss="alert">×</button> -->
                        <strong id='info-value'></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">

            @if (session()->has('success'))
                <x-adminlte-alert theme="success" title="Success" dismissable>
                    {{ session()->get('success') }}
                </x-adminlte-alert>
            @endif

            @if (session()->has('error'))
                <x-adminlte-alert theme="danger" title="Error" dismissable>
                    {{ session()->get('error') }}
                </x-adminlte-alert>
            @endif
            <div class="textarea-import ">
                <form class="row" action="{{ route('catalog.buybox.upload.file') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="col-3"></div>

                    <div class="col-6 ">
                        <div class="card">
                            <div class="card-header source">
                                <label for="Select Source">Select Source</label>
                                <div class="row ">
                                    <div class="col-2">
                                        <label for="IN">IN</label>
                                        <input type="checkbox" class="destination-priority" name="destination[]"
                                            value="IN">
                                    </div>
                                    <div class="col-2">
                                        <label for="US">US</label>
                                        <input type="checkbox" class="destination-priority" name="destination[]"
                                            value="US">
                                    </div>
                                    <div class="col-2">
                                        <label for="AE">AE</label>
                                        <input type="checkbox" class="destination-priority" name="destination[]"
                                            value="AE">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body priority">
                                <label for="Select Priority">Select Priority</label>
                                <div class="row ">
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
                                    <div class="col-2 ">
                                        <label for="P4">P4</label>
                                        <input type="radio" class="destination-priority" name="priority"
                                            value="4">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-area">
                                <x-adminlte-textarea label="ASIN By Text-area" name="text_area" type="text"
                                    rows="6" placeholder="Enter ASIN " id="textarea" />
                                <input type="hidden" name="form_type" value="text_area">

                                <div class="text-center">
                                    <x-adminlte-button label="Upload" theme="primary" class="add_asin"
                                        icon="fas fa-plus" type="submit" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-3"></div>

                </form>
            </div>
            <div class="bulk-import d-none">
                <form class="row" action="{{ route('catalog.buybox.upload.file') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="col-3"></div>

                    <div class="col-6 ">
                        <div class="card">
                            <div class="card-header source">
                                <label for="Select Source">Select Source</label>
                                <div class="row ">
                                    <div class="col-2">
                                        <label for="IN">IN</label>
                                        <input type="checkbox" class="destinationCheck" name="destination[]"
                                            value="IN">
                                    </div>
                                    <div class="col-2">
                                        <label for="US">US</label>
                                        <input type="checkbox" class="destinationCheck" name="destination[]"
                                            value="US">
                                    </div>
                                    <div class="col-2 ">
                                        <label for="AE">AE</label>
                                        <input type="checkbox" class="destinationCheck" name="destination[]"
                                            value="AE">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body priority">
                                <label for="Select Priority">Select Priority</label>
                                <div class="row ">
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
                                    <div class="col-2 ">
                                        <label for="P4">P4</label>
                                        <input type="radio" class="destination-priority" name="priority"
                                            value="4">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bulk">
                                <x-adminlte-input label="Upload ASIN File" name="asin" id="asin"
                                    type="file" />
                                <input type="hidden" name="form_type" value="file_upload">

                                <div class="text-center">
                                    <x-adminlte-button label="Upload" theme="primary" class="add_asin"
                                        id='bulk_import_button' icon="fas fa-plus" type="submit" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-3"></div>
                </form>
            </div>
        </div>
        <div class="col"></div>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $('#text-area').click(function() {
            $('.bulk-import').addClass('d-none');
            $('.textarea-import').removeClass('d-none');
        });

        $('#bulk-import').click(function() {
            $('.bulk-import').removeClass('d-none');
            $('.textarea-import').addClass('d-none');
        });

        $(document).ready(function() {
            $.ajax({
                method: 'get',
                url: "{{ route('buybox.file.management.monitor') }}",
                data: {
                    "module_type": "IMPORT_ASIN_INTO_BUYBOX",
                    "_token": "{{ csrf_token() }}",
                },
                response: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.status == 'Processing') {
                        $('#bulk_import_button').prop('disabled', true);

                        $('.info-msg').removeClass('d-none');
                        $('#info-value').html(response.description);

                    } else if (response.description) {

                        $('.info-msg').removeClass('d-none');
                        $('#info-value').html(response.description);
                    }
                },
            });
        });

        $('#bulk_import_button').click(function() {
            $('#bulk_import_button').html('<i class="fa fa-circle-o-notch fa-spin"></i> Uploading...');
        });


        $('#TruncateBuyBox').click(function() {
            $.ajax({
                method: 'get',
                url: "{{ route('catalog.buybox.count') }}",
                response: 'json',
                success: function(response) {

                    record = '<tr>';
                    $.each(response, function(key, value) {
                        if (key == null) {

                            record += "<td>" + 0 + "</td>"
                        } else {
                            record += "<td>" + value + "</td> "
                        }
                    });
                    record += '</tr>';
                    $('#BuyBoxCount').html(record);
                },
            });
        });
    </script>
@stop
