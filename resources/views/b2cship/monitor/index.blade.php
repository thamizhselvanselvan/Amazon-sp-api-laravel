@extends('adminlte::page')
@section('title', 'Status Details')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark"> Bombino Booking API Failure Error</h1>
    <div class="col text-right">
    </div>
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
            <th>Error ID</th>
            <th>Company Code</th>
            <th>Error From</th>
            <th>Error Details</th>
            <th>Error Date</th>
            <th>Created By</th>
            <th>IP Address</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $value)
            <tr>
                @foreach ($value as $key1 => $details )
                    <td>
                        {{$details}}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

@stop