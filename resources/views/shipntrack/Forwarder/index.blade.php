@extends('adminlte::page')

@section('title', 'Forwarder Mapping')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Forwarder Mapping</h1>
    <h2 class="mb-4 text-right col">
        <a href="{{Route('shipntrack.forwarder.upload')}}">
            <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-plus" class="btn-sm" />
        </a>
        <a href="{{Route('shipntrack.forwarder.template')}}">
            <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download" class="btn-sm" />
        </a> <a href="{{Route('shipntrack.missing.find')}}">
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

<form action="{{Route('shipntrack.forwarder.search')}}" method="post" id="admin_user">
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
</form>
<div class="pl-2">
    <!-- <table class="table table-bordered yajra-datatable table-striped text-center" style="line-height:12px">
        <thead>
            <tr>
                <th>ID</th>
                <th>AWB No.</th>
                <th>Date</th>
                <th>Activity</th>
                <th>Status</th>
                <th>Location</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody> -->
    </table>
</div>
@stop

@section('js')
<script type="text/javascript">
    // $('#update_form').hide();
    $("#oid").on('click', function(e) {
        let orderid = $('#orderid').val();
        if (orderid == '') {
            alert('Order ID Requirerd');
            return false;
        } else {
            $('#update_form').show();
        }

    });
</script>
@stop