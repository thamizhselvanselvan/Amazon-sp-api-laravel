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
            <a class="nav-link" data-toggle="tab" id="MiscStatus" href="">Misc Status</a>
            </li>
        </ul>
    </div>

    <div class="mt-4 tab-content">
        <div class="tab-pane active" id="Tracking">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" id="TrackingAPIActive" href="">Active</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" data-toggle="tab" id="TrackingAPInActive"  href="" >Inactive</a>
                </li>
            </ul>
        </div>
        <div class="tab-pane " id="Bombinotab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" id="BombinoStatusActive" href="">Active</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" id="BombinoStatusInactive"  href="" >Inactive</a>
                </li>
            </ul>
        </div>
        <div class="tab-pane " id="Deliverytab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" id="DeliveryStatusActive" href="">Active</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" id="DeliveryStatusInactive"  href="" >Inactive</a>
                </li>
            </ul>
        </div>
        <div class="tab-pane " id="MiscActiveShow">

        </div>
            
    </div>

    <div class=" tab-content">
        <div class="tab-pane active" id="TrackingAPIActiveShow">
            <h3 class="mt-4 text-dark">Status Details</h3>
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
        </div>
        <div class="tab-pane" id="TrackingAPInActiveShow">

        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane " id="BombinoStatusActiveShow">

        </div>
        <div class="tab-pane " id="BombinoStatusInactiveShow">

        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane" id="DeliveryStatusActiveShow">

        </div>
        <div class="tab-pane" id="DeliveryStatusInactiveShow">

        </div>
    </div>
  
</div>
@stop

