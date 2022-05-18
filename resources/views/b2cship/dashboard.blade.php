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
<h3 class="m-0 text-dark">Tacking Update Details</h3>
<div class="container-fluid">

    <div class="row">
        <div class="col-3 ">
            
            <div class="info-box bg-info text-center">
                <div class="info-box-content">
                    <h4>{{$bombino_date}}</h4>
                    <h5>Bombino Last Update </h5>
                </div>
            </div>
        </div>
        <div class="col-3 ">
           
            <div class="info-box bg-info text-center">
                <div class="info-box-content">
                    <h4>{{$bluedart_date}}</h4>
                    <h5>Bluedart Last Update </h5>
                </div>
            </div>
        </div>
        <div class="col-3 ">
           
            <div class="info-box bg-info text-center">
                <div class="info-box-content">
                    <h4>{{$delivery_date}}</h4>
                    <h5>Delivery Last Update </h5>
                </div>
            </div>
        </div>
        <div class="col-3 ">
            <!-- <h4 style="font-weight: bold; text-align: center;">Last 30 Days </h4> -->
            <div class="info-box bg-info  text-center">
                <div class="info-box-content">
                    <h4>{{$dl_delhi_date}}</h4>
                    <h5>DL Delhi last Update </h5>
                </div>
            </div>
        </div>
    </div>
</div>

@stop