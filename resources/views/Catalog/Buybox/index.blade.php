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
    </script>
@stop
