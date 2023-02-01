@extends('adminlte::page')
@section('title', 'OMS Status Master')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class='row'>
    <a href="{{ route('oms.home') }}">
        <x-adminlte-button label="Back" type="submit" theme="primary" icon="fas fa-arrow-left" class="btn btn-primary btn-sm" />
    </a>

    <div class="col text-center">
        <h1 class="mb-1 text-dark font-weight-bold"> OMS Status Master </h1>

    </div>

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
            @elseif($message = Session::get('danger'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>

        <table class="table table-bordered yajra-datatable table-striped text-center table-sm">
            <thead>
                <tr class="length">
                    <th>S/N</th>
                    <th>Code</th>
                    <th>Status</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

    </div>
</div>

@stop

@section('js')
<script>
    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        pageLength: 100,
        ajax: "{{ url('v2/oms/status-master/recycle') }}",
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 'code',
                name: 'code',
            },
            {
                data: 'status',
                name: 'status',
            },
            {
                data: 'active',
                name: 'active',
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });

    $(document).on('click', '.restore', function() {
        let bool = confirm('Are you sure you want to restore this ?');
        if (!bool) {
            return false;
        }
    });
</script>
@stop