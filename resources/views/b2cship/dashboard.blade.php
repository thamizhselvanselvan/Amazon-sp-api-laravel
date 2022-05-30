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
            <a class="nav-link active" data-toggle="tab" id="TrackingAPI" data-target="#Tracking"  href="Trackingtab">Tracking API</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" data-toggle="tab" id="BombinoStatus" data-target="#Bombinotab" href="Bombinotab">Bombino Status</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" data-toggle="tab" id="DeliveryStatus" data-target="#Deliverytab" href="Deliverytab">Delivery Status</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" data-toggle="tab" id="MiscStatus" data-target="#Misctab" href="Misctab">Misc Status</a>
            </li>
        </ul>
    </div>

    <div class="mt-4 tab-content">
        <div class="tab-pane active" id="Tracking">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" id="TrackingAPIActive" href="Trackingtab">Active</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" data-toggle="tab" id="TrackingAPInActive"  href="TrackingAPInActive" >Inactive</a>
                </li>
            </ul>
        </div>
        <div class="tab-pane " id="Bombinotab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" id="BombinoStatusActive" href="Bombinotab">Active</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" id="BombinoStatusInactive"  href="BombinoStatusInactive" >Inactive</a>
                </li>
            </ul>
        </div>
        <div class="tab-pane " id="Deliverytab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" id="DeliveryStatusActive" href="Deliverytab">Active</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" id="DeliveryStatusInactive"  href="" >Inactive</a>
                </li>
            </ul>
        </div>
        <div class="tab-pane " id="Misctab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" id="MiscStatusActive" href="Misctab">Active</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" id="MiscStatusInactive"  href="" >Inactive</a>
                </li>
            </ul>
        </div>
    </div>

    

    <div class="mt-4 tab-content">
        <div class="tab-pane active" id="active1">
            <h3 class="m-4 text-dark">Status Details</h3>
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
        <div class="tab-pane " id="inactive1">

        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane " id="active2">

        </div>
        <div class="tab-pane " id="inactive2">

        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane" id="active3">

        </div>
        <div class="tab-pane" id="inactive3">

        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane" id="active4">

        </div>
        <div class="tab-pane" id="inactive4">

        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function(){
    $('#TrackingAPI').click(function(){
        // var url=$(this).attr('href');
        // alert(url);
        $('#TrackingAPIActive').addClass('active');
        $('#TrackingAPInActive').removeClass('active');
        $('#active1').empty();
        $('.tab-pane').removeClass('active');
        $.ajax({
            url :'Trackingtab',
            method : 'GET',
            success:function(response){
                let track ="<h3 class=m-4 text-dark>Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Last Record</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(response, function(i, response){
                track +="<tr><td>"+response.StatusDetials+"</td><td>"+response.FPCode+"</td><td>"+response.day+"</td><td>"+response.time+"</td></tr>";
                });
                $('#active1').append(track).addClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });
    $('#TrackingAPIActive').click(function(){
        // var url=$(this).attr('href');
        // alert(url);
        $('#inactive1').empty();
        $.ajax({
            url :'Trackingtab',
            method : 'GET',
            success:function(response){
                ;
                let track ="<h3 class=m-4 text-dark>Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Last Record</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(response, function(i, response){
                track +="<tr><td>"+response.StatusDetials+"</td><td>"+response.FPCode+"</td><td>"+response.day+"</td><td>"+response.time+"</td></tr>";
                });
                $('#active1').append(track).addClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });

    $('#TrackingAPInActive').click(function(){
        $('#active1').empty();
        $('#inactive1').html("<h2>No record here......</h2>").addClass('active');
    });
});

$(document).ready(function(){

    $('#BombinoStatus').click(function(){

        $('#BombinoStatusActive').addClass('active');
        $('#BombinoStatusInactive').removeClass('active');
        $('#active2').empty();
        $('.tab-pane').removeClass('active');
        // var url=$(this).attr('href');
            // alert(url);
        $.ajax({
            url :'Bombinotab',
            method : 'GET',
            success:function(result){
                
                let bombino ="<h3 class=m-4 text-dark>Bombino Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Last Record</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                bombino +="<tr><td>"+result.Status+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#active2').append(bombino).addClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });
});

$(document).ready(function(){
    
    $('#BombinoStatusActive').click(function(){
        $('#active2').empty();
        $('#inactive2').removeClass('active');
        // var url=$(this).attr('href');
           
        $.ajax({
            url :'Bombinotab',
            method : 'GET',
            success:function(result){
                // alert('success');
                let bombino ="<h3 class=m-4 text-dark>Bombino Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Last Record</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                bombino +="<tr><td>"+result.Status+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#active2').append(bombino).addClass('active');
                $(this).addClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });
});

$(document).ready(function(){
    $('#BombinoStatusInactive').click(function(){
        var url=$(this).attr('href');
           
        $('#inactive2').empty();
        $('#active2').removeClass('active');

        $.ajax({
            url :'BombinoStatusInactive',
            method : 'GET',
            success:function(result){
                
                let bombino ="<h3 class=m-4 text-dark>Bombino Status Details</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                bombino +="<tr><td>"+result.inactive+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#inactive2').append(bombino).addClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });   
    });
});

$(document).ready(function(){
    $('#DeliveryStatus').click(function(){
        // var url=$(this).attr('href');
        
        $('#DeliveryStatusActive').addClass('active');
        $('#DeliveryStatusInactive').removeClass('active');
        $('#active3').empty();
        $('.tab-pane').removeClass('active');

        $.ajax({
            url :'Deliverytab',
            method : 'GET',
            success:function(result){
                // alert('success');
                let deliver ="<h3 class=m-4 text-dark>Bluedart, DL Delhi And DELHIVERY Last Packet Delivered Status</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Status</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                deliver +="<tr><td>"+result.FPCode+"</td><td>"+result.StatusDetails+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#active3').append(deliver).addClass('active');
            },
            error:function(){
                alert('ERROR');
            }
        });

    });

    $('#DeliveryStatusActive').click(function(){
        // var url=$(this).attr('href');
        // alert(url);
        $('#inactive3').empty();
        $.ajax({
            url :'Deliverytab',
            method : 'GET',
            success:function(result){
                
                let deliver ="<h3 class=m-4 text-dark>Bluedart, DL Delhi And DELHIVERY Last Packet Delivered Status</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Status</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                deliver +="<tr><td>"+result.FPCode+"</td><td>"+result.StatusDetials+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#active3').append(deliver).addClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });


    });

    $('#DeliveryStatusInactive').click(function(){
            // alert('working');
            $('#active3').empty();
            $('#inactive3').html("<h2> No records here....</h2>").addClass('active');
    })
});

$(document).ready(function(){
    $('#MiscStatus').click(function(){
        // var url=$(this).attr('href');
        $('#MiscStatusActive').addClass('active');
        $('#MiscStatusInactive').removeClass('active');
        $('#active4').empty();
        $('.tab-pane').removeClass('active');
        // alert(url);
        $.ajax({
            url :'Misctab',
            method : 'GET',
            success:function(result){
                
                let micro ="<h3 class=m-4 text-dark>B2CShip Booking And Kyc Status</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Awb No.</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                micro +="<tr><td>"+result.Status+"</td><td>"+result.AwbNo+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#active4').append(micro).addClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });

    $('#MiscStatusActive').click(function(){
        var url=$(this).attr('href');
        $('#inactive4').empty();
        
        $.ajax({
            url :'Misctab',
            method : 'GET',
            success:function(result){
                let micro ="<h3 class=m-4 text-dark>B2CShip Booking And Kyc Status</h3><table class=table table-bordered yajra-datatable table-striped> <thead> <tr><td>Description</td><td>Awb No.</td><td>Days</td><td>Time</td></tr></thead><tbody>";
                $.each(result, function(i, result){
                micro +="<tr><td>"+result.Status+"</td><td>"+result.AwbNo+"</td><td>"+result.day+"</td><td>"+result.time+"</td></tr>";
                });
                $('#active4').append(micro).addClass('active');
                
            },
            error:function(){
                alert('ERROR');
            }
        });
    });

    $('#MiscStatusInactive').click(function(){
        $('#active4').empty();
        $('#inactive4').html("<h2> No records available here....</h2>").addClass('active');
    })
});

</script>
@stop

