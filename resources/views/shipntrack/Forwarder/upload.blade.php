@extends('adminlte::page')

@section('title', 'Upload')

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{route('shipntrack.forwarder')}}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center ">Upload Forwarder Tracking No.</h1>
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

        @if(session()->has('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session()->get('success') }}
        </x-adminlte-alert>
        @endif

        @if(session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session()->get('error') }}
        </x-adminlte-alert>
        @endif

        <form class="row" action="{{ route('shipntrack.forwarder.save')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="col-3"></div>

            <div class="col-6">
                <x-adminlte-input label="" name="forwarder_awb" id="forwarder_csv" type="file" />
            </div>

            <div class="col-3"></div>

            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Submit" theme="primary" class="add_forwarder_awb btn-sm" icon="fas fa-plus" type="submit" />
                </div>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop