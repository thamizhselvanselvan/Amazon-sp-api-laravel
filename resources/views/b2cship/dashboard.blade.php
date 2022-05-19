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
<h3 class="m-0 text-dark">Status Details</h3>
<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr>
            <td>Description</td>
            <td>Last Record</td>
            <td>Time</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Bombino</td>
            <td>{{$bombino_date['lastRecord']}}</td>
            <td>{{$bombino_date['Diff']}}</td>
        </tr>
        <tr>
            <td>Bluedart</td>
            <td>{{$bluedart_date['lastRecord']}}</td>
            <td>{{$bluedart_date['Diff']}}</td>
        </tr>
        <tr>
            <td>DL Delhi</td>
            <td>{{$dl_delhi_date['lastRecord']}}</td>
            <td>{{$dl_delhi_date['Diff']}}</td>
        </tr>
        <tr>
            <td>Delivery</td>
            <td>{{$delivery_date['lastRecord']}}</td>
            <td>{{$delivery_date['Diff']}}</td>
        </tr>
    </tbody>
</table>
<br>
<h3 class="m-0 text-dark">Bombino Status Details</h3>

<table class="table table-bordered yajra-datatable table-striped">

<thead>
    <tr>
        <td>Description </td>
        <td>Status</td>
        <td>Updated Date</td>
    </tr>
</thead>
<tbody>
    @foreach ($bombino_status as $value )
        <tr>
            <td>Bombino</td>
            <td>{{$value['Status']}}</td>
            <td>{{$value['updatedDate']}}</td>
        </tr>
    @endforeach
    <tr></tr>
</tbody>
</table>

<br>
<h3 class="m-0 text-dark">Bluedart, DL Delhi And Delivery Last Packet Delivered Status</h3>

<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr>
            <td>Description </td>
            <td>Status</td>
            <td>Updated Date</td>
        </tr>
    </thead>

    <tbody>
        @foreach ($delivery_status as $value)
            
            <tr>
                <td>{{$value['FPCode']}}</td>
                <td>{{$value['StatusDetails']}}</td>
                <td>{{$value['updatedDate']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@stop