@section('js')
<script>
$(document).ready(function(){

    $('#TrackingAPI').click(function(){

        $('#TrackingAPIActive').addClass('active');
        $('#TrackingAPInActive').removeClass('active');
        $('#TrackingAPIActiveShow').empty();
        $('.tab-pane').removeClass('active');
        $.ajax({
            url :'Trackingtab',
            method : 'GET',
            success:function(response){
                let track ="<h3 class=m-4 text-dark>Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Last Record</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(response, function(i, response){
                track +="<tr><td>"+response.FPCode+"</td><td>"+response.StatusDetials+"</td><td>"+response.day+"</td><td>"+response.time+"</td></tr>";
                });
                $('#TrackingAPIActiveShow').html(track).addClass('active');
                $('#BombinoStatusActiveShow').removeClass('active');
                $('#DeliveryStatusActiveShow').removeClass('active');
                $('#MiscActiveShow').removeClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });
    $('#TrackingAPIActive').click(function(){
        
        $.ajax({
            url :'Trackingtab',
            method : 'GET',
            success:function(response){
                ;
                let track ="<h3 class=m-4 text-dark>Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Last Record</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(response, function(i, response){
                track +="<tr><td>"+response.FPCode+"</td><td>"+response.StatusDetials+"</td><td>"+response.day+"</td><td>"+response.time+"</td></tr>";
                });
                $('#TrackingAPIActiveShow').html(track).addClass('active');
                $('#TrackingAPInActiveShow').removeClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
        
    });

    $('#TrackingAPInActive').click(function(){
        
        $.ajax({
            url :'TrackingAPInActive',
            method : 'GET',
            success:function(response){
                ;
                let track ="<h3 class=m-4 text-dark>Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Last Record</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(response, function(i, response){
                track +="<tr><td>"+response.FPCode+"</td><td>"+response.StatusDetials+"</td><td>"+response.day+"</td><td>"+response.time+"</td></tr>";
                });
                $('#TrackingAPInActiveShow').html(track).addClass('active');
                $('#TrackingAPIActiveShow').removeClass('active');
            },
            error:function(){
                alert('ERROR');
            }
        });
      
    });

    $('#BombinoStatus').click(function(){

        $('#BombinoStatusActive').addClass('active');
        $('#BombinoStatusInactive').removeClass('active');
        $('#BombinoStatusActiveShow').empty();
        $('.tab-pane').removeClass('active');
        
        $.ajax({
            url :'Bombinotab',
            method : 'GET',
            success:function(result){
                
                let bombino ="<h3 class=m-4 text-dark>Bombino Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Last Record</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                bombino +="<tr><td>"+result.Status+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#BombinoStatusActiveShow').html(bombino).addClass('active');
                $('#TrackingAPIActiveShow').removeClass('active');
                $('#DeliveryStatusActiveShow').removeClass('active');
                $('#MiscActiveShow').removeClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });

    
    $('#BombinoStatusActive').click(function(){
          
        $.ajax({
            url :'Bombinotab',
            method : 'GET',
            success:function(result){
                let bombino ="<h3 class=m-4 text-dark>Bombino Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Last Record</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                bombino +="<tr><td>"+result.Status+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#BombinoStatusActiveShow').html(bombino).addClass('active');
                $('#BombinoStatusInactiveShow').removeClass('active');
                   
            },
            error:function(){
                alert('ERROR');
            }
        });
    });

    $('#BombinoStatusInactive').click(function(){
        
        $.ajax({
            url :'BombinoStatusInactive',
            method : 'GET',
            success:function(result){
                
                let bombino ="<h3 class=m-4 text-dark>Bombino Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                bombino +="<tr><td>"+result.inactive+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#BombinoStatusInactiveShow').html(bombino).addClass('active');
                $('#BombinoStatusActiveShow').removeClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });   
    });

    $('#DeliveryStatus').click(function(){
        
        $('#DeliveryStatusActive').addClass('active');
        $('#DeliveryStatusInactive').removeClass('active');
        $('#DeliveryStatusActiveShow').empty();
        $('.tab-pane').removeClass('active');
        $('#MiscActiveShow').removeClass('active');
        
        $.ajax({
            url :'Deliverytab',
            method : 'GET',
            success:function(result){
                // alert('success');
                let deliver ="<h3 class=m-4 text-dark>Bluedart, DL Delhi And DELHIVERY Last Packet Delivered Status</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Status</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                deliver +="<tr><td>"+result.FPCode+"</td><td>"+result.StatusDetails+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#DeliveryStatusActiveShow').html(deliver).addClass('active');
                $('#DeliveryStatusInactiveShow').removeClass('active');
                $('#TrackingAPIActiveShow').removeClass('active');
                $('#BombinoStatusActiveShow').removeClass('active');
                $('#MiscActiveShow').removeClass('active');
            },
            error:function(){
                alert('ERROR');
            }
        });

    });

    $('#DeliveryStatusActive').click(function(){
        
        $.ajax({
            url :'Deliverytab',
            method : 'GET',
            success:function(result){
                
                let deliver ="<h3 class=m-4 text-dark>Bluedart, DL Delhi And DELHIVERY Last Packet Delivered Status</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Status</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                deliver +="<tr><td>"+result.FPCode+"</td><td>"+result.StatusDetails+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#DeliveryStatusActiveShow').html(deliver).addClass('active');
                $('#DeliveryStatusInactiveShow').removeClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });

    $('#DeliveryStatusInactive').click(function(){
              
        $.ajax({
            url :'DeliveryStatusInactive',
            method : 'GET',
            success:function(result){
                
                let deliver ="<h3 class=m-4 text-dark>Bluedart, DL Delhi And DELHIVERY Last Packet Delivered Status</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Status</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                deliver +="<tr><td>"+result.FPCode+"</td><td>"+result.StatusDetails+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#DeliveryStatusInactiveShow').html(deliver).addClass('active');
                $('#DeliveryStatusActiveShow').removeClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });

    $('#MiscStatus').click(function(){
        
        
        $('#MiscActiveShow').empty();
        $('.tab-pane').removeClass('active');
       
        $.ajax({
            url :'Misctab',
            method : 'GET',
            success:function(result){
                
                let micro ="<h3 class=m-4 text-dark>B2CShip Booking And Kyc Status</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Awb No.</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                micro +="<tr><td>"+result.Status+"</td><td>"+result.AwbNo+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#MiscActiveShow').html(micro).addClass('active');
                $('#TrackingAPIActiveShow').removeClass('active');
                $('#DeliveryStatusActiveShow').removeClass('active');
                $('#BombinoStatusActiveShow').removeClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });
});

</script>
@stop

