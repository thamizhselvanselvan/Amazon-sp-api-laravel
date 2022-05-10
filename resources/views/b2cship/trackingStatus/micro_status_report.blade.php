@extends('adminlte::page')
@section('title', 'Status Details')

@section('content_header')
<h1 class="m-0 text-dark"> B2CShip Micro Status Report</h1>
@stop

@section('css')
<style>
    .table td {
        padding: 0.1rem;
    }
</style>
@stop

@section('content')


<table class="table table-bordered yajra-datatable table-striped" style="font-size:13px;">

    <thead>
        <div class="row">
            <div class="col">Status</div>
            <div class="col"> Today</div>
            <div class="col"> Yesterday</div>
            <div class="col"> Last 7 Days</div>
            <div class="col"> Last 30 Days</div>
        </div>

    </thead>
    <tbody>
        @foreach ($micro_status_final_array as $key => $values )

        <div class ='row'>
            {{$key}}
            @foreach ($values as $key => $value)
                <div class="col-3">
                    {{$value}}
                </div>
            @endforeach
        </div>
        @endforeach
    </tbody>
</table>

@stop

@section('js')
<script type="text/javascript">

</script>

@stop