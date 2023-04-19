@extends('adminlte::page')

@section('title', 'Packet Details')

<!-- @section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark col"><b>Packet Details</b></h1>
        <h2 class="mb-4 text-right col">

        </h2>
    </div>
@stop -->
<style>
    li{
        list-style: none;
    }
    .detail{
        line-height: 25px;
        padding:0;
    }
    .history-tl-container{
    font-family: "Roboto",sans-serif;
    margin:auto;
    display:block;
    position:relative;
    }
    .history-tl-container ul.tl{
        margin:20px 0;
        padding:0;
        display:inline-block;

    }
    .history-tl-container ul.tl li{
        list-style: none;
        margin:auto;
        margin-left:200px;
        min-height:50px;
        border-left:1px dashed #86D6FF;
        padding:0 0 50px 30px;
        position:relative;
    }
    .history-tl-container ul.tl li:last-child{ border-left:0;}
    .history-tl-container ul.tl li::before{
        position: absolute;
        left: -13px;
        top: -5px;
        content:"";
        border: 8px solid rgba(255, 255, 255, 0.74);
        border-radius: 500%;
        background: #258CC7;
        height: 25px;
        width: 25px;
        transition: all 500ms ease-in-out;
    }
    .history-tl-container ul.tl li:nth-child(1)::after{
        position: absolute;
        left: -12px;
        top: -6px;
        content:"🚚";
        color:green;
        height: 20px;
        width: 20px;
        transition: all 500ms ease-in-out;
    }
    .history-tl-container ul.tl li:hover::before{
        border-color:  #258CC7;
        transition: all 1000ms ease-in-out;
    }

    ul.tl li .item-detail{
        color:rgba(0,0,0,0.5);
        font-size:12px;
    }
    ul.tl li .timestamp{
        color: #8D8D8D;
        position: absolute;
        width:100px;
        left: -50%;
        text-align: right;
        font-size: 12px;
    }
    .model-hide-bg{
        position: fixed;
            top:0;
            left: 0;
            height:100vh;
            width:100vw;
            background:#afafaf;
            z-index:1100;
    }
    .tracking-details{
            background: white;
            position: absolute;
            top:30px;
            right: 50%;
            transform: translate(50%);
            border-radius: 5px;
            width:40%; 
            padding:10px;
            overflow-y: auto;
            z-index:1100;
            padding: 10px;
    }
</style>
@section('content')
    <div>
        <div class="model-hide-bg"></div>
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

                <div class="alert_display">
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- <div class="pl-2">
            <div class="row">
                <div class="col-2">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Origin:</th>
                                <td>{{ $forwarder_details['origin'] }}</td>
                            <tr>
                                <th>Destination:</th>
                                <td>{{ $forwarder_details['destination'] }}</td>
                            </tr>
                            <tr>
                                <th>Consignor:</th>
                                <td>{{ $forwarder_details['consignor'] }}</td>
                            </tr>
                            <tr>
                                <th>Consignee:</th>
                                <td>{{ $forwarder_details['consignee'] }}</td>
                            </tr>
                        </thead>

                    </table>
                </div>
                <div class="col-10">

                    <table class="table table-bordered yajra-datatable table-striped text-center">

                        <thead>
                            <th>Courier Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Activity</th>
                            <th>Location</th>
                        </thead>


                        <tbody>
                            @foreach ($records as $record)
                                <tr>
                                    @foreach ($record as $key => $result)
                                        @if ($key == 'date')
                                            <td>{{ date('Y-m-d', strtotime($record['date'] ?? 'NA')) }}
                                            </td>
                                            <td>{{ date('H:i:s', strtotime($record['date'] ?? 'NA')) }}
                                            </td>
                                        @elseif ($key == 'update_date_time')
                                            <td>{{ date('Y-m-d', strtotime($record['update_date_time'] ?? 'NA')) }}
                                            </td>
                                            <td>{{ date('H:i:s', strtotime($record['update_date_time'] ?? 'NA')) }}
                                            </td>
                                        @else
                                            <td>{{ $result }}</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div> -->

        <div class="tracking-details">

            <div class="detail border-bottom d-flex justify-content-between">
                <h4 class="font-weight-bold py-2">Packet Details</h4>
                <ul>
                    <li><span class="font-weight-bold">Origin:</span>  {{ $forwarder_details['origin'] }}</li>
                    <li><span class="font-weight-bold">Destination:</span>  {{ $forwarder_details['destination'] }}</li>
                    <li><span class="font-weight-bold">Consignor:</span>  {{ $forwarder_details['consignor'] }}</li>
                    <li><span class="font-weight-bold">Consignee:</span>  {{ $forwarder_details['consignee'] }}</li>
                </ul>
            </div>
            <div class="history-tl-container">
                <ul class="tl">
                    @foreach ($records as $record)
                        <li class="tl-item" ng-repeat="item in retailer_history">
                            @foreach ($record as $key => $result)
                                <div class="timestamp">
                                    @if ($key == 'date')
                                        {{ date('Y-m-d', strtotime($record['date'] ?? 'NA')) }}<br> {{ date('H:i:s', strtotime($record['date'] ?? 'NA')) }}                 
                                    @elseif ($key == 'update_date_time')
                                    {{ date('Y-m-d', strtotime($record['update_date_time'] ?? 'NA')) }}
                                    <br>{{ date('H:i:s', strtotime($record['update_date_time'] ?? 'NA')) }}
                                                    
                                    @else
                                        {{ date('Y-m-d', strtotime($record['action_date'] ?? 'NA')) }}
                                        <br>{{ date('H:i:s', strtotime($record['action_time'] ?? 'NA')) }}
                                    @endif
                                </div>
                            @endforeach
                                <div class="item-title">{{ $record['event_detail'] }}</div>
                                <div class="item-detail">{{ $record['courier_name'] }}</div>          
                                <div class="item-detail">{{ $record['location'] }}</div>
                        </li>         
                    @endforeach
                        
                    <!-- <li class="tl-item" ng-repeat="item in retailer_history">
                        <div class="timestamp">
                            19th March 2015<br> 3:00 PM
                        </div>
                        <div class="item-title">Aramex</div>
                        <div class="item-detail">Delivered</div>
                        <div class="item-detail">Alquoz Branch, United Arab Emirates</div>
                    </li>
                    <li class="tl-item" ng-repeat="item in retailer_history">
                        <div class="timestamp">
                            1st June 2015<br> 7:00 PM
                        </div>
                        <div class="item-title">Aramex</div>
                        <div class="item-detail">Out for Delivery</div>
                        <div class="item-detail">Alquoz Branch, United Arab Emirates</div>
                    </li> -->

                </ul>

            </div>
        </div>
    </div>
@stop

@section('js')

@stop
