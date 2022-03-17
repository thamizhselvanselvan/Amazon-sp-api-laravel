@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark"> B2C Ship KYC Details and Status</h1>
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
             <div class="info-box bg-info">
               <div class="info-box-content">
                 <h5>{{$todayTotalBooking['totalBooking']}}</h5>
                 <span>Today KYC Received </span>
               </div>
             </div>
            </div>
          <div class="col-3 ">
             <div class="info-box bg-info">
                <div class="info-box-content">
                 <h5>{{$yesterdayTotalBooking['totalBooking']}}</h5>
                 <h5>Yesterday</h5>
                </div>
             </div>
          </div>
          <div class="col-3 ">
             <div class="info-box bg-info">
                <div class="info-box-content">
                <h5>{{$Last7DaysTotalBooking['totalBooking']}}</h5>
                <h5>Last 7 Days</h5>
                </div>
             </div>
           </div>
           <div class="col-3 ">
             <div class="info-box bg-info">
                <div class="info-box-content">
                <h5>{{$Last30DaysTotalBooking['totalBooking']}}</h5>
                <h5>Last 30 Days</h5>
                </div>
             </div>
           </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="info-box bg-success">
                <div class="info-box-content">
                <h4>{{$todayTotalBooking['kycApproved']}}</h4>
                    <span class="info-box-text">KYC Approved</span>
                </div>
            </div>
        </div>

        <div class="col">
        <div class="info-box bg-success">
                <div class="info-box-content">
                 <h4>{{$yesterdayTotalBooking['kycApproved']}}</h4>
                    <span class="info-box-text">KYC Approved</span>
                </div>
            </div>
        </div>

        <div class="col">
        <div class="info-box bg-success">
                <div class="info-box-content">
                  <h4>{{$Last7DaysTotalBooking['kycApproved']}}</h4>
                    <span class="info-box-text">KYC Approved</span>
                </div>
            </div>
        </div>
        <div class="col">
          
            <div class="info-box bg-success">
                <div class="info-box-content">
             <h4>{{$Last30DaysTotalBooking['kycApproved']}}</h4>
                    <span class="info-box-text">KYC Approved</span>
                </div>
            </div>
        </div>
    </div>
  
    <div class="row">
    <div class="col">
            <div class="info-box bg-warning">
                <div class="info-box-content">
                <h4>{{$todayTotalBooking['kycPending']}}</h4>
                    <span class="info-box-text">KYC Pending</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-warning">
                <div class="info-box-content">
                 <h4>{{$yesterdayTotalBooking['kycPending']}}</h4>
                    <span class="info-box-text">KYC Pending</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-warning">
                <div class="info-box-content">
                 <h4>{{$Last7DaysTotalBooking['kycPending']}}</h4>
                    <span class="info-box-text">KYC Pending</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-warning">
                <div class="info-box-content">
                  <h4>{{$Last30DaysTotalBooking['kycPending']}}</h4>
                    <span class="info-box-text">KYC Pending</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
    <div class="col">
            <div class="info-box bg-danger">
                <div class="info-box-content">
                <h4>{{$todayTotalBooking['kycRejected']}}</h4>
                    <span class="info-box-text">KYC Rejected</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-danger">
                <div class="info-box-content">
                <h4>{{$yesterdayTotalBooking['kycRejected']}}</h4>
                    <span class="info-box-text">KYC Rejected</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-danger">
                <div class="info-box-content">
                <h4>{{$Last7DaysTotalBooking['kycRejected']}}</h4>
                    <span class="info-box-text">KYC Rejected</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box bg-danger">
                <div class="info-box-content">
                      <h4>{{$Last30DaysTotalBooking['kycRejected']}}</h4>
                    <span class="info-box-text">KYC Rejected</span>
                </div>
            </div>
        </div>
    </div>
</div>

    @stop

    @section('js')


    @stop