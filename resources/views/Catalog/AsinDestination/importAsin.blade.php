@extends('adminlte::page')

@section('title', 'ASIN Destiation')

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ route('Asin.destination.index') }}" class="btn btn-primary">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">

            <input type="radio" name="Asin-destination" id="text-area" checked />
            <label for="Text-area" class="ml-1">Text-Area</label>
            <input type="radio" name="Asin-destination" id="bulk-import" class="ml-2" />
            <label for="Text-area" class="ml-1">Bulk Asin Import</label>
            <h1 class="m-0 text-dark text-center ">Add ASIN Destination</h1>

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
                <form class="row" action="{{ route('catalog.asin.destination.file') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="col-2"></div>

                    <div class="col-6 ">
                        <!-- <x-adminlte-select name="destination" label="Select Source" id="source">
                                                                                                                                                                                                                                                            
                                                                                                                                                                                                                                                            <option value="">Select Source</option>
                                                                                                                                                                                                                                                            <option value="IN">IN</option>
                                                                                                                                                                                                                                                            <option value="AE">AE</option>
                                                                                                                                                                                                                                                            <option value="US">US</option>
                                                                                                                                                                                                                                                            <option value="UK">UK</option>

                                                                                                                                                                                                                                                        </x-adminlte-select> -->
                        <label for="Select Source">Select Source</label><br>
                        <div class="row ">
                            <div class="col-2">
                                <label for="IN">IN</label>
                                <input type="checkbox" class="destination-priority" name="destination[]" value="IN">
                            </div>
                            <div class="col-2">
                                <label for="US">US</label>
                                <input type="checkbox" class="destination-priority" name="destination[]" value="US">
                            </div>
                            <div class="col-2">
                                <label for="AE">AE</label>
                                <input type="checkbox" class="destination-priority" name="destination[]" value="AE">
                            </div>
                            <div class="col-2">
                                <label for="AE">KSA</label>
                                <input type="checkbox" class="destination-priority" name="destination[]" value="SA">
                            </div>
                        </div>

                        <label for="Select Priority" class="mt-2">Select Priority</label><br>
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
                        </div>
                        <x-adminlte-textarea label="ASIN By Text-area" name="text_area" type="text" rows="6"
                            placeholder="Enter ASIN " id="textarea" />
                        <input type="hidden" name="form_type" value="text_area">
                    </div>

                    <div class="col-3"></div>

                    <div class="col-12">
                        <div class="text-center">
                            <x-adminlte-button label="Upload" theme="primary" class="add_asin" icon="fas fa-plus"
                                type="submit" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="bulk-import d-none">
                <form class="row" action="{{ route('catalog.asin.destination.file') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="col-3"></div>

                    <div class="col-6 ">
                        <label for="Select Source">Select Source</label><br>
                        <div class="row ">
                            <div class="col-2">
                                <label for="IN">IN</label>
                                <input type="checkbox" class="destinationCheck" name="destination[]" value="IN">
                            </div>
                            <div class="col-2">
                                <label for="US">US</label>
                                <input type="checkbox" class="destinationCheck" name="destination[]" value="US">
                            </div>
                            <div class="col-2 ">
                                <label for="AE">AE</label>
                                <input type="checkbox" class="destinationCheck" name="destination[]" value="AE">
                            </div>
                            <div class="col-2 ">
                                <label for="AE">KSA</label>
                                <input type="checkbox" class="destinationCheck" name="destination[]" value="SA">
                            </div>
                        </div>
                        <label for="Select Priority" class="mt-2">Select Priority</label><br>
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
                        </div>
                        <x-adminlte-input label="Upload ASIN File" name="asin" id="asin" type="file" />
                        <input type="hidden" name="form_type" value="file_upload">

                    </div>

                    <div class="col-3"></div>

                    <div class="col-12">
                        <div class="text-center">
                            <x-adminlte-button label="Upload" theme="primary" class="add_asin" id='bulk_import_button'
                                icon="fas fa-plus" type="submit" />
                        </div>
                    </div>
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
                // url: "/catalog/file/monitor/",
                url: "{{ route('destination.file.management.monitor') }}",
                data: {
                    "module_type": "IMPORT_ASIN_DESTINATION",
                    "_token": "{{ csrf_token() }}",
                },
                response: 'json',
                success: function(response) {
                    // console.log(response);
                    // if (response == '0000-00-00 00:00:00') {

                    //     $('#bulk_import_button').prop('disabled', true);
                    //     $('#bulk_import_button').attr("title", "File is importing...");
                    // }

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
    </script>
@stop
