@extends('adminlte::page')

@section('title', 'Credentials')

@section('content_header')

<div class="col text-center">
    <h1 class="m-0 text-dark">Credentials</h1>
</div>
@stop

@section('content')

<div class="row">
    <a href="{{ route('credentials.home') }}">
        <x-adminlte-button label="Back" type="submit" theme="primary" icon="fas fa-arrow-left" class="btn btn-primary btn-sm" />
    </a>
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
</h2>
<table class="table table-bordered yajra-datatable table-sm">
    <thead>
        <tr>
            <th>ID</th>
            <th>Company</th>
            <th>Store Name</th>
            <th>Seller/Merchant ID</th>
            <th>Auth Code</th>
            <th>Marketplace ID</th>
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

        ajax: "{{ url('/v2/master/store/credentials/trash-view') }}",
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 'company',
                name: 'company',
            },
            {
                data: 'store_name',
                name: 'store_name',
            },
            {
                data: 'merchant_id',
                name: 'merchant_id',
            },
            {
                data: 'authcode',
                name: 'authcode',
            },
            {
                data: 'region',
                name: 'region',
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });

    $(document).on('click', ".restore", function(e) {
        e.preventDefault();
        let self = $(this);
        let id = self.attr('data-id');
        $.ajax({
            method: 'post',
            url: '/v2/master/store/credentials/restore/' + id,
            data: {
                "_token": "{{ csrf_token() }}",
                "_method": 'POST'
            },
            response: 'json',
            success: function(response) {
                $('.yajra-datatable').DataTable().ajax.reload();
                alert('Restore success');
            },
            error: function(response) {

            }
        });

    });
</script>
@stop