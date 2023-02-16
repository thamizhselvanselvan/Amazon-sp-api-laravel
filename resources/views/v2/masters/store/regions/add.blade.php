@extends('adminlte::page')

@section('title', 'Add Region')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ Route('regions.home') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
        <h1 class="mb-1 text-dark font-weight-bold text-center">Add Regions </h1>
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
    <div class="col-8">

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
        <form class="row" method="POST" action="{{route('regions.home')}}">
            @csrf
            <div class="col-6">
                <div class="form-group">
                    <x-adminlte-input label="Region Code" name="region_code" id="region_code" type="text" placeholder="Region Code" value="{{old('region_code')}}" />
                </div>
                <div class="form-group">
                    <x-adminlte-input label="Region" id="region" name="region" type="text" placeholder="Region" value="{{old('region')}}" />
                </div>
                <div class="form-group">
                    <x-adminlte-input label="Marketplace ID" id="marketplace_id" name="marketplace_id" type="text" placeholder="Marketplace ID" value="{{old('marketplace_id')}}" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <x-adminlte-input label="URL" id="url" name="url" type="text" placeholder="URL" value="{{old('url')}}" />
                </div>
                <div class="form-group">
                    <x-adminlte-input label="Site URL" id="site_url" name="site_url" type="text" placeholder="Site URL" value="{{old('site_url')}}" />
                </div>
                <div class="form-group">
                    <x-adminlte-select id="currency_id" name="currency_id" label="Currency">
                        @foreach ($currencies as $currency)
                        <option value="{{$currency->id}}">{{$currency->name}}</option>
                        @endforeach
                    </x-adminlte-select>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <x-adminlte-select id="status" name="status" label="Status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </x-adminlte-select>
                </div>
            </div>
            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label=" Add  Region" theme="primary" icon="fas fa-plus" type="submit" />
                </div>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>
</div>
</div>

</div>
@stop