@extends('adminlte::page')

@section('title', 'Upload Excel')

@section('content_header')

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center ">Label CSV Upload</h1>
    </div>
</div>

<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>Excel is no longer accepted please download CSV format and upload the data.</strong>
</div>

<div class="row">
    <div class="col">
        <a href="{{ route('label.manage') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
        <a href="{{ route('download.label.template') }}">
            <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download" class="btn-sm ml-2" />
        </a>
    </div>
</div>

@stop

@section('css')
<style>
    div.form-group {
        margin-bottom: 0rem;
    }
</style>
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


        <form class="row" id="multi-file-upload" method="POST" action="{{route('upload.label.excel.file')}}" accept-charset="utf-8" enctype="multipart/form-data">
            @csrf
            <div class="col-3"></div>

            <div class="col-6">
                <x-adminlte-input label="Select CSV File" name="label_csv_file" id="files" type="file" />
                <label></label>
            </div>
            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Upload" theme="primary" class="add_ btn-sm" id="upload_excel" icon="fas fa-plus" type="submit" />
                </div>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop

@section('js')
<script src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript">

</script>

@stop