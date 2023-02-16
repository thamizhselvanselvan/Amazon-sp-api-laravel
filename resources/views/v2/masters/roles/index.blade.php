@extends('adminlte::page')

@section('title', 'All Roles & Permissions')


@section('content_header')
    <h1 class="m-0 text-dark">All Roles & Permissions</h1>
@stop

@section('content')

    <div class="row">
        <div class="col">

            <table class="table table-bordered yajra-datatable table-striped table-sm">
                <thead class='table-primary'>
                    <tr>
                        <th>No</th>
                        <th>Role Name</th>
                        <!-- <th>Permission</th> -->
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
                ajax: "{{ url('v2/master/roles') }}",
                pageLength: 100,
                paging:false,
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
                ]
            });

        });
    </script>
@stop
