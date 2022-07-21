@extends('adminlte::page')

@section('title', 'SMSA Tracking')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">SMSA Tracking</h1>
    <h2 class="mb-4 text-right col">
        <a href="{{Route('shipntrack.smsa.upload')}}">
            <x-adminlte-button label="Add New SMSA AWB No." theme="primary" icon="fas fa-file-upload" class="btn-sm" />
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
    <table class="table table-bordered yajra-datatable table-striped text-center" style="line-height:12px">
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
        </tbody>
    </table>
</div>
@stop

@section('js')
<script type="text/javascript">
        $(function() {

            let yajra_table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('/shipntrack/smsa') }}",
                pageLength:50,
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'awbno',
                        name: 'awbno'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'details',
                        name: 'details'
                    },
                    {
                        data: 'location',
                        name: 'location',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

        });
</script>
@stop