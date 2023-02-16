@extends('adminlte::page')

@section('title', 'Credentials')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ Route('credentials.home') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
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

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Credentials</h3>
            </div>


            <form action="{{route('credentials.home')}}" method="POST">
                @csrf
                <div class="card-body">
                <div class="form-group">
                        <x-adminlte-select name="company" label="Company">
                            @foreach ($companys as $company)
                                <option value="{{$company->id}}">{{$company->company_name}}</option>
                            @endforeach
                    </x-adminlte-select>
                    </div>
                    <div class="form-group">
                        <x-adminlte-input label="Store Name" name="store_name" id="store-name" type="text" placeholder="Store Name" value="{{old('store_name')}}" />
                    </div>
                    <div class="form-group">
                        <x-adminlte-input label="Seller/Merchant ID" name="seller_id" id="seller-id" type="text" placeholder="Merchant ID" value="{{old('seller_id')}}" />
                    </div>
                    <div class="form-group">
                        <x-adminlte-input label="Auth Code" name="auth_code" id="auth-code" type="text" placeholder="Auth Code" value="{{old('auth_code')}}"/>
                    </div>
                    <div class="form-group">
                        <x-adminlte-select name="marketplace_id" label="Marketplace ID">
                        @foreach ($regions as $region)
                                <option value="{{$region->id}}">{{$region->marketplace_id}},{{$region['currency']->name}},{{$region->region_code}}</option>
                            @endforeach
                    </x-adminlte-select>
                    </div>
                </div>
                <div class="card-footer">
                    <x-adminlte-button label="Save credentials" theme="primary" icon="fas fa-plus" type="submit" />
                </div>
            </form>
        </div>
    </div>
    <div class="col"></div>
</div>

@stop