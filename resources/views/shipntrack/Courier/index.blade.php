@extends('adminlte::page')

@section('title', 'Courier')

@section('css')
<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
<div class="row">
    <div class="col">
        <h1 class="m-0 text-dark text-center"><strong>Courier</strong> </h1>
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
<div class="row">
    <div class="col-1"></div>
    <div class="col">

        <div class="card">
            <div class="card-body">
                <form action="{{ $record ?? '' ? route('snt.courier.update') : route('snt.courier.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="update_id" value="{{ $record['id'] ?? '' }}">
                        <div class="col-5">
                            <x-adminlte-input label='Courier Name' type='text' name='c_name' placeholder='Name' value="{{ $record['courier_name'] ?? '' }}" />
                        </div>
                        <div class="col-5">
                            <x-adminlte-input label='Courier Code' type='text' name='code' placeholder='Code' value="{{ $record['courier_code'] ?? '' }}" />
                        </div>
                        <div class="col-2">
                            <div style="margin-top: 2.0rem;">
                                <x-adminlte-button class="btn-flat" type="submit" label="{{ $record ?? '' ? 'Update' : 'Submit' }}" theme="{{ $record ?? '' ? 'primary' : 'success' }}" icon="{{ $record ?? '' ? 'fa fa-refresh' : 'fas fa-lg fa-save' }}" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-1"></div>
</div>

<table class="table table-striped yajra-datatable table-bordered text-center table-sm">

    <thead class="table-info">
        <th>Id</th>
        <th>Courier Name</th>
        <th>Courier Code</th>
        <th>Action</th>
    </thead>

</table>
@stop

@section('js')
<script>
    let yajra_table = $('.yajra-datatable').DataTable({

        processing: true,
        serverSide: true,
        bFilter: false,
        lengthChange: false,
        ajax: "{{ route('shipntrack.courier.index') }}",
        pageLength: 100,
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 'courier_name',
                name: 'courier_name',
                orderable: false,
                searchable: false
            },
            {
                data: 'courier_code',
                name: 'courier_code',
                orderable: false,
                searchable: false
            },
            {
                data: 'action',
                name: 'action',
            },

        ],
    });

    $(document).on('click', '.remove', function() {
        let bool = confirm('Are you sure you want to delete?');
        if (!bool) {
            return false;
        }
    })
</script>
@stop