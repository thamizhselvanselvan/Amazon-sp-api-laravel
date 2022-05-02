@extends('adminlte::page')

@section('title', 'Add Warehouse')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

    <div class="row">
        <div class="col">
             <a href="{{ route('warehouses.index') }}" class="btn btn-primary">  
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center">Add Warehouse</h1>
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

             <form action="{{ route('warehouses.store') }}" method="POST" id="admin_user">
               @csrf

                <div class="row justify-content-center">
                    <div class="col-6">
                        <x-adminlte-input label="Name" name="name" type="text" placeholder="Name"
                            value="{{ old('name') }}" />
                    </div>

                    <div class="col-6">
                        <x-adminlte-input label="Address 1" name="address_1" type="text" placeholder="Address 1"
                            value="{{ old('name') }}" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Address 2" name="address_2" type="text" placeholder="Address 2"
                            value="{{ old('name') }}" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="city" name="city" type="text" placeholder="City"
                            value="{{ old('name') }}" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="State" name="state" type="text" placeholder="State"
                            value="{{ old('name') }}" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Country" name="country" type="text" placeholder="Country"
                            value="{{ old('name') }}" />
                    </div>
                </div>
                    <div class="col-6">
                        <x-adminlte-input label="Pin Code" name="pin_code" type="text" placeholder="Pin Code"
                            value="{{ old('name') }}" />
                    
                </div>
    
                <div class="row justify-content-center">
                    <div class="col-6">
                        <x-adminlte-input label="Contact person name" name="contact_person_name" type="text" placeholder="Person"
                            value="{{ old('name') }}" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Phone No" name="phone_number" type="text" placeholder="Phone No"
                            value="{{ old('name') }}" />
                    </div>
                </div>
                    <div class="col-6">
                        <x-adminlte-input label="Email" name="email" type="text" placeholder="Email"
                            value="{{ old('name') }}" />
                    </div>
                

                <div class="text-center">
                    <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-plus" type="submit" />
                </div>
                

            </form>
        </div>
        <div class="col"></div>
    </div>

@stop  
