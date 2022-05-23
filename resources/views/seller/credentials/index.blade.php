@extends('adminlte::page')

@section('title', 'Products')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
    <h1 class="m-0 text-dark text-center font-weight-bolder">Amazon Store Credentials</h1>
@stop


@section('content')

    <div class="loader d-none">
        <div class="sub-loader position-relative ">
            <div class="lds-hourglass"></div>
            <p>Loading...</p>
        </div>
    </div>

    <div class="row text-center mt-3 mb-5">
        <div class="col"></div>

        <div class="col-4 text-left">

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            <form action="/store" method="POST" class="" id="store_credentials">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-12">

                        <div>
                            <x-adminlte-input label="Store Name" name="storename" value='' type="text" placeholder="Store Name" />
                        </div>

                        <div>
                            <x-adminlte-input label="Seller/Merchant ID" name="merchantid" value = '' type="text" placeholder="Merchant ID"/>
                        </div>

                        <div>
                            <x-adminlte-input label="Auth Code" name="authcode" value="" type="text" placeholder="Auth Code"/>
                        </div>

                        <div>

                        <x-adminlte-select name="marketplaceid" label="Marketplace ID">
                                @foreach ($mws_regions as $mws_region)
                                    @php
                                        $mwsRegion = $awsCredentials->mws_region['marketplace_id'] ?? '';
                                    @endphp

                                    @if ($mws_region->marketplace_id == $mwsRegion)
                                        <option value="{{ $mws_region->id }}" selected>{{ $mws_region->marketplace_id  }}, {{ $mws_region->currency->name ?? '' }}, {{ $mws_region->region_code }}</option>
                                    @else
                                        <option value="{{ $mws_region->id }}" >{{ $mws_region->marketplace_id  }}, {{ $mws_region->currency->name ?? '' }}, {{ $mws_region->region_code }}</option>
                                    @endif

                                @endforeach
                            </x-adminlte-select>

                        </div>


                        <div class="text-center"></div>

                        <div class="text-center d-flex justify-content-center mt-1">

                            <div class="ml-2 pt-4">
                                <x-adminlte-button label="Amazon credentials check & save" theme="primary" icon="fas fa-save" type="submit" />
                            </div>

                        </div>

                    </div>

                </div>

            </form>

        </div>

        <div class="col"></div>

    </div>

@stop




