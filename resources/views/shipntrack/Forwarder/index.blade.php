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
</style>
@stop
@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Forwarder Mapping</h1>
    <h2 class="mb-4 text-right col">
        <a href="{{Route('shipntrack.forwarder.template')}}">
            <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download" class="btn-sm" />
        </a>
        <a href="{{Route('shipntrack.forwarder.upload')}}">
            <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-plus" class="btn-sm" />
        </a>
        <a href="{{Route('shipntrack.missing.find')}}">
            <x-adminlte-button label="Export Order ID's And AWB Number" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
    </h2>
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
<form action="{{Route('shipntrack.forwarder.store.forwarder')}}" method="post" id="admin_user">
    @csrf
    <div style="margin-top: -1.2rem;">
        <div class="col-2">
            <!-- <x-adminlte-input label="Source" name="source" type="text" placeholder="source" value="{{ old('source') }}" /> -->
            <x-adminlte-select name="mode" label="Select Tracking Mode:" id="mode" vlaue="{{old('mode')}}">
                <option value="">Select Mode</option>
                <option value="IN_AE">IN to UAE</option>
                <option value="IN_KSA">IN to KSA</option>
                <option value="USA_AE">USA to UAE</option>
                <option value="USA_KSA">USA to KSA</option>
            </x-adminlte-select>
        </div>
        <div class="align">

            <div>
                <x-adminlte-input label="Enter Reference ID:" name="refrence" id="refrence" type="text" placeholder="RefrenceID..." value="{{ old('refrence') }}" />
            </div>

            <div>
                <x-adminlte-input label="Consignor :" name="consignor" type="text" placeholder="Consignor " value="{{ old('consignor') }}" />
            </div>
            <div>
                <x-adminlte-input label="Consignee :" name="consignee" type="text" placeholder="Consignee " value="{{ old('consignee') }}" />
            </div>

            <div></div>
            <div>
                <x-adminlte-select label="Select Forwarder 1:" name="forwarder1" id="forwarder_info_1" value="{{ old('forwarder2') }}">
                    <option value=''> Forwarder 1</option>
                    <!-- @foreach ($partners_lists as $partners_list)
                    <option value="{{ $partners_list->courier_code }}">{{$partners_list->name }}</option>
                    @endforeach -->
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
                <x-adminlte-input label="Forwarder 1 AWB :" name="forwarder_1_awb" type="text" placeholder="Forwarder 1 AWB " value="{{ old('forwarder_1_awb') }}" />
            </div>
            <div>
                <x-adminlte-input label="Forwarder 2 AWB :" name="forwarder_2_awb" type="text" placeholder="Forwarder 2 AWB " value="{{ old('forwarder_2_awb') }}" />
            </div>
            <div>
                <x-adminlte-input label="Forwarder 3 AWB :" name="forwarder_3_awb" type="text" placeholder="Forwarder 3 AWB " value="{{ old('forwarder_3_awb') }}" />
            </div>
            <div>
                <x-adminlte-input label="Forwarder 4 AWB :" name="forwarder_4_awb" type="text" placeholder="Forwarder 4 AWB " value="{{ old('forwarder_4_awb') }}" />
            </div>


            <div>
                <div>

                    <x-adminlte-button label=" Submit" theme="info" icon="fas fa-save" type="submit" />
                </div>
            </div>

        </div>
    </div>
</form>


<!-- <form action="{{Route('shipntrack.forwarder.search')}}" method="post" id="admin_user">
    @csrf
    <div class="row">
        <div class="col-2">
            <x-adminlte-input label="Enter Order ID:" name="orderid" id="orderid" type="text" placeholder="orderid...." />
        </div>
        <div class="col ">
            <div style="margin-top: 2.0rem;">
                <x-adminlte-button label="Submit" theme="primary" id="oid" icon="fas fa-plus" type="submit" />
            </div>
        </div>
    </div>
