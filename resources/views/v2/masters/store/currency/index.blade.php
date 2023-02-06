@extends('adminlte::page')

@section('title', 'Currencies')

@section('content_header')
<div class="col text-center">
<h1 class="m-0 text-dark">Currencies</h1>
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
    </div>
</div>
<h2 class="mb-4">
    <a href="/v2/master/store/currency/create">
        <x-adminlte-button label="Add Currency" theme="primary" icon="fas fa-plus" />
    </a>
    <!-- <a href="/v2/master/store/currency/trash-view">
        <x-adminlte-button label="Bin" theme="primary" icon="fas fa-trash" />
    </a> -->
</h2>
<table class="table table-bordered yajra-datatable table-sm">
    <thead class='table-primary'>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Code</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

@stop

@section('js')

<script type="text/javascript">
    let yajra_table = $('.yajra-datatable').DataTable({

        processing: true,
        serverSide: true,

        ajax: "{{ url('/v2/master/store/currency') }}",
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 'name',
                name: 'name',
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
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });

    $(document).on('click', ".delete", function(e) {
        e.preventDefault();
        let bool = confirm('Are you sure you want to delete this currency?');

        if (!bool) {
            return false;
        }
        let self = $(this);
        let id = self.attr('data-id');

        self.prop('disable', true);
        $.ajax({
            method: 'post',
            url: '/v2/master/store/currency/delete/' + id,
            data: {
                "_token": "{{ csrf_token() }}",
                "_method": 'POST'
            },
            response: 'json',
            success: function(response) {
                $('.yajra-datatable').DataTable().ajax.reload();
                alert('Delete success');
                window.location='/v2/master/store/currency'
            },
            error: function(response) {

            }
        });

    });
</script>
@stop