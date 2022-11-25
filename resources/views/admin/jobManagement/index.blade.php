@extends('adminlte::page')

@section('title', 'Failed Jobs ')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;
    }

    .table th {
        padding: 2;
        padding-left: 5px;
    }
</style>

@stop

@section('content_header')
<h1 class="m-0 text-dark">Failed Jobs </h1>
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


<div class="modal fade " id="job_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exceptionModal">Exception Details</h4>
                <a id="closemodal" class="btn btn-danger">close</a>
            </div>
            <div class="modal-body">

            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <a id="closemodal2" class="btn btn-danger">close</a>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr class="table-info">
            <th>ID</th>
            <th>UUID </th>
            <th>Connection </th>
            <th>Queue</th>
            <th>Command Name</th>
            <th>Failed At </th>
            <th>Exception</th>
        </tr>
    </thead>
    <tbody id="job_body">
    </tbody>
</table>
@stop

@section('js')
<script type="text/javascript">
    $.extend($.fn.dataTable.defaults, {
        pageLength: 100,
    });

    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,

        ajax: "{{route('jobs.management.index') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'uuid',
                name: 'uuid'
            },
            {
                data: 'connection',
                name: 'connection'
            },
            {
                data: 'queue',
                name: 'queue'
            },
            {
                data: 'command_name',
                name: 'command_name'
            },

            {
                data: 'failed_at',
                name: 'failed_at'
            },
            {
                data: 'exception',
                name: 'exception'
            },


        ]
    });

    $(document).on('click', '#job_details', function() {
        data = $(this).attr('value');

        $.ajax({
            method: 'get',
            url: "{{route ('jobs.management.exception')}}",
            data: {
                "id": data,
                "_token": "{{ csrf_token() }}",
            },
            response: 'json',
            success: function(response) {
                console.log(response['data'][0]['exception']);

                $('#job_modal').modal('show');
                if (response['data'][0]['exception'] == '') {
                    $('#job_modal .modal-body').append('No data Found');
                } else {

                    $('#job_modal .modal-body').append(response['data'][0]['exception']);
                }
            },
            error: function(response) {
                console.log(response);
            }
        });

    });
    $('#closemodal').click(function() {
        $('#job_modal').modal('hide');
    });
    $('#closemodal2').click(function() {
        $('#job_modal').modal('hide');
    });
</script>
@stop