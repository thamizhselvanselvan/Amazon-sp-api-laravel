@extends('adminlte::page')
@section('title', 'Orders Item Details Dashboard')

@section('content_header')
<div class='row'>

    <h1 class="m-0 text-dark">Order Item Details Dashboard</h1>
    <!-- <h2 class='text-right col'>
        <a href="{{route('getOrder.list')}}">
            <x-adminlte-button label="Refresh" theme="primary" icon="fas fa-sync" />
        </a>
    </h2> -->
</div>
<h5 class="mt-2 text-dark">Last 30 days statistics</h5>
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
        <th>Last Update</th>
    </thead>
    <thead>
        @foreach ($age as $key => $value)
        <tr>
            <td>{{$key}}</td>
            <td>{{ $value }}</td>
        </tr>
        @endforeach
    </thead>
</table>
@stop

@section('js')

</script>

@stop