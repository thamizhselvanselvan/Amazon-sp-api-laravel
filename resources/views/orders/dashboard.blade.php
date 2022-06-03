@extends('adminlte::page')
@section('title', 'Orders Details Dashboard')

@section('content_header')
<div class= 'row'>

    <h1 class="m-0 text-dark">Order Details Dashboard</h1>
    <h2 class = 'text-right col'>
        <a href="{{route('getOrder.list')}}">
            <x-adminlte-button label="Refresh" theme="primary" icon="fas fa-sync" />
        </a>
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
    </div>
</div>

<!-- <div class="container-fluid">
    <h3 style="font-weight: bold;">Last 3 Days Details</h3>
    <div class="row">
        @foreach ($order_status_count as $key => $value)
            <div class="col-2 ">
                <div class="info-box bg-info ">
                    <div class="info-box-content">
                        <h4>{{$key}}</h4>
                        @foreach ($value as $key1 => $data)
                        @if($data != NULL)
                            <h5>{{$key1}}: {{$data}}</h5>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div> -->
<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr>
            <th>Store Name</th>
            <th>Last Update</th>
            <th>Unshipped</th>
            <th>Pending</th>
            <th>Canceled</th>
            <th>Shipped</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order_status_count as $key => $value)
        <tr>
            <td>{{$key}}</td>
            @foreach ($value as $data )
            <td>{{$data}}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

@stop

@section('js')

</script>

@stop