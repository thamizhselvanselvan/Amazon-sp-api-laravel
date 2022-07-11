@extends('adminlte::page')

@section('title', 'Warehouses')

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
<div class="row ">
    <div class="col">
        <h2 class="">
            <a href="{{ Route('warehouses.create') }}">
                <x-adminlte-button label="Add Warehouse" theme="primary" icon="fas fa-plus" />
            </a>
        </h2>
    </div>
    <div class="col">
        <h1 class="m-0 text-dark">Inventory Warehouses</h1>
    </div>
    <div class="col"></div>
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



        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <!-- <th>Address 2</th> -->
                    <!-- <th>Country</th>
                        <th>State</th> -->
                    <th>City</th>
                    <th>Pin Code</th>
                    <th>Contact Person Name</th>
                    <th>Phone No.</th>
                    <th>Email</th>
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
    $(function() {

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('warehouses.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'gn'
                },
                {
                    data: 'address_1',
                    name: 'adress_1'
                },
                // {
                //     data: 'address_2',
                //     name: 'address_2'
                // },
                // {
                //     data: 'country_name',
                //     name: 'country_name'
                // },
                // {
                //     data: 'state_name',
                //     name: 'state_name'
                // },
                {
                    data: 'city_name',
                    name: 'city_name'
                },
                {
                    data: 'pin_code',
                    name: 'pin_code'
                },
                {
                    data: 'contact_person_name',
                    name: 'contact_person_name'
                },
                {
                    data: 'phone_number',
                    name: 'phone_number'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
        $(document).on('click', ".delete", function(e) {
            e.preventDefault();
            let bool = confirm('Are you sure you want to delete?');

            if (!bool) {
                return false;
            }
            let self = $(this);
            let id = self.attr('data-id');

            self.prop('disable', true);


            $.ajax({
                method: 'post',
                url: '/inventory/warehouses/' + id,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "_method": 'DELETE'
                },
                response: 'json',
                success: function(response) {
                    alert('Delete success');
                    location.reload()
                },
                error: function(response) {


                }
            });
        });
    });
</script>
@stop