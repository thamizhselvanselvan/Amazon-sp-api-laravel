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
<div class="container-fluid">

    <div class="row">
        <div class="col-3 ">
            
            <div class="info-box bg-info text-center">
                <div class="info-box-content">
                    <h5>Bombino Update </h5>
                    <div>{{$bombino_date}}</div>
                </div>
            </div>
        </div>
        <div class="col-3 ">
           
            <div class="info-box bg-info text-center">
                <div class="info-box-content">
                    <h5>Bluedart Update </h5>
                    <div>{{$bluedart_date}}</div>
                </div>
            </div>
        </div>
        <div class="col-3 ">
           
            <div class="info-box bg-info text-center">
                <div class="info-box-content">
                    <h5>Delivery Update </h5>
                    <div>{{$delivery_date}}</div>
                </div>
            </div>
        </div>
        <div class="col-3 ">
            <!-- <h4 style="font-weight: bold; text-align: center;">Last 30 Days </h4> -->
            <div class="info-box bg-info  text-center">
                <div class="info-box-content">
                    <h5>DL Delhi Update </h5>
                    <div>{{$dl_delhi_date}}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop