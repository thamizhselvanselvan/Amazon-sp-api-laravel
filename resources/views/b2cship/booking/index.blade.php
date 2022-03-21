@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark"> Booking Status</h1>
@stop

@section('content')



<div class="row">
    <div class="col">
        <div class="alert_display">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
           <div class="col-3 ">
             <div class="info-box bg-info text-center">
               <div class="info-box-content">
                 <h2>{{$todaysTotalBooking['totalBooking']}}</h2>
                 <h5> Today's Total Booking</h5>
               </div>
             </div>
            </div>

          <div class="col-3 ">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                   <h2>00</h2>
                 <h5>  Today's Total Deliverd</h5>
                </div>
             </div>
          </div>

          <div class="col-3 ">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                <h2>00</h2>
                <h5>Intransist</h5>
                </div>
             </div>
           </div>
           
           <div class="col-3">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                <h2>00</h2>
                <h5>Picked-up</h5>
                </div>
             </div>
           </div>
         </div>   
        <div class="row">
        <div class="col-3">
            <div class="info-box bg-danger text-center">
                <div class="info-box-content">
                <h2>00</h2>
                    <h5>RTO</h5>
                </div>
            </div>
        </div>

        <div class="col-3">
        <div class="info-box bg-danger text-center">
                <div class="info-box-content">
                 <h2>00</h2>
                    <h5>Un Deliverd</h5>
                </div>
            </div>
        </div>
     </div>
  </div>
</div>

    @stop

    @section('js')


    @stop