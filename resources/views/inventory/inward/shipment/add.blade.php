@extends('adminlte::page')

@section('title', 'Add Shipment')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ route('shipments.index') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center">Add Shipment</h1>
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

        <form action=" {{ route('shipments.store') }}" method="POST" id="admin_user">


            @csrf

            <div class="row justify-content-center">
                <div class="col-9">

                    <x-adminlte-select name="" label="Select Source">
                        <option>Select Source</option>
            

                    </x-adminlte-select>

                </div>

            </div>


            <div class="row justify-content-center">

                <div class="col-9">
                    <x-adminlte-input label="Shipment ID" name="name" id="ID" type="text" placeholder="Shipment ID" value="{{ old('name') }}" />
                </div>

                <div class="col-9">
                    <x-adminlte-input label=" ASIN" name="ASIN" id="ASIN" type="text" placeholder=" ASIN" value="{{ old('name') }}" />
                </div>
            </div>


            <div class="text-center">
                <x-adminlte-button label="Submit" theme="primary" icon="fas fa-plus" type="submit" />
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop