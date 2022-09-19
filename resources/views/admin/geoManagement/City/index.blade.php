@extends('adminlte::page')

@section('title', 'Cities')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
    <style>
.table td {
        padding: 0;
        padding-left: 6px;
    }
    .table th {
        padding: 2;
        padding-left: 5px;
    }
    </style>
@stop

@section('content_header')
    <h1 class="m-0 text-dark">Cities</h1>

@stop

@section('content')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
    <div class="row">
        <div class="col">        
            <h2 class="mb-4">
                <a href="/admin/geo/city/add">
                    <x-adminlte-button label="Add City" theme="primary" icon="fas fa-plus" />
                </a>
            </h2>

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>State Name</th>
                        <th>City Name</th>
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
        ajax: "{{route('city.get') }}",
        columns: [
            {
                data: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'state_name',
                name: 'state_name',
                orderable: false,
                searchable: false,
            },
            {
                data: 'name',
                name: 'name',
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
    });

    // $(document).on('click', ".delete", function(e) {
    //     e.preventDefault();
    //     let bool = confirm('Are you sure you want to push this asin to Bin?');

    //     if (!bool) {
    //         return false;
    //     }
    //     let self = $(this);
    //     let id = self.attr('data-id');

    //     self.prop('disable', true);
    //     $.ajax({
    //         method: 'post',
    //         url: '/admin.geoManagement.State/trash/' + id,
    //         data: {
    //             "_token": "{{ csrf_token() }}",
    //             "_method": 'POST'
    //         },
    //         response: 'json',
    //         success: function(response) {
    //             $('.yajra-datatable').DataTable().ajax.reload();
    //             alert('Delete success');
    //         },
    //         error: function(response) {

    //         }
    //     });

    // });
</script>
@stop