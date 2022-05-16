@extends('adminlte::page')

@section('title', 'BOE Export')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

<!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> -->

@stop


@section('content_header')

<h1 class = "m-0 text-dark"> BOE Report </h1>
@stop

@section('content')

<div class = "container-fluid">

    <div class = "row">

       @foreach ($total_companys as $key => $company)

           <div class = "col">

                <div class = "info-box bg-info text-center">

                    <div class = "info-box-content">

                        <h3 style = "font-weight:bold; text-align:center;"> {{$company['products_count']}} </h3>

                        <h6 style = "text-align:center;"> {{$company['name']}} </h6>

                    </div>

                </div>
                
           </div>

       @endforeach
        
    </div>

</div>
<br>
<div class = "continer-fluid">

    <div class = "row">

        <div class = "col-3">

            <h3 style = "font-weight:bold; text-align:center;"> Today   </h3>

            <div class = "info-box bg-success text-center">

                <div class = "info-box-content">

                    <h4 style = "font-weight:bold; text-align:center;"> {{$todayTotalBOE}}   </h4>

                    <h6 style = "text-align:center;">   Total BOE   </h6>

                </div>

            </div>

        </div>

        <div class = "col-3">

            <h3 style = "font-weight:bold; text-align:center;"> Yesterday   </h3>

            <div class = "info-box bg-success text-center">

                <div class = "info-box-content">

                    <h4 style = "font-weight:bold; text-align:center;"> {{$yesterdayTotalBOE}}  </h4>

                    <h6 style = "text-align:center;">   Total BOE   </h6>

                </div>

            </div>

        </div>

        <div class="col-3">

            <h3 style = "font-weight:bold; text-align:center;"> Last 7 Days </h3>

            <div class = "info-box bg-success text-center">

                <div class = "info-box-content">

                    <h4 style = "font-weight:bold; text-align:center;"> {{$Last7daysBOE}}   </h4>

                    <h6 style = "text-align:center;">   Total BOE   </h6>

                </div>

            </div>

        </div>
        
        <div class="col-3">

            <h3 style = "font-weight:bold; text-align:center;"> Last 30 Days    </h3>

            <div class = "info-box bg-success text-center">
                
                <div class = "info-box-content">

                    <h4 style = "font-weight:bold; text-align:center;"> {{$Last30daysBOE}}  </h4>

                    <h6 style = "text-align:center;">   Total BOE   </h6>

                </div>

            </div>

        </div>

    </div>

</div>

@stop

