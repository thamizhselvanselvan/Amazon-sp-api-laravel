@extends('adminlte::page')

@section('title', 'Packet Details')

@section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark col"><b>Packet Details</b></h1>
        <h2 class="mb-4 text-right col">

        </h2>
    </div>
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
    <div class="pl-2">
        <div class="row">
            <div class="col-2">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Origin:</th>
                            <td></td>
                        <tr>
                            <th>Destination:</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Conginor:</th>
                            <td>{{ $forwarder_details['consignor'] }}</td>
                        </tr>
                        <tr>
                            <th>Conginee:</th>
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
    </div>
@stop

@section('js')

@stop
