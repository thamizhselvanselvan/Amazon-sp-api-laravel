@extends('adminlte::page')
@section('title', 'Orders Item Details Dashboard')

@section('content_header')
<div class='row'>

    <h1 class="m-0 text-dark">Aws Order Dashboard</h1>
</div>
<h5 class="mt-2 text-dark">Last 5 Days ZOHO ID Missing Details</h5>
@stop

@section('css')
<style>
    .table td {
        padding: 0.1rem;
    }
</style>
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

<table class="table table-striped table-bordered">
    <thead>
        <th>Store Name</th>
        <th>Amazon Order ID</th>
        <th>Booking Date</th>
        <th>Action</th>
    </thead>
    <thead>
        @foreach ($all_store_details as $key => $value )
        @if($value)
        <tr>
            <td>
                {{$key}}
            </td>
            <td>
                {{$value[count($value)-1]->amazon_order_id}}
            </td>
            <td>
                {{$value[count($value)-1]->purchase_date}}
            </td>
            <td class="toggle" id='{{$key}}'>
                More
            </td>
        </tr>

        @foreach ($value as $details)
        <tr class='{{$key}}' style="display: none;">
            <td>{{$key}}</td>
            <td>{{$details->amazon_order_id}}</td>
            <td>{{$details->purchase_date}}</td>
        </tr>
        @endforeach
        @endif
        @endforeach
    </thead>
</table>
@stop

@section('js')

<script type="text/javascript">
    $(document).ready(function() {
        $('.toggle').css('cursor', 'pointer');
        $('.toggle').css('color', 'blue');

        $('.toggle').on('click', function() {
            let this_class = this.id;
            $('.' + this_class).toggle();
        });
    });
</script>

@stop