@extends('adminlte::page')
@section('title', 'Invoice')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Invoice Management</h1>
    <h2 class="mb-4 text-right col">
        <a href="excel/template">
            <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download" class="btn-sm" />
        </a>
        <a href="upload">
            <x-adminlte-button label="Upload Excel Sheet" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <a href="download-all">
            <x-adminlte-button label="Download Label" id='download_pdf' theme="primary" icon="fas fa-check-circle" class="btn-sm" />
        </a>
    </h2>
</div>
@stop

@section('content')


@stop

@section('js')
<script>


</script>
@stop