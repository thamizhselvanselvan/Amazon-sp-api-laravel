@extends('adminlte::page')
@section('title', 'Status Details')

@section('content_header')
<div class="row">

    <h1 class="m-0 text-dark"> B2CShip Micro Status Report </h1>
    <h2 class="mb-4 text-right col">
        <a href="update-report">
            <x-adminlte-button label="Update Report" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
    </h2>
</div>
@stop

@section('css')
<style>
    .table td {
        padding: 0.1rem;
    }
</style>
@stop

@section('content')


<table class="table table-bordered yajra-datatable table-striped" style="font-size:13px;">

    <thead>
        <tr>
            <td><strong>Today</strong></td>
            <td><strong>Yesterday</strong></td>
            <td><strong>Last 7 days</strong></td>
            <td><strong>Last 30 days</strong></td>
        </tr>
    </thead>
    <tbody>
        @foreach ($micro_status_final_array as $key => $values )
        <tr>
            @foreach ($values as $value)
            <td>
                {{$key}}
                <strong style="padding-left: 10px;">{{$value}}</strong>
            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

@stop

@section('js')
<script type="text/javascript">

</script>

@stop