</form>
<form action="{{ route('shipntrack.forwarder.update') }}" method="post" id="update_form">
    @csrf
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-2">
                @if (isset($data[0]->order_id))
                <x-adminlte-input label="Amazon Order Identifier:" name="order_id" id="identifier" value="{{$data[0]->order_id}}" type="text" />
                @else
                <x-adminlte-input label="Amazon Order Identifier:" name="order_id" id="identifier" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->awb_no))
                <x-adminlte-input label="Seller SKU:" name="sku" value="{{$data[0]->seller_sku}}" type="text" />
                @else
                <x-adminlte-input label="Seller SKU:" name="sku" type="text" />
                @endif
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-2">
                <x-adminlte-input label="Source:" name="" value="" type="text" />
            </div>
            <div class="col-2">
                <x-adminlte-input label="Destination:" name="destination" value="" type="text" />
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-2">
                <x-adminlte-select label="Select Forwarder 1:" name="forwarder1">
                    <option value='0'>Select Forwarder 1</option>
                    @foreach ($partners_lists as $partners_list)
                    @if ($partners_list->courier_code == $selected_forwarder_1)
                    <option value="{{ $partners_list->courier_code }}" selected> {{ $partners_list->name }}</option>
                    @else
                    <option value="{{ $partners_list->courier_code }}">{{$partners_list->name }}</option>
                    @endif
                    @endforeach
                </x-adminlte-select>
            </div>
            <div class="col-2">
                @if (isset($data[0]->forwarder_1_awb))
                <x-adminlte-input label="Forwarder 1 AWB:" name="forwarder_1_awb" value="{{$data[0]->forwarder_1_awb}}" type="text" />
                @else
                <x-adminlte-input label="Forwarder 1 AWB:" name="forwarder_1_awb" type="text" />
                @endif
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-2">
                <x-adminlte-select label="Select Forwarder 2:" name="forwarder2">
                    <option value='0'>Select Forwarder 2</option>
                    @foreach ($partners_lists as $partners_list)
                    @if ($partners_list->courier_code == $selected_forwarder_2)
                    <option value="{{ $partners_list->courier_code }}" selected> {{ $partners_list->name }}</option>
                    @else
                    <option value="{{ $partners_list->courier_code }}">{{$partners_list->name }}</option>
                    @endif
                    @endforeach
                </x-adminlte-select>
            </div>
            <div class="col-2">
                @if (isset($data[0]->forwarder_2_awb))
                <x-adminlte-input label="Forwarder 2 AWB:" name="forwarder_2_awb" value="{{$data[0]->forwarder_2_awb}}" type="text" />
                @else
                <x-adminlte-input label="Forwarder 2 AWB:" name="forwarder_2_awb" type="text" />
                @endif
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-2">
            @if (isset($data[0]->awb_no))
            <x-adminlte-input label="AWB No." name="awb_no" value="{{$data[0]->awb_no}}" type="text" />
            @else
            <x-adminlte-input label="AWB No." name="awb_no" type="text" />
            @endif
        </div>
        <div class="col-2">
            <div style="margin-top: 2.0rem;">
                <x-adminlte-button label="Update" theme="success" icon="fas fa-upload" type="submit" />
            </div>
        </div>
    </div>
</form> -->
@stop

@section('js')
<script type="text/javascript">
    $("#mode").on('change', function(e) {
        // $(".align").removeClass("d-none")

        let mode = $(this).val();

        $.ajax({
            method: 'get',
            url: "{{route('shipntrack.forwarder.select.view')}}",
            data: {
                'mode': mode,

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
                    forwarder_data += "<option value='" + result.id + "'>" + result.name + "</option>";
                });
                $('#forwarder_info_1').append(forwarder_data);
                $('#forwarder_info_2').append(forwarder_data);
                $('#forwarder_info_3').append(forwarder_data);
                $('#forwarder_info_4').append(forwarder_data);
            },
            error: function(result) {
                console.log(result);
                alert('Something went Wrong...');
            }
        });


    });
</script>
@stop