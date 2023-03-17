@extends('adminlte::page')

@section('title', 'Add Courier Partner')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col-3"></div>
    <div class="col-0.5">
        <a href="{{ route('snt.courier.index') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>


    <div class="col-5">
        <h1 class="m-0 text-dark text-center">Add Courier Partner</h1>
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
    <div class="col-3"></div>
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

        <form action="{{ route('courier.partners.store') }}" method="POST" id="admin_user">
            @csrf

            <div class="row justify-content-center">
                <div class="col-6">
                    <x-adminlte-input label="Name" name="name" type="text" placeholder="Name" value="{{ old('name') }}" />
                </div>

                <div class="col-6">
                    <x-adminlte-select name="status" label="Select status:" id="status" value="{{ old('status') }}">
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </x-adminlte-select>
                </div>

                <div class="col-6">
                    <!-- <x-adminlte-input label="Source" name="source" type="text" placeholder="source" value="{{ old('source') }}" /> -->
                    <x-adminlte-select name="source" label="Select Source:" id="source">
                        <option value="">Select source</option>
                        <option value="IN">IN</option>
                        <option value="USA">USA</option>
                        <option value="AE">AE</option>
                        <option value="KSA">KSA</option>
                    </x-adminlte-select>
                </div>

                <div class="col-6">
                    <!-- <x-adminlte-input label="Destination" name="destination" type="text" placeholder="destination" value="{{ old('destination') }}" /> -->
                    <x-adminlte-select name="destination" label="Select destination:" id="destination">
                        <option value="">Select destination</option>
                        <option value="IN">IN</option>
                        <option value="USA">USA</option>
                        <option value="AE">AE</option>
                        <option value="KSA">KSA</option>
                    </x-adminlte-select>
                </div>

                <div class="col-6">
                    <x-adminlte-select name="type" label="Select type:" id="type">
                        <option value="">Select type</option>
                        <option value="1">International</option>
                        <option value="2">Domestic</option>
                        <option value="3">Both</option>
                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Courier code" name="code" type="text" placeholder="code" value="{{ old('code') }}" />
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-6">
                    <!-- <x-adminlte-input label="Time Zone" name="time_zone" type="text" placeholder="time zone" value="{{ old('time_zone') }}" /> -->
                    <x-adminlte-select name="time_zone" label="Select Time Zone:" id="time_zone" value="{{ old('time_zone') }}">
                        <option value="">Select Time Zone</option>
                        <option value="Asia/Kolkata"> Asia/Kolkata</option>
                        <option value="Asia/Dubai">Asia/Dubai</option>
                        <option value="Asia/Dubai">Asia/Dubai</option>
                        <option value="Asia/Kuwait">Asia/Kuwait</option>
                        <option value="Asia/Riyadh">Asia/Riyadh</option>
                        <option value="Asia/Qatar"> Asia/Qatar</option>
                        <option value="Asia/Bahrain">Asia/Bahrain </option>

                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <x-adminlte-input label="key1" name="key1" type="text" placeholder="key1" value="{{ old('key1') }}" />
                </div>

                <div class="col-6">
                    <x-adminlte-input label="key2" name="key2" type="text" placeholder="key2" value="{{ old('key2') }}" />
                </div>

                <div class="col-6">
                    <x-adminlte-input label="key3" name="key3" type="text" placeholder="key3" value="{{ old('key3') }}" />
                </div>

                <div class="col-6">
                    <x-adminlte-input label="key4" name="key4" type="text" placeholder="key4" value="{{ old('key4') }}" />
                </div>

                <div class="col-6">
                    <x-adminlte-input label="key5" name="key5" type="text" placeholder="key5" value="{{ old('key5') }}" />
                </div>
            </div>
            <div class="row justify-content-center">
                <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-save" type="submit" />
            </div>
        </form>
    </div>

</div>
@stop