@extends('adminlte::page')

@section('title', 'Forwarder Mapping')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Forwarder Mapping</h1>
    <h2 class="mb-4 text-right col">
        <a href="{{Route('shipntrack.forwarder.upload')}}">
            <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <a href="{{Route('shipntrack.forwarder.template')}}">
            <x-adminlte-button label="Download Templates" theme="primary" icon="fas fa-file-download" class="btn-sm" />
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
    <!-- <table class="table table-bordered yajra-datatable table-striped text-center" style="line-height:12px">
        <thead>
            <tr>
                <th>ID</th>
                <th>AWB No.</th>
                <th>Date</th>
                <th>Activity</th>
                <th>Status</th>
                <th>Location</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody> -->
    </table>
</div>
@stop

@section('js')
<script type="text/javascript">

</script>
@stop