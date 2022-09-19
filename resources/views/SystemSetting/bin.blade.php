@extends('adminlte::page')
@section('title', 'System Setting')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class='row'>
    <a href="{{ route('system.setting.home') }}" class="btn btn-primary btn-sm" >
        <i class="fas fa-arrow-left"></i> back
    </a>

    <div class="col text-center">
        <h1 class="mb-1 text-dark font-weight-bold"> System Setting </h1>

    </div>
    <!-- <div class="col-5 text-right">
        <a href="test">
            <x-adminlte-button label="Bin" type="submit" theme="primary" icon="far fa-trash-alt text-danger" class="btn btn-sm  ml-2" />
        </a>
    </div> -->
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
                    <th>Key</th>
                    <th>Key Value</th>
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
            ajax: "{{ url('admin/system-setting/recycle') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'key',
                    name: 'key',
                },
                {
                    data: 'value',
                    name: 'value',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
</script>
@stop