@extends('adminlte::page')

@section('title', 'BuyBox Export')

@section('css')

@stop

@section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark"><b>BuyBox ASIN Export </b> </h1>
        <div class="col d-flex justify-content-end">

            <h2 class="ml-2">
                <x-adminlte-button label="Export BuyBox" theme="primary" class="btn-sm" icon="fas fa-file-export"
                    id="exportBuyBox" data-toggle="modal" data-target="#BuyBoxExport" />
            </h2>

            <h2 class="ml-2">
                <x-adminlte-button label="Download BuyBox" theme="primary" class="btn-sm" icon="fas fa-download"
                    id="downloadBuyBox" data-toggle="modal" data-target="#downloadBuyBox" />
            </h2>
        </div>

        <div class="modal fade" id="BuyBoxExport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Export BuyBox</h5>
                        <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="font-size:15px">
                        <form action="{{ route('catalog.buybox.export.csv') }}" method="POST">
                            @csrf
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
                                <input type="hidden" name="export_type" value="via_priority">
                                <x-adminlte-button label="Export" theme="success" class="btn btn-sm "
                                    icon="fas fa-file-export " type="submit" id="buyboxExport" />
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="downloadBuyBox">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Download BuyBox Zip</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body BuyBoxFiles">

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
                <form class="row" action="{{ route('catalog.buybox.export.csv') }}" method="POST"
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
                                        <input type="checkbox" class="destination-priority" name="source"
                                            value="IN">
                                    </div>
                                    <div class="col-2">
                                        <label for="US">US</label>
                                        <input type="checkbox" class="destination-priority" name="source"
                                            value="US">
                                    </div>
                                    <div class="col-2">
                                        <label for="AE">AE</label>
                                        <input type="checkbox" class="destination-priority" name="source"
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
                                <input type="hidden" name="export_type" value="text_area">

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
        </div>
        <div class="col"></div>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {

            $.ajax({
                method: 'get',
                url: "{{ route('buybox.file.management.monitor') }}",
                data: {
                    "module_type": "BUYBOX_EXPORT",
                    "_token": "{{ csrf_token() }}",
                },
                response: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.status == 'Processing') {
                        $('#buyboxExport').prop('disabled', true);

                        $('.info-msg').removeClass('d-none');
                        $('#info-value').append(response.description);

                    } else if (response.description) {

                        $('.info-msg').removeClass('d-none');
                        $('#info-value').append(response.description);
                    }

                },
            });
        });
    </script>
@stop
