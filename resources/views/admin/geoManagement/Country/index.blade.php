@extends('adminlte::page')

@section('title', 'Countries')

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
<h1 class="m-0 text-dark">Countries</h1>

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
            <a href="/admin/geo/country/add">
                <x-adminlte-button label="Add Country" theme="primary" icon="fas fa-plus" />
            </a>
        </h2>

        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Country Name</th>
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
        ajax: "{{route('country.get') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: true,
                searchable: true
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
            },
        ]
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
    //         url: '/admin.geoManagement.Country/trash/' + id,
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