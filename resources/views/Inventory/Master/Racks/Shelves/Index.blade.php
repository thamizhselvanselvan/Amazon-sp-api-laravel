@extends('adminlte::page')

@section('title', 'Shelves')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
    <h1 class="m-0 text-dark">Inventory Racks Shelves</h1>

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
                <a href="{{ Route('inventory.Shelves_add') }}">
                    <x-adminlte-button label="Add Shelves" theme="primary" icon="fas fa-plus" />
                </a>
            </h2>

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>Rack ID</th>
                        <th>Shelves ID</th>
                        <th>Shelves Name</th>
                        <th>Number of Bins</th>
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
                ajax: "{{ url('Inventory/Master/Racks/shelves_list') }}",
                columns: [{
                        data: '',
                        name: '',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    
                    {
                        data: 'Shelves_name',
                        name: 'Shelves_name'
                    },
                    {
                        data: 'No_of_Bins',
                        name: 'No_of_Bins'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

        });
    </script>
@stop

