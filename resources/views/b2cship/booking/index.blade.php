@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark"> Booking Report</h1>
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
<h3 style="font-weight: bold;">Today</h3>
    <div class="row">
           <div class="col-2 ">
             <div class="info-box bg-info text-center">
               <div class="info-box-content">
                 <h3>{{$todayTotalBooking['totalBooking']}}</h3>
                 <h5>Total Booking</h5>
               </div>
             </div>
            </div>

           <div class="col-2 ">
             <div class="info-box bg-success text-center">
               <div class="info-box-content">
                 <h3>{{$todayTotalBooking['booked']}}</h3>
                 <h5> Booked Status</h5>
               </div>
             </div>
            </div>

          <div class="col-2 ">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                   <h3>{{$todayTotalBooking['delivered']}}</h3>
                 <h5> Total Delivered</h5>
                </div>
             </div>
          </div>

          <div class="col-2 ">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                <h3>{{$todayTotalBooking['intransit']}}</h3>
                <h5>Intransit</h5>
                </div>
             </div>
           </div>
           
           <div class="col-2">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                <h3>{{$todayTotalBooking['Ofd']}}</h3>
                <h5>Off To Delivery</h5>
                </div>
             </div>
           </div>

        <div class="col-2">
        <div class="info-box bg-danger text-center">
                <div class="info-box-content">
                 <h3>{{$todayTotalBooking['UnDelivered']}}</h3>
                    <h5>Un Deliverd</h5>
                </div>
            </div>
        </div>
  </div>
 <h3 style="font-weight: bold;">Yesterday</h3>
    <div class="row">
           <div class="col-2 ">
             <div class="info-box bg-info text-center">
               <div class="info-box-content">
                 <h3>{{$yesterdayTotalBooking['totalBooking']}}</h3>
                 <h5>Total Booking</h5>
               </div>
             </div>
            </div>
                 <div class="col-2 ">
             <div class="info-box bg-success text-center">
               <div class="info-box-content">
                 <h3>{{$yesterdayTotalBooking['booked']}}</h3>
                 <h5>Booked Status</h5>
               </div>
             </div>
            </div>

          <div class="col-2 ">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                   <h3>{{$yesterdayTotalBooking['delivered']}}</h3>
                 <h5>Total Delivered</h5>
                </div>
             </div>
          </div>

          <div class="col-2 ">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                  <h3>{{$yesterdayTotalBooking['intransit']}}</h3>
                <h5>Intransit</h5>
                </div>
             </div>
           </div>
           
           <div class="col-2">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                    <h3>{{$yesterdayTotalBooking['Ofd']}}</h3>
                <h5>Off To Delivery</h5>
                </div>
             </div>
           </div>

        <div class="col-2">
        <div class="info-box bg-danger text-center">
                <div class="info-box-content">
                  <h3>{{$yesterdayTotalBooking['UnDelivered']}}</h3>
                    <h5>Un Deliverd</h5>
                </div>
            </div>
        </div>
  </div>

<h3 style="font-weight: bold;" >Last 7 Days</h3>
  <div class="row">
           <div class="col-2 ">
             <div class="info-box bg-info text-center">
               <div class="info-box-content">
                 <h3>{{$Last7DaysTotalBooking['totalBooking']}}</h3>   
                <h5>Total Booking</h5>
               </div>
             </div>
            </div>
                 <div class="col-2 ">
             <div class="info-box bg-success text-center">
               <div class="info-box-content">
                 <h3>{{$Last7DaysTotalBooking['booked']}}</h3>
                 <h5>Booked Status</h5>
               </div>
             </div>
            </div>

          <div class="col-2 ">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                   <h3>{{$Last7DaysTotalBooking['delivered']}}</h3>
                 <h5>Total Delivered</h5>
                </div>
             </div>
          </div>

          <div class="col-2 ">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                  <h3>{{$Last7DaysTotalBooking['intransit']}}</h3>
                <h5>Intransit</h5>
                </div>
             </div>
           </div>
           
           <div class="col-2">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                    <h3>{{$Last7DaysTotalBooking['Ofd']}}</h3>
                <h5>Off To Delivery</h5>
                </div>
             </div>
           </div>

        <div class="col-2">
        <div class="info-box bg-danger text-center">
                <div class="info-box-content">
                  <h3>{{$Last7DaysTotalBooking['UnDelivered']}}</h3>
                    <h5>Un Delivered</h5>
                </div>
            </div>
        </div>
  </div>
   <h3 style="font-weight: bold;">Last 30 Days</h3>
     <div class="row">
           <div class="col-2 ">
             <div class="info-box bg-info text-center">
               <div class="info-box-content">
                 <h3>{{$last30DaysTotalBooking['totalBooking']}}</h3>   
                <h5>Total Booking</h5>
               </div>
             </div>
            </div>
                 <div class="col-2 ">
             <div class="info-box bg-success text-center">
               <div class="info-box-content">
                 <h3>{{$last30DaysTotalBooking['booked']}}</h3>
                 <h5>Booked Status</h5>
               </div>
             </div>
            </div>

          <div class="col-2 ">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                   <h3>{{$last30DaysTotalBooking['delivered']}}</h3>
                 <h5>Total Delivered</h5>
                </div>
             </div>
          </div>

          <div class="col-2 ">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                  <h3>{{$last30DaysTotalBooking['intransit']}}</h3>
                <h5>Intransit</h5>
                </div>
             </div>
           </div>
           
           <div class="col-2">
             <div class="info-box bg-success text-center">
                <div class="info-box-content">
                    <h3>{{$last30DaysTotalBooking['Ofd']}}</h3>
                <h5>Off To Delivery</h5>
                </div>
             </div>
           </div>

        <div class="col-2">
        <div class="info-box bg-danger text-center">
                <div class="info-box-content">
                  <h3>{{$last30DaysTotalBooking['UnDelivered']}}</h3>
                    <h5>Un Deliverd</h5>
                </div>
            </div>
        </div>
  </div>
</div>

    @stop

    @section('js')


    @stop