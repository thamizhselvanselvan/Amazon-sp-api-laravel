@extends('adminlte::page')
@section('title', 'Orders Details Dashboard')

@section('content_header')
<h1 class="m-0 text-dark">Order Details Dashboard</h1>
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
    <!-- <h3 style="font-weight: bold;">Today</h3> -->
    <div class="row">
        @foreach ($order_status_count as $key => $value)
            <div class="col-2 ">
                <div class="info-box bg-info ">
                    <div class="info-box-content">
                        <h4>{{$key}}</h4>
                        @foreach ($value as $key1 => $data)
                        <h5>{{$key1}}: {{$data}}</h5>
                            
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@stop

@section('js')

</script>

@stop