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
<div class="container-fluid">
    <div class="row">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" id="TrackingAPI" data-target="#Tracking"  href="">Tracking API</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" id="BombinoStatus" data-target="#Bombinotab" href="">Bombino Status</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" id="DeliveryStatus" data-target="#Deliverytab" href="">Delivery Status</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" id="Misc" data-target="" href="">Misc Status</a>
            </li>
        </ul>
    </div>

    <div class="mt-4 tab-content">
        <div class="tab-pane active" id="Tracking">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" id="TrackingActive" href="">Active</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" data-toggle="tab" id="TrackingInactive"  href="" >Inactive</a>
                </li>
            </ul>
        </div>
        
        <div class="tab-pane " id="Bombinotab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" id="BombinoActive" href="">Active</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" data-toggle="tab" id="BombinoInactive"  href="" >Inactive</a>
                </li>
            </ul>
        </div>
        <div class="tab-pane " id="Deliverytab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" id="DeliveryActive" href="">Active</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" data-toggle="tab" id="DeliveryInactive"  href="" >Inactive</a>
                </li>
            </ul>
        </div>
    </div>

    <div class=" tab-content">
        <div class="tab-pane active" id="TrackingAPIActiveShow">
            <h3 class="mt-4 text-dark">Status Details</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Last Record</th>
                        <th>Days</th>
                        <th>Time</th>
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
        </div>
        <div class="tab-pane" id="TrackingAPInActiveShow">
            <h3 class="mt-4 text-dark">Status Details</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Last Record</th>
                        <th>Days</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tracking_inactive_status as $value)
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
        </div>
        <div class="tab-pane active" id="bombinoStatus">
            <h3 class="mt-4 text-dark">Bombino Details</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Last Record</th>
                        <th>Days</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bombino_status as $value)
                    <tr>
                        <td>{{$value['Status']}}</td>
                        <td>{{$value['day']}}</td>
                        <td>{{$value['time']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
        </div>
        <div class="tab-pane" id="bombinoInactiveShow">
            <h3 class="mt-4 text-dark">Bombino Inactive Details</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Last Record</th>
                        <th>Days</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bombino_inactive as $value)
                    <tr>
                        <td>{{$value['inactive']}}</td>
                        <td>{{$value['day']}}</td>
                        <td>{{$value['time']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="tab-pane active" id="deliveryStatus">
            <h3 class="mt-4 text-dark">Delivery Details</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Days</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($delivery_status as $value)
                    <tr>
                        <td>{{$value['FPCode']}} </td>
                        <td>{{$value['StatusDetails']}}</td>
                        <td>{{$value['day']}}</td>
                        <td>{{$value['time']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
        </div>
        <div class="tab-pane" id="deliveryInactiveShow">
            <h3 class="mt-4 text-dark">Delivery Inactive Details</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Days</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($delivery_inactive_status as $value)
                    <tr>
                        <td>{{$value['StatusDetails']}}</td>
                        <td>{{$value['FPCode']}} </td>
                        <td>{{$value['day']}}</td>
                        <td>{{$value['time']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="MiscStatus">
        <h3 class="mt-4 text-dark">Misc Details</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Awb No.</th>
                    <th>Days</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kyc_booking_status as $value)
                <tr>
                    <td>{{$value['Status']}} </td>
                    <td>{{$value['AwbNo']}}</td>
                    <td>{{$value['day']}}</td>
                    <td>{{$value['time']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function(){

    $("#bombinoStatus").hide();
    $('#deliveryStatus').hide();
    $('#MiscStatus').hide();

    $('#TrackingAPI').on('click', function(){
        $('#TrackingAPIActiveShow').show();
        $("#bombinoStatus").hide();
        $('#deliveryStatus').hide();
        $('#MiscStatus').hide();
        $('#bombinoInactiveShow').hide();
        $('#deliveryInactiveShow').hide();
        $('#TrackingAPInActiveShow').hide(); 
        $('.tab-content').show();
        $('#TrackingActive').addClass('active');
        $('#TrackingInactive').removeClass('active');
    });

    $('#TrackingActive').on('click', function(){
        $('#TrackingAPIActiveShow').show();
        $("#bombinoStatus").hide();
        $('#deliveryStatus').hide();
        $('#MiscStatus').hide();
        $('#bombinoInactiveShow').hide();
        $('#deliveryInactiveShow').hide();
        $('#TrackingAPInActiveShow').hide();  
        $('.tab-content').show();
        $('#TrackingActive').addClass('active');
        $('#TrackingInactive').removeClass('active');
    });

    $('#TrackingInactive').on('click', function(){
        $('#TrackingAPInActiveShow').show();
        $('#TrackingAPIActiveShow').hide();
        $("#bombinoStatus").hide();
        $('#deliveryStatus').hide();
        $('#MiscStatus').hide();
        $('#bombinoInactiveShow').hide();
        $('#deliveryInactiveShow').hide(); 
        $('.tab-content').show();
    });

    $('#BombinoStatus').on('click', function(){
        $("#bombinoStatus").show();
        $('#TrackingAPIActiveShow').hide();
        $('#deliveryStatus').hide();
        $('#MiscStatus').hide();
        $("#bombinoInactiveShow").hide();
        $('#deliveryStatus').hide();
        $('#deliveryInactiveShow').hide();
        $('#TrackingAPIActiveShow').hide();
        $('#TrackingAPInActiveShow').hide(); 
        $('#BombinoActive').addClass('active');
        $('#BombinoInactive').removeClass('active');
        $('.tab-content').show();
    });

    $('#BombinoActive').on('click', function(){
        $("#bombinoStatus").show();
        $('#TrackingAPIActiveShow').hide();
        $('#deliveryStatus').hide();
        $('#MiscStatus').hide();
        $("#bombinoInactiveShow").hide();
        $('#TrackingAPIActiveShow').hide();
        $('#TrackingAPInActiveShow').hide(); 
    });

    $('#BombinoInactive').on('click',function(){
        $("#bombinoInactiveShow").show();
        $('#bombinoStatus').hide();
        $('#TrackingAPIActiveShow').hide();
        $('#deliveryStatus').hide();
        $('#MiscStatus').hide();
        $('#TrackingAPIActiveShow').hide();
        $('#TrackingAPInActiveShow').hide(); 
    });

    $('#DeliveryStatus').on('click', function(){

        $('#deliveryStatus').show();
        $('#TrackingAPIActiveShow').hide();
        $("#bombinoStatus").hide();
        $('#MiscStatus').hide();
        $('#bombinoStatus').hide();
        $("#bombinoInactiveShow").hide();
        $('#deliveryInactiveShow').hide();
        $('#TrackingAPIActiveShow').hide();
        $('#TrackingAPInActiveShow').hide(); 
        $('#DeliveryActive').addClass('active');
        $('#DeliveryInactive').removeClass('active');
        $('.tab-content').show();
    });

    $('#DeliveryActive').on('click', function(){

        $('#deliveryStatus').show();
        $('#TrackingAPIActiveShow').hide();
        $("#bombinoStatus").hide();
        $('#MiscStatus').hide();
        $('#bombinoStatus').hide();
        $("#bombinoInactiveShow").hide();
        $('#deliveryInactiveShow').hide();
        $('#TrackingAPIActiveShow').hide();
        $('#TrackingAPInActiveShow').hide(); 
    });

    $('#DeliveryInactive').on('click', function(){

        $('#deliveryInactiveShow').show();
        $('#TrackingAPIActiveShow').hide();
        $("#bombinoStatus").hide();
        $('#MiscStatus').hide();
        $('#bombinoStatus').hide();
        $("#bombinoInactiveShow").hide();
        $('#deliveryStatus').hide();
        $('#TrackingAPIActiveShow').hide();
        $('#TrackingAPInActiveShow').hide(); 
    });

    $('#Misc').on('click', function(){
        $('#MiscStatus').show();
        $('#TrackingAPIActiveShow').hide();
        $("#bombinoStatus").hide();
        $('#deliveryStatus').hide();
        $('.tab-content').hide();
        $('#TrackingAPIActiveShow').hide();
        $('#TrackingAPInActiveShow').hide(); 
    });
    
});

</script>
@stop

