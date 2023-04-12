@extends('adminlte::page')

@section('title', 'Edit')
@section('css')
<link rel="stylesheet" href="/css/styles.css">
<style>
    .align {
        background: wheat;
        border-radius: 15px;
        padding: 15px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        width: 100%;
        margin: auto;
        grid-gap: 15px;
        margin-top: 20px
    }

    .form-group {
        margin-bottom: 0px;
    }
</style>
@stop
@section('content_header')

<div class="row">
    <div class="col-2">

    </div>
    <div class="col-3">
        <a href="{{route('shipntrack.forwarder')}}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>


    <div class="col-2">
        <h1 class="m-0 text-dark text-center ">Edit Shipment</h1>
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
        <div class="alert alert-warning" role="alert">
            Please Enter Destination<b> {{$source_destination}} </b>AWB Numbers To Edit
        </div>
        @if(session()->has('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session()->get('success') }}
        </x-adminlte-alert>
        @endif

        @if(session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session()->get('error') }}
        </x-adminlte-alert>
        @endif
        <div class="row">
            <div class="col-3">
                <x-adminlte-input label="Enter Reference ID:" name="reference" id="refrence" type="text" placeholder="RefrenceID..." value="{{ old('reference') }}" />
            </div>
            <input type="hidden" id="destination" name="destination" value={{$source_destination}}>
            <div class="col-2">
                <div style="margin-top: 2.0rem;">
                    <x-adminlte-button label="Search" theme="info" class="search" id="awb_search" icon="fas fa-search" type="submit" value="" />

                </div>
            </div>
        </div>
        <form action="{{ Route('shipntrack.forwarder.save.edit') }}" method="post" id="admin_user">
            @csrf
            <div class="align d-none">
                <input type="hidden" id="destination" name="destination" value={{$source_destination}}>
                <input type="hidden" id="reference" name="reference" value="">
                <div>
                    <x-adminlte-input label="Consignor :" name="consignor" class="edit" type="text" placeholder="Consignor" value="{{ old('consignor') }}" autocomplete="off" />
                </div>
                <div>
                    <x-adminlte-input label="Consignee :" name="consignee" type="text" placeholder="Consignee" value="{{ old('consignee') }}" autocomplete="off" />
                </div>

                <div></div>
                <div></div>
                <div>
                    <x-adminlte-select label="Select Forwarder 1:" name="forwarder1" id="forwarder_info_1" value="{{ old('forwarder2') }}">
                        <option value=''> Forwarder 1</option>

                    </x-adminlte-select>
                </div>
                <div>
                    <x-adminlte-select label="Select Forwarder 2:" name="forwarder2" id="forwarder_info_2" value="{{ old('forwarder2') }}">
                        <option value=''> Forwarder 2</option>
                    </x-adminlte-select>
                </div>
                <div>
                    <x-adminlte-select label="Select Forwarder 3:" name="forwarder3" id="forwarder_info_3" value="{{ old('forwarder3') }}">
                        <option value=''> Forwarder 3</option>
                    </x-adminlte-select>
                </div>
                <div>
                    <x-adminlte-select label="Select Forwarder 4:" name="forwarder4" id="forwarder_info_4" value="{{ old('forwarder2') }}">
                        <option value=''> Forwarder 4</option>

                    </x-adminlte-select>
                </div>

                <div>
                    <x-adminlte-input label="Forwarder 1 AWB :" name="forwarder_1_awb" id="forwarder_1_awb" type="text" placeholder="Forwarder 1 AWB " value="{{ old('forwarder_1_awb') }}" autocomplete="off" />
                </div>
                <div>
                    <x-adminlte-input label="Forwarder 2 AWB :" name="forwarder_2_awb" id="forwarder_2_awb" type="text" placeholder="Forwarder 2 AWB " value="{{ old('forwarder_2_awb') }}" autocomplete="off" />
                </div>
                <div>
                    <x-adminlte-input label="Forwarder 3 AWB :" name="forwarder_3_awb" id="forwarder_3_awb" type="text" placeholder="Forwarder 3 AWB " value="{{ old('forwarder_3_awb') }}" autocomplete="off" />
                </div>
                <div>
                    <x-adminlte-input label="Forwarder 4 AWB :" name="forwarder_4_awb" id="forwarder_4_awb" type="text" placeholder="Forwarder 4 AWB " value="{{ old('forwarder_4_awb') }}" autocomplete="off" />
                </div>

                <div>
                    <div>
                        <!-- <div style="margin-top: 2.0rem;"> -->
                        <x-adminlte-button label=" Submit" theme="success" icon="fas fa-save" type="submit" />
                        <!-- </div> -->
                    </div>
                </div>

            </div>
        </form>
    </div>
    <div class="col"></div>
</div>
@stop

@section('js')
<script type="text/javascript">
    $("#awb_search").on('click', function(e) {

        let id = $('#refrence').val();
        let destination = $('#destination').val();

        if (id == "") {
            alert("Reference  ID Is Required..!");
            return false;
        }

        $.ajax({
            method: 'get',
            url: "{{ route('shipntrack.forwarder.edit.view') }}",
            data: {
                'id': id,
                'destination': destination,

                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(result) {
                $('.align').removeClass('d-none');

                if (result.hasOwnProperty('eror_data')) {
                    $('.align').addClass('d-none');
                    alert('Invalid Refrence ID Please Check....!!');
                    return false;
                }
                $("#reference").val($("#reference").val() + result.ref_data[0].reference_id);
                $("#consignor").val($("#consignor").val() + result.ref_data[0].consignor);
                $("#consignee").val($("#consignee").val() + result.ref_data[0].consignee);
                $("#forwarder_1_awb").val($("#forwarder_1_awb").val() + result.ref_data[0].forwarder_1_awb);
                $("#forwarder_2_awb").val($("#forwarder_2_awb").val() + result.ref_data[0].forwarder_2_awb);
                $("#forwarder_3_awb").val($("#forwarder_3_awb").val() + result.ref_data[0].forwarder_3_awb);
                $("#forwarder_4_awb").val($("#forwarder_4_awb").val() + result.ref_data[0].forwarder_4_awb);

                $('#forwarder_info_1').empty();
                $('#forwarder_info_2').empty();
                $('#forwarder_info_3').empty();
                $('#forwarder_info_4').empty();
                let forwarder_data_1 = "<option value='' >" + 'Select Forwarder' + "</option>";
                let forwarder_data_2 = "<option value='' >" + 'Select Forwarder' + "</option>";
                let forwarder_data_3 = "<option value='' >" + 'Select Forwarder' + "</option>";
                let forwarder_data_4 = "<option value='' >" + 'Select Forwarder' + "</option>";
                $.each(result.forwarder_data, function(key, values) {
                    if (values.id == result.ref_data[0].forwarder_1) {

                        forwarder_data_1 += "<option value='" + values.id + "' selected>" + values.user_name + "</option>";
                    } else {

                        forwarder_data_1 += "<option value='" + values.id + "'>" + values.user_name + "</option>";
                    }
                    if (values.id == result.ref_data[0].forwarder_2) {

                        forwarder_data_2 += "<option value='" + values.id + "' selected>" + values.user_name + "</option>";
                    } else {

                        forwarder_data_2 += "<option value='" + values.id + "'>" + values.user_name + "</option>";
                    }
                    if (values.id == result.ref_data[0].forwarder_3) {

                        forwarder_data_3 += "<option value='" + values.id + "' selected>" + values.user_name + "</option>";
                    } else {

                        forwarder_data_3 += "<option value='" + values.id + "'>" + values.user_name + "</option>";
                    }
                    if (values.id == result.ref_data[0].forwarder_4) {

                        forwarder_data_4 += "<option value='" + values.id + "' selected>" + values.user_name + "</option>";
                    } else {

                        forwarder_data_4 += "<option value='" + values.id + "'>" + values.user_name + "</option>";
                    }
                });
                $('#forwarder_info_1').append(forwarder_data_1);
                $('#forwarder_info_2').append(forwarder_data_2);
                $('#forwarder_info_3').append(forwarder_data_3);
                $('#forwarder_info_4').append(forwarder_data_4);
            }
        });

    });
</script>
@stop