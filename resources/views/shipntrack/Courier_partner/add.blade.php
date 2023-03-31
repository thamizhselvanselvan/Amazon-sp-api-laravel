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
                        <x-adminlte-input label="User Name:" name="user_name" type="text" placeholder="User Name"
                            value="{{ old('user_name') }}" autocomplete="off" />

                    </div>
                    <div class="col-6">

                        <x-adminlte-select label="Select Courier:" name="courier_name" id="courier_name" type="text"
                            value="{{ old('courier_name') }}">
                            <option value="">Select Courier</option>
                            @foreach ($couriers as $Courier)
                                <option value="{{ $Courier->id }}">{{ $Courier->courier_name }}</option>
                            @endforeach

                        </x-adminlte-select>

                    </div>

                    <div class="col-6">
                        <x-adminlte-select name="status" label="Select Status:" id="status" value="{{ old('status') }}">
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </x-adminlte-select>
                    </div>

                    <div class="col-6">

                        <x-adminlte-select name="source" label="Select Source:" id="source">
                            <option value="">Select source</option>
                            <option value="AE">AE</option>
                            <option value="IN">IN</option>
                            <option value="KSA">KSA</option>
                            <option value="USA">USA</option>
                        </x-adminlte-select>
                    </div>

                    <div class="col-6">

                        <x-adminlte-select name="destination" label="Select Destination:" id="destination">
                            <option value="">Select destination</option>
                            <option value="AE">AE</option>
                            <option value="IN">IN</option>
                            <option value="KSA">KSA</option>
                            <option value="USA">USA</option>
                        </x-adminlte-select>
                    </div>

                    <div class="col-6">
                        <x-adminlte-select name="type" label="Select Type:" id="type">
                            <option value="">Select type</option>
                            <option value="3">Both</option>
                            <option value="2">Domestic</option>
                            <option value="1">International</option>
                        </x-adminlte-select>
                    </div>

                    <div class="col-6">

                        <x-adminlte-select name="time_zone" label="Select Time Zone:" id="time_zone"
                            value="{{ old('time_zone') }}">
                            <option value="">Select Time Zone</option>
                            <option value="Asia/Bahrain">Asia/Bahrain </option>
                            <option value="Asia/Dubai">Asia/Dubai</option>
                            <option value="Asia/Kolkata"> Asia/Kolkata</option>
                            <option value="Asia/Kuwait">Asia/Kuwait</option>
                            <option value="Asia/Riyadh">Asia/Riyadh</option>
                            <option value="Asia/Qatar"> Asia/Qatar</option>

                        </x-adminlte-select>
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="User ID:" name="user_id" id="user_id" type="text"
                            placeholder="User ID" value="{{ old('user_id') }}" autocomplete="off" />
                    </div>
                </div>
                <div class="row justify-content-center">

                    <div class="col-6">
                        <x-adminlte-input label="Password:" id="Password" name="password" type="text"
                            placeholder="Password" value="{{ old('password') }}" autocomplete="off" />
                    </div>

                    <div class="col-6">
                        <x-adminlte-input label="Account ID:" name="account_id" id="account_id" type="text"
                            placeholder="account_id" value="{{ old('account_id') }}" autocomplete="off" />
                    </div>

                    <div class="col-6">
                        <x-adminlte-input label="Key1:" name="key1" type="text" placeholder="Key1"
                            value="{{ old('key1') }}" autocomplete="off" />
                    </div>

                    <div class="col-6">
                        <x-adminlte-input label="Key2:" name="key2" type="text" placeholder="Key2"
                            value="{{ old('key2') }}" autocomplete="off" />
                    </div>
                </div>
                <div class="row justify-content-center">
                    <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-save" type="submit" />
                </div>
            </form>
        </div>

    </div>
@stop

@section('js')

@stop
