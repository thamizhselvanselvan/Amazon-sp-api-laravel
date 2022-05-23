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
            <td>Days</td>
            <td>Time</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($status_detials_array as $value)
        <tr>
            <td>{{$value['FPCode']}}</td>
            <td>{{$value['StatusDetials']}} </td>
            <td>{{$value['day']}}</td>
            <td>{{$value['time']}}</td>

        </tr>

        @endforeach
    </tbody>
</table>
<br>
<h3 class="m-0 text-dark">Bombino Status Details</h3>

<table class="table table-bordered yajra-datatable table-striped">

    <thead>
        <tr>
            <td>Description </td>
            <td>Status</td>
            <td>Days</td>
            <td>Time</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($bombino_status as $value )
        <tr>
            <td>Bombino</td>
            <td>{{$value['Status']}}</td>
            <td>{{$value['day']}}</td>
            <td>{{$value['time']}}</td>

        </tr>
        @endforeach
        <tr></tr>
    </tbody>
</table>

<br>
<h3 class="m-0 text-dark">Bluedart, DL Delhi And DELHIVERY Last Packet Delivered Status</h3>

<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr>
            <td>Description </td>
            <td>Status</td>
            <td>Days</td>
            <td>Time</td>
        </tr>
    </thead>

    <tbody>
        @foreach ($delivery_status as $value)

        <tr>
            <td>{{$value['FPCode']}}</td>
            <td>{{$value['StatusDetails']}}</td>
            <td>{{$value['day']}}</td>
            <td>{{$value['time']}}</td>

        </tr>
        @endforeach
    </tbody>
</table>
<br>
<h3 class="m-0 text-dark">B2CShip Booking And Kyc Status</h3>
<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr>
            <td>Description </td>
            <td>AwbNo</td>
            <td>Days</td>
            <td>Time</td>
        </tr>
    </thead>

    <tbody>
        @foreach ($kyc_booking_status as $value)

        <tr>
            <td>{{$value['Status']}}</td>
            <td>{{$value['AwbNo']}}</td>
            <td>{{$value['day']}}</td>
            <td>{{$value['time']}}</td>

        </tr>
        @endforeach
    </tbody>
</table>
@stop