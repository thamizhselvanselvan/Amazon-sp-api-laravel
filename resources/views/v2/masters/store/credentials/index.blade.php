@extends('adminlte::page')

@section('title', 'Credentials')

@section('content_header')
<div class="col text-center">
<h1 class="m-0 text-dark">Credentials</h1>
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
   
    <h2 class="mb-4">
        <a href="/v2/master/store/credentials/create">
            <x-adminlte-button label="Add Credentials" theme="primary" icon="fas fa-plus" />
        </a>
    <!-- <a href="/v2/master/store/credentials/trash-view">
        <x-adminlte-button label="Bin" theme="primary" icon="fas fa-trash" />
    </a> -->
    </h2>
    <table class="table table-bordered yajra-datatable table-sm" style="width:100%">
        <thead class='table-primary'>
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
</div>
</div>

@stop

@section('js')

<script type="text/javascript">
    let yajra_table = $('.yajra-datatable').DataTable({

        processing: true,
        serverSide: true,

        ajax: "{{ url('/v2/master/store/credentials') }}",
        scrollY:        true,
        scrollX:        true,
        scrollCollapse: true,
        autowidth :false,
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
        ],
        columnDefs: [
            { "width":"50px" , "targets": 0},
            { "width":"100px" , "targets": 1},
            { "width":"100px" , "targets": 2},
            { "width":"150px" , "targets":3},
            { "width":"50px" , "targets": 4},
            { "width":"50px" , "targets": 5},
            { "width":"120px" , "targets": 6}
           
        ],
        fixedColumns: true
    });

    $(document).on('click', ".delete", function(e) {
        e.preventDefault();
        let bool = confirm('Are you sure you want to delete this credential?');

        if (!bool) {
            return false;
        }
        let self = $(this);
        let id = self.attr('data-id');

        self.prop('disable', true);
        $.ajax({
            method: 'post',
            url: '/v2/master/store/credentials/delete/' + id,
            data: {
                "_token": "{{ csrf_token() }}",
                "_method": 'POST'
            },
            response: 'json',
            success: function(response) {
                $('.yajra-datatable').DataTable().ajax.reload();
                alert('Delete success');
                $('.alert_display').html(response);
            },
            error: function(response) {

            }
        });

    });
</script>
@stop