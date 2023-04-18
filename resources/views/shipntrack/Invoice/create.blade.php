@extends('adminlte::page')

@section('title', 'SNT Invoice Add')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
<style>
    .align {
        background: lightgray;
        border-radius: 10px;
        padding: 15px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
        width: 80%;
        margin: auto;
        grid-gap: 15px;
        margin-top: 20px
    }

    .form-group {
        margin-bottom: -10px;
    }
</style>
@stop
@section('content_header')
<div class="row">
    <div class="col-2"></div>
    <div class="col-3">
        <a href="{{ route('shipntrack.invoice') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
    <div class="col-3">
        <h1 class="m-0 text-dark text-center">Add Invoice Details</h1>
    </div>
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
    </div>
</div>
<form action="{{ Route('shipntrack.invoice.store') }}" method="post" id="admin_user">
    @csrf
    <div class="align">

        <div>
            <x-adminlte-input label="Invoice Number:" name="invoice_no" type="text" placeholder="invoice_no..." value="{{ old('invoice_no') }}" />
        </div>

        <div>
            <x-adminlte-input label="AWB No. :" name="awb_no" type="text" placeholder="awb_no" value="{{ old('awb_no') }}" autocomplete="off" />
        </div>
        <div>
            <!-- <x-adminlte-input label="Mode :" name="mode" type="text" placeholder="mode" value="{{ old('mode') }}" autocomplete="off" /> -->
            <x-adminlte-select name="mode" label="mode:" id="mode" >
                <option value="0">Select Mode</option>
                <option value="IND2UAE">IND2UAE</option>
                <option value="USA2UAE">USA2UAE</option>
            </x-adminlte-select>
        </div>
        <div>
            <x-adminlte-input label="Invoice Date :" name="invoice_date" type="date" class="form-control" placeholder=" invoice_date" value="{{ old('invoice_date') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="SKU :" name="sku" type="text" placeholder="sku" value="{{ old('sku') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="channel :" name="channel" type="text" placeholder="channel" value="{{ old('channel') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Shipped By :" name="shipped_by" type="text" placeholder="shipped_by" value="{{ old('shipped_by') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="ARN NO :" name="arn_no" type="text" placeholder="arn_no" value="{{ old('arn_no') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Store Name :" name="store_name" type="text" placeholder="store_name" value="{{ old('store_name') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Store Address :" name="store_add" type="text" placeholder="store_add" value="{{ old('store_add') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Bill To Name :" name="bill_to_name" type="text" placeholder="bill_to_name" value="{{ old('bill_to_name') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Bill To Address :" name="bill_to_add" type="text" placeholder="bill_to_add" value="{{ old('bill_to_add') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Ship To Name :" name="ship_to_name" type="text" placeholder="ship_to_name" value="{{ old('ship_to_name') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Ship To Address :" name="ship_to_add" type="text" placeholder="ship_to_add" value="{{ old('ship_to_add') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Item Description :" name="item_description" type="text" placeholder="item_description" value="{{ old('item_description') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="HSN Code :" name="hsn_code" type="text" placeholder="hsn_code" value="{{ old('hsn_code') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Quantity :" name="quantity" type="text" placeholder="quantity" value="{{ old('quantity') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Currency :" name="currency" type="text" placeholder="currency" value="{{ old('currency') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Product Price :" name="product_price" type="text" placeholder="product_price" value="{{ old('product_price') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Taxable Value :" name="taxable_value" type="text" placeholder="taxable_value" value="{{ old('taxable_value') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Total Including Taxes :" name="total_including_taxes" type="text" placeholder="total_including_taxes" value="{{ old('total_including_taxes') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Grand Total :" name="grand_total" type="text" placeholder="grand_total" value="{{ old('grand_total') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="SR No :" name="sr_no" type="text" placeholder="sr_no" value="{{ old('sr_no') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Client Code :" name="client_code" type="text" placeholder="client_code" value="{{ old('client_code') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Packing :" name="packing" type="text" placeholder="packing" value="{{ old('packing') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Number of Pieces :" name="no_of_pcs" type="text" placeholder="no_of_pcs" value="{{ old('no_of_pcs') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Dimension :" name="dimension" type="text" placeholder="dimension" value="{{ old('dimension') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Actual Weight :" name="actual_weight" type="text" placeholder="actual_weight" value="{{ old('actual_weight') }}" autocomplete="off" />
        </div>
        <div>
            <x-adminlte-input label="Charged Weight :" name="charged_weight" type="text" placeholder="charged_weight" value="{{ old('charged_weight') }}" autocomplete="off" />
        </div>
        <div>
            <div>
                <div style="margin-top:2.0rem">
                    <x-adminlte-button label=" Submit" theme="info" icon="fas fa-save" type="submit" />
                </div>
            </div>
        </div>
    </div>
</form>



@stop

@section('js')
<script type="text/javascript">

</script>
@stop