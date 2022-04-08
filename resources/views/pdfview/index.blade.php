@extends('adminlte::page')

@section('title', 'PDF Master')

@section('content_header')

 <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center ">Upload PDF</h1>
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
     
        <form class="row" action="upload" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="col-3"></div>

            <div class="col-6" >
                <x-adminlte-input label="PDF Lists" name="pdf" id="pdf" type="file" />
            </div>

            <div class="col-3"></div>
         
            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Upload PDF" theme="primary" class="add_" icon="fas fa-plus" type="submit" />
                </div>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop