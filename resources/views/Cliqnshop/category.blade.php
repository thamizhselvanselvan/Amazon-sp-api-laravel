@extends('adminlte::page')
@section('title', 'Categories')
@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
<h1 class="m-0 text-dark">Categories</h1>

@stop

@section('content')
<form action="{{ route('cliqnshop.category-export') }}" method="POST" accept-charset="utf-8" enctype="multipart/form-data">
    @csrf
    <div class="form-group w-25">
<x-adminlte-input class="form-control" label="Choose CSV File" name="category_csv" id="files" type="file" />
<x-adminlte-button label="Upload" theme="primary" class="add_ btn-sm" icon="fas fa-upload" type="submit" id="csv_upload" />
<a href="{{ route('cliqnshop.catalog.csv.templete') }}"
<x-adminlte-button label="Template Download" theme="primary" class="add_ btn-sm" icon="fas fa-download" id="csv_template_download" />
    </a>
</div>
</form>
@stop