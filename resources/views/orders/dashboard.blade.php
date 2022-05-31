@extends('adminlte::page')
@section('title', 'Orders Details')

@section('content_header')
<h1 class="m-0 text-dark">Order Details</h1>
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

        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

@stop

@section('js')

</script>

@stop