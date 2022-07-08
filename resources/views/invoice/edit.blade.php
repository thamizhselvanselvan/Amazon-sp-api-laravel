@extends('adminlte::page')
@section('title', 'Search Invoice')

@section('content_header')
<h3 class="register-heading">Update Invoice</h3>
<div class="row">
    <div class="col">
        <a href="{{ route('invoice.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
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
@foreach ($data as $key => $value)
<form action="{{ route('invoice.update', $value->id) }}" method="post">
    @csrf
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label for="">Invoice No.</label>
                    <input tabindex="1" type="text" name="invoice_no" class="form-control" placeholder="Invoice No *"
                        value="{{$value->invoice_no}}" />
                </div>
                <div class="form-group">
                    <label for="">Shipped By</label>
                    <input type="text" name="shipped_by" class="form-control" placeholder="Shipped By"
                        value="{{$value->shipped_by}}" />
                </div>
                <div class="form-group">
                    <label for="">Bill To Name</label>
                    <input tabindex="3" type="text" name="bill_to_name" class="form-control" placeholder="Bil To Name"
                        value="{{$value->bill_to_name}}" />
                </div>
                <div class="form-group">
                    <label for="">Quantity</label>
                    <input type="text" name="qty" class="form-control" placeholder="Quantity" value="{{$value->qty}}" />
                </div>
                <div class="form-group">
                    <label for="">Total Including Tax</label>
                    <input type="text" name="total_including_taxes" class="form-control" placeholder="Including Tax"
                        value="{{$value->total_including_taxes}}" />
                </div>
                <div class="form-group">
                    <label for="">Dimension</label>
                    <input type="text" name="dimension" class="form-control" placeholder="Dimension"
                        value="{{$value->dimension}}" />
                </div>
                <div class="form-group">
                    <label for="">Item Description</label>
                    <textarea class="form-control" name="item_description"
                        placeholder="Item Description">{{$value->item_description}}</textarea>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="">Invoice Date</label>
                    <input tabindex="2" type="text" name="invoice_date" class="form-control" placeholder="Invoice Date"
                        value="{{$value->invoice_date}}" />
                </div>
                <div class="form-group">
                    <label for="">Awb No.</label>
                    <input type="text" name="awb_no" class="form-control" placeholder="Awb No."
                        value="{{$value->awb_no}}" />
                </div>
                <div class="form-group">
                    <label for="">Ship To Name</label>
                    <input type="text" name="ship_to_name" class="form-control" placeholder="Ship To Name"
                        value="{{$value->ship_to_name}}" />
                </div>
                <div class="form-group">
                    <label for="">Currency</label>
                    <input type="text" name="currency" class="form-control" placeholder="Currency"
                        value="{{$value->currency}}" />
                </div>
                <div class="form-group">
                    <label for="">Grand Total</label>
                    <input type="text" name="grand_total" class="form-control" placeholder="CGrand Total"
                        value="{{$value->grand_total}}" />
                </div>
                <div class="form-group">
                    <label for="">Actual Weight</label>
                    <input type="text" name="actual_weight" class="form-control" placeholder="Actual Weight"
                        value="{{$value->actual_weight}}" />
                </div>
                <div class="form-group">
                    <label for="">Bill To Address</label>
                    <textarea class="form-control" name="bill_to_add"
                        placeholder="Bill To Address">{{$value->bill_to_add}}</textarea>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="">Mode</label>
                    <input type="text" name="mode" class="form-control" placeholder="mode " value="{{$value->mode}}" />
                </div>
                <div class="form-group">
                    <label for="">Arn. No.</label>
                    <input type="text" name="arn_no" class="form-control" placeholder="Arn no."
                        value="{{$value->arn_no}}" />
                </div>
                <div class="form-group">
                    <label for="">SKU</label>
                    <input type="text" name="sku" class="form-control" placeholder="SKU" value="{{$value->sku}}" />
                </div>
                <div class="form-group">
                    <label for="">Product Price</label>
                    <input type="text" name="product_price" class="form-control" placeholder="Product Price"
                        value="{{$value->product_price}}" />
                </div>
                <div class="form-group">
                    <label for="">No. Of PCS.</label>
                    <input type="text" name="no_of_pcs" class="form-control" placeholder="No. Of PCS."
                        value="{{$value->no_of_pcs}}" />
                </div>
                <div class="form-group">
                    <label for="">Charged Weight</label>
                    <input type="text" name="charged_weight" class="form-control" placeholder="Charded Weight"
                        value="{{$value->charged_weight}}" />
                </div>
                <div class="form-group">
                    <label for="">Ship To Address</label>
                    <textarea class="form-control" name="ship_to_add"
                        placeholder="Store Address">{{$value->ship_to_add}}</textarea>
                </div>
            </div>
            <div class="col-3">

                <div class="form-group">
                    <label for="">Channel</label>
                    <input type="text" name="channel" class="form-control" placeholder="Channel"
                        value="{{$value->channel}}" />
                </div>
                <div class="form-group">
                    <label for="">Store Name</label>
                    <input type="text" name="store_name" class="form-control" placeholder="Store Name"
                        value="{{$value->store_name}}" />
                </div>
                <div class="form-group">
                    <label for="">Hsn Code</label>
                    <input type="text" name="hsn_code" class="form-control" placeholder="Hsn Code"
                        value="{{$value->hsn_code}}" />
                </div>
                <div class="form-group">
                    <label for="">Taxable Value</label>
                    <input type="text" name="taxable_value" class="form-control" placeholder="Taxable Value"
                        value="{{$value->taxable_value}}" />
                </div>
                <div class="form-group">
                    <label for="">Packing </label>
                    <input type="text" name="packing" class="form-control" placeholder="Packing"
                        value="{{$value->packing}}" />
                </div>
                <div class="form-group">
                    <label for="">Store Address</label>
                    <textarea name="store_add" class="form-control"
                        placeholder="Store Address">{{$value->store_add}}</textarea>
                </div>
                <input type="submit" class="btn btn-success mt-4" value="Update" />
            </div>
        </div>
    </div>
</form>
@endforeach
@stop
