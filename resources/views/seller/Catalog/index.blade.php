@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark">Catalog Details</h1>
    <div class="col text-right">
        <a href="/seller/catalog/export">
            <x-adminlte-button label="Catalog CSV Export" theme="primary" icon="fas fa-file-import" id='catalog_details' />
        </a>
        <a href='/seller/catalog/download'>
            <x-adminlte-button label="Download Catalog CSV" theme="primary" icon="fas fa-file-download" />
        </a>
    </div>
</div>
@stop

@section('content')
@csrf

<div class="row">

</div>

@stop

@section('js')
<script type="text/javascript">

</script>
@stop