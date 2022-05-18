@extends('adminlte::page')

@section('title', 'Add Vendor')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ route('vendors.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h3 class="m-0 text-dark text-center">Add Source/Destination</h3>
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

        <form action="{{ route('vendors.store') }}" method="POST" id="admin_user">

            @csrf

            <div class="row justify-content-center">
                <div class="col-6">
                    <x-adminlte-input label="Name" name="name" id="" type="text" placeholder="Name " value="{{ old('ID') }}" />
                </div>
                <div class="col-6">
                    <x-adminlte-select name="type" label="Select type">
                            <option>Select Type</option>
                            <option>Source</option>
                            <option>Destination</option>
                        </x-adminlte-select>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-6">
                    <x-adminlte-input label="Country" name="country" id="" type="text" placeholder="country" value="{{ old('ID') }}" />
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Currency" name="currency" id="" type="text" placeholder="currency " value="{{ old('ID') }}" />
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

