@extends('adminlte::page')
@section('title', 'Tracking Details')

@section('content_header')
<div class="row">

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
<h3 class="m-0 text-dark">Packet Update Details</h3>
<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr>
            <td>Status</td>
            <td>Updated Details</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Bombino</td>
            <td>{{$bombino_date}}</td>
        </tr>
        <tr>
            <td>Bluedart</td>
            <td>{{$bluedart_date}}</td>
        </tr>
        <tr>
            <td>DL Delhi</td>
            <td>{{$dl_delhi_date}}</td>
        </tr>
        <tr>
            <td>Delivery</td>
            <td>{{$delivery_date}}</td>
        </tr>
    </tbody>
</table>

@stop