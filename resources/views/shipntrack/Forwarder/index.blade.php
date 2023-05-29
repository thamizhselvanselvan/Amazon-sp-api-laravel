@extends('adminlte::page')

@section('title', 'Forwarder Mapping')
@section('css')
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .align {
            background: wheat;
            border-radius: 10px;
            padding: 15px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            width: 70%;
            margin: auto;
            grid-gap: 15px;
            margin-top: 20px
        }

        .form-group {
            margin-bottom: 0px;
        }

        .alert {
            padding: 0.45rem 1.25rem;
            font-size: 14px;
        }
    </style>
@stop
@section('content_header')
    <div class="row">
        <div class="col-3">
            <h1 class="m-0 text-dark col">Forwarder Mapping</h1>
        </div>
        <div class="col-6">
            <div class="alert alert-warning" role="alert">
                Please Choose<b> Source-Destination </b>Before Creating Or Editing Shipment...
            </div>
        </div>
        <!-- <h2 class="mb-4 text-right col">
                <a href="{{ Route('shipntrack.forwarder.template') }}">
        <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download" class="btn-sm" />
        </a>
        <a href="{{ Route('shipntrack.forwarder.upload') }}">
            <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-plus" class="btn-sm" />
        </a>
        <a href="{{ Route('shipntrack.missing.find') }}">
            <x-adminlte-button label="Export Order ID's And AWB Number" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        </h2> -->

        {{-- <div class="col-2 temp d-none">
        <x-adminlte-button label="Edit Shipment" type="edit" name="edit" theme="success" icon="fa fa-edit" class="float-right" id="edit_shipment" />
    </div>
    <div class="col">
        <a href="{{ route('shipntrack.courier.track') }}">
            <x-adminlte-button label="Get Details" type="submit" name="GetDetails" theme="primary" icon="fa fa-refresh" class="float-right" id="dashboard_refresh" />
        </a>
    </div> --}}
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
    <form action="{{ Route('shipntrack.forwarder.store.forwarder') }}" method="post" id="admin_user">
        @csrf

        <div class="col-2">
            <div style="margin-top: -1.4rem;">
                <x-adminlte-select name="destination" label="Source-Destination" id="destination">
                    <option value="">Source-Destination</option>
                    @foreach ($destinations as $destination)
                        <option value={{ $destination['destination'] }}>
                            {{ $destination['source'] . '-' . $destination['destination'] }}
                        </option>
                    @endforeach
                </x-adminlte-select>
            </div>
        </div>

        <div class="align">

            <div>
                <x-adminlte-input label="Enter Reference ID:" name="reference" id="refrence" type="text"
                    placeholder="RefrenceID..." value="{{ old('reference') }}" />
            </div>

            <div>
                <x-adminlte-input label="Consignor :" name="consignor" type="text" placeholder="Consignor"
                    value="{{ old('consignor') }}" autocomplete="off" />
            </div>
            <div>
                <x-adminlte-input label="Consignee :" name="consignee" type="text" placeholder="Consignee"
                    value="{{ old('consignee') }}" autocomplete="off" />
            </div>

            <div></div>
            <div>
                <x-adminlte-select label="Select Forwarder 1:" name="forwarder1" id="forwarder_info_1"
                    value="{{ old('forwarder2') }}">
                    <option value=''> Forwarder 1</option>

                </x-adminlte-select>
            </div>
            <div>
                <x-adminlte-select label="Select Forwarder 2:" name="forwarder2" id="forwarder_info_2"
                    value="{{ old('forwarder2') }}">
                    <option value=''> Forwarder 2</option>
                </x-adminlte-select>
            </div>
            <div>
                <x-adminlte-select label="Select Forwarder 3:" name="forwarder3" id="forwarder_info_3"
                    value="{{ old('forwarder3') }}">
                    <option value=''> Forwarder 3</option>
                </x-adminlte-select>
            </div>
            <div>
                <x-adminlte-select label="Select Forwarder 4:" name="forwarder4" id="forwarder_info_4"
                    value="{{ old('forwarder2') }}">
                    <option value=''> Forwarder 4</option>

                </x-adminlte-select>
            </div>

            <div>
                <x-adminlte-input label="Forwarder 1 AWB :" name="forwarder_1_awb" type="text"
                    placeholder="Forwarder 1 AWB " value="{{ old('forwarder_1_awb') }}" autocomplete="off" />
            </div>
            <div>
                <x-adminlte-input label="Forwarder 2 AWB :" name="forwarder_2_awb" type="text"
                    placeholder="Forwarder 2 AWB " value="{{ old('forwarder_2_awb') }}" autocomplete="off" />
            </div>
            <div>
                <x-adminlte-input label="Forwarder 3 AWB :" name="forwarder_3_awb" type="text"
                    placeholder="Forwarder 3 AWB " value="{{ old('forwarder_3_awb') }}" autocomplete="off" />
            </div>
            <div>
                <x-adminlte-input label="Forwarder 4 AWB :" name="forwarder_4_awb" type="text"
                    placeholder="Forwarder 4 AWB " value="{{ old('forwarder_4_awb') }}" autocomplete="off" />
            </div>

            <div>
                <div>
                    <x-adminlte-button label=" Submit" theme="info" icon="fas fa-save" type="submit" />
                </div>
            </div>

        </div>
    </form>

@stop

@section('js')
    <script type="text/javascript">
        $("#destination").on('change', function(e) {
            $(".temp").removeClass("d-none")
            let destination = $(this).val();
            if (destination != 'NULL') {

                $.ajax({
                    method: 'get',
                    url: "{{ route('shipntrack.forwarder.select.view') }}",
                    data: {
                        'destination': destination,

                        "_token": "{{ csrf_token() }}",
                    },
                    'dataType': 'json',
                    success: function(result) {
                        $('#forwarder_info_1').empty();
                        $('#forwarder_info_2').empty();
                        $('#forwarder_info_3').empty();
                        $('#forwarder_info_4').empty();
                        let forwarder_data = "<option value='' >" + 'Select Forwarder' + "</option>";
                        $.each(result, function(i, result) {
                            forwarder_data += "<option value='" + result.id + "'>" + result
                                .user_name +
                                "</option>";
                        });
                        $('#forwarder_info_1').append(forwarder_data);
                        $('#forwarder_info_2').append(forwarder_data);
                        $('#forwarder_info_3').append(forwarder_data);
                        $('#forwarder_info_4').append(forwarder_data);
                    }
                });
            }
        });




        $("#edit_shipment").on('click', function(e) {

            let destination = $('#destination').val();
            if (destination == '') {
                alert('Source-Destination is Required');
                return false;
            }
            window.location = "/shipntrack/shipment/edit/" + destination;

        });
    </script>
@stop
