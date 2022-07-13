@extends('adminlte::page')

@section('title', 'SMSA Tracking')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">SMSA Tracking</h1>
    <h2 class="mb-4 text-right col">
        <a href="{{Route('shipntrack.smsa.upload')}}">
            <x-adminlte-button label="Add New SMSA AWB No." theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
    </h2>
</div>
@stop

@section('content')
<div class="pl-2">
    <table class="table table-bordered yajra-datatable table-striped text-center table-sm">
        <thead>
            <tr class="text-bold bg-info">
               
            </tr>
        </thead>
        <tbody>
        </tbody>

    </table>
</div>
@stop

@section('js')
<script>

</script>
@stop
