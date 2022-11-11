@extends('adminlte::page')

@section('title', 'ASIN Source')

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="/catalog/asin-source" class="btn btn-primary">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <input type="radio" name="Asin-destination" id="text-area" checked />
            <label for="Text-area" class="ml-1">Text-Area</label>
            <input type="radio" name="Asin-destination" id="bulk-asin" class="ml-2" />
            <label for="Text-area" class="ml-1">Bulk Asin Import</label>
            <h1 class="m-0 text-dark text-center ">Add ASIN Source</h1>
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
        <div class="col"></div>
        <div class="col-8">

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
            <div class="text-area ">
                <form class="row" action="{{ route('catalog.asin.import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="col-3"></div>

                    <div class="col-6">
                        <!-- <x-adminlte-select name="source" label="Select Source" id="source">
                                                                                
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
                                <input type="checkbox" name="source[]" value="IN">
                            </div>
                            <div class="col-2">
                                <label for="US">US</label>
                                <input type="checkbox" name="source[]" value="US">
                            </div>
                            {{-- <div class="col-2">
                                <label for="AE">AE</label>
                                <input type="checkbox" name="source[]" value="AE">
                            </div> --}}
                        </div>
                        <x-adminlte-textarea label="ASIN by text-area" name="text_area" id="asin" type="text"
                            rows="6" placeholder="Enter ASIN" />
                        <input type="hidden" name="form_type" value="text_area" />
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

            <div class="bulk-asin d-none">
                <form class="row" action="{{ route('catalog.asin.import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="col-3"></div>

                    <div class="col-6">
                        <label for="Select Source">Select Source</label><br>
                        <div class="row ">
                            <div class="col-2">
                                <label for="IN">IN</label>
                                <input type="checkbox" name="source[]" value="IN">
                            </div>
                            <div class="col-2">
                                <label for="US">US</label>
                                <input type="checkbox" name="source[]" value="US">
                            </div>
                            {{-- <div class="col-2 ">
                                <label for="AE">AE</label>
                                <input type="checkbox" name="source[]" value="AE">
                            </div> --}}
                        </div>
                        <x-adminlte-input label="Upload ASIN File" name="asin" id="asin" type="file" />
                        <input type="hidden" name="form_type" value="file_upload" />
                    </div>

                    <div class="col-3"></div>

                    <div class="col-12">
                        <div class="text-center">
                            <x-adminlte-button label="Upload" theme="primary" class="add_asin" id="bulk_import_button"
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
    <script>
        $('#text-area').click(function() {
            $('.bulk-asin').addClass('d-none');
            $('.text-area').removeClass('d-none');
        });

        $('#bulk-asin').click(function() {
            $('.bulk-asin').removeClass('d-none');
            $('.text-area').addClass('d-none');
        });

        $(document).ready(function() {
            $.ajax({
                method: 'get',
                url: "/catalog/source/file/monitor/",
                data: {
                    "module_type": "IMPORT_ASIN_SOURCE",
                    "_token": "{{ csrf_token() }}",
                },
                response: 'json',
                success: function(response) {
                    console.log(response);
                    if (response == '0000-00-00 00:00:00') {

                        $('#bulk_import_button').prop('disabled', true);
                    }

                },
            });
        });
    </script>
@stop
