@extends('adminlte::page')

@section('title', 'Status Master')

@section('css')
<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
<div class="row">
    <div class="col">
        <h1 class="m-0 text-dark text-center"><strong>Status Master</strong> </h1>
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
                <form action="{{ $record ?? '' ? route('snt.booking.update') : route('snt.booking.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="update_id" value="{{ $record['id'] ?? '' }}">
                        <div class="col">
                            <x-adminlte-input label='Name' type='text' name='name' placeholder='Name' value="{{ $record['name'] ?? '' }}" />
                        </div>
                    </div>
                    <x-adminlte-button class="btn-flat" type="submit" label="{{ $record ?? '' ? 'Update' : 'Submit' }}" theme="{{ $record ?? '' ? 'primary' : 'success' }}" icon="{{ $record ?? '' ? 'fa fa-refresh' : 'fas fa-lg fa-save' }}" />
                </form>
            </div>
        </div>
    </div>
    <div class="col-1"></div>
</div>

<table class="table table-striped yajra-datatable table-bordered text-center table-sm">

    <thead class="table-info">
        <th>Id</th>
        <th>Name</th>
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
        ajax: "{{ route('snt.booking.index') }}",
        pageLength: 100,
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 'name',
                name: 'name',
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