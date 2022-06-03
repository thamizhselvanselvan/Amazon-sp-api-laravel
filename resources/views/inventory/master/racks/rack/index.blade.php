@extends('adminlte::page')

@section('title', 'Racks')

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
    <h1 class="m-0 text-dark">Inventory Racks</h1>

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
                <a href="{{ route('racks.create') }}">
                    <x-adminlte-button label="Add Rack" theme="primary" icon="fas fa-plus" />
                </a>
            </h2>

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Rack ID</th>
                        <th>Rack Name</th>
                        <th>Number of Shelves</th>
                        <th>Shelves Name</th>
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
                ajax: "{{ route('racks.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'rack_id',
                        name: 'rack_id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'shelves_no',
                        name: 'shelves_no'
                    },
                    {
                        data: 'shelve_name',
                        name: 'shelve_name'
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
                    url: "/inventory/racks/"+id,
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
