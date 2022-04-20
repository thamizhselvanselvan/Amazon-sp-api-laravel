@extends('adminlte::page')

@section('title', 'Bins')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
    <h1 class="m-0 text-dark">Inventory Bin</h1>

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
                <a href="{{ route('inventory.bin_add') }}">
                    <x-adminlte-button label="Add Bin" theme="primary" icon="fas fa-plus" />
                </a>
            </h2>

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop


{{-- @section('js')
    <script type="text/javascript">
        $(function() {

            let yajra_table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                 ajax: "{{ url('Inventory/Master/Racks/rack_list') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: '',
                        name: ''
                    },
                ]
            });

        });
    </script>
@stop --}}
