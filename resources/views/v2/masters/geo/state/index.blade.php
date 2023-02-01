@extends('adminlte::page')

@section('title', 'States')

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
    <h1 class="m-0 text-dark">States</h1>

@stop
@section('content')
    @if (session()->has('message'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session()->get('message') }}
        </div>
    @elseif(session()->has('danger'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session()->get('danger') }}
        </div>
    @endif
    </div>
    </div>
    <div class="row">
        <div class="col">
            <h2 class="mb-4">
                <a href="{{ route('geo.state.create') }}">
                    <x-adminlte-button class="ml-2" label="Add State" theme="primary" icon="fas fa-plus" />
                </a>
            </h2>
            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Country Name</th>
                        <th>State Name</th>
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
            ajax: "{{ route('geo.state') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    // orderable: false,
                    // searchable: false,
                },
                {
                    data: 'country.name',
                    name: 'country.name',
                    // orderable: false,
                    // searchable: false,
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
        $(document).on('click', '.delete', function() {
            let bool = confirm('Are you sure you want to delete this ?');
            if (!bool) {
                return false;
            }
        });
    </script>
@stop
