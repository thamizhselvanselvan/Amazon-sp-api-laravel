@extends('adminlte::page')

@section('title', 'Exchange Rate')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row">
    <div class="col">
        <h1 class="m-0 text-dark">Catalog Exchange Rate</h1>
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

<div class="row">
    <div class="col"></div>
    <div class="col">
        <div class="row">
            <div class="col"></div>
            <div class="col"></div>
        </div>
        <form action="">
            <x-adminlte-select label="Choose Source-Destination" name="source-destination">
                <option value="NULL"> Select Source Destination </option>
                <option value="ind_to_uae"> IND TO UAE </option>
                <option value="ind_to_sg"> IND TO SG </option>
                <option value="ind_to_sa"> IND TO SA </option>
                <option value="usa_to_sg"> USA TO SG </option>
                <option value="usa_to_uae"> USA TO UAE </option>
                <option value="usa_to_ind_b2c"> USA TO IND B2C </option>
                <option value="usa_to_ind_b2b"> USA TO IND B2B </option>
            </x-adminlte-select>
            <x-adminlte-input label="Base Weight" type="text" name="base_weight" placeholder="Base Weight" />
            <x-adminlte-input label="Base Shipping Charge" type="text" name="base_shipping_charge" placeholder="Base Shipping Charge" />
        </form>
    </div>
    <div class="col"></div>
</div>

@stop

