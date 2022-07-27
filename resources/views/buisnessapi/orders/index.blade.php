@extends('adminlte::page')

@section('title', ' Orders API ')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<h1 class="m-0 text-dark">Order Request</h1>
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

<div class="row">
    <div class="col-2">
        <x-adminlte-input label="Order Date:" name="orderDate" id="date" type="text" placeholder=" Enter  Order Date...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Order ID:" name="OrderID" id="orderid" type="text" placeholder=" Enter OrderID...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="order Type:" name="orderType" id="orderType" placeholder="order Type...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Total Amount:" name="totalamt" id="totalamt" placeholder="total amount...." />
    </div>
</div>
<div class="row">
    <h6 class="m-0 text-dark">Destination :</h6><br>
</div>

<div class="row">
    <div class="col-2">
        <x-adminlte-input label="Deliver To:" name="deliverto" id="deliverto" type="text" placeholder=" Enter deliver to...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Street:" name="Street" id="Street" type="text" placeholder=" Enter Street...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="City:" name="City" id="City" type="text" placeholder=" Enter City...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="State:" name="State" id="State" type="text" placeholder=" Enter State...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Post-code:" name="post" id="post" type="text" placeholder=" Enter postal code...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Email:" name="Email" id="email" type="text" placeholder=" Enter Email...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="PH No.:" name="phno" id="phno" type="text" placeholder=" Enter Phone NO...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Area Code.:" name="AreaCode" id="area_code" type="text" placeholder=" Enter Area Code...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Country code:" name="code" id="code" type="text" placeholder=" Enter Country Code...." />
    </div>
</div>
<div class="row">
    <h6 class="m-0 text-dark">Bill To :</h6><br>
</div>
<div class="row">
    <div class="col-2">
        <x-adminlte-input label="Price:" name="Price" id="price" type="text" placeholder=" Enter Price...." />
    </div>
</div>
<div class="row">
    <h6 class="m-0 text-dark">Item Description :</h6>
</div>

<div class="row">
    <div class="col-2">
        <x-adminlte-input label="quantity:" name="quantity" id="quantity" type="text" placeholder=" Enter quantity...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Supplier Part ID:" name="SupplierPartID" id="SupplierPartID" type="text" placeholder=" Enter SupplierPartID...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Supplier Part Auxiliary ID:" name="AuxiliaryID" id="AuxiliaryID" type="text" placeholder=" Enter SupplierPartAuxiliaryID...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="price:" name="price" id="price1" type="text" placeholder=" Enter price...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Description:" name="Description" id="Description" type="text" placeholder=" Enter Description...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Manufacturer Part ID:" name="ManufacturerPartID" id="ManufacturerPartID" type="text" placeholder=" Enter ManufacturerPartID.." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="Manufacturer Name:" name="ManufacturerName" id="ManufacturerName" type="text" placeholder=" Enter ManufacturerName...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="sold By:" name="soldBy" id="soldBy" type="text" placeholder=" Enter sold By...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="fulfilled By:" name="fulfilledBy" id="fulfilledBy" type="text" placeholder=" Enter fulfilled By...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="category:" name="category" id="category" type="text" placeholder=" Enter category...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="sub Category:" name="sub Category" id="subCategory" type="text" placeholder=" Enter sub Category...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="item Condition:" name="itemCondition" id="itemCondition" type="text" placeholder=" Enter item Condition...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="qualified Offer:" name="qualifiedOffer" id="qualifiedOffer" type="text" placeholder=" true or false...." />
    </div>
    <div class="col-2">
        <x-adminlte-input label="preference:" name="preference" id="preference" type="text" placeholder=" Enter preference...." />
    </div>

</div>

<div class="row">
    <div class="col">
        <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="create" class="btn-sm product_search" />
    </div>
</div>

@stop