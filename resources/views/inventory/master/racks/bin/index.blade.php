@extends('adminlte::page')

@section('title', 'Bins')

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
    <h1 class="m-0 text-dark">Inventory Bins</h1>

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
                <a href="{{ route('bins.create') }}">
                    <x-adminlte-button label="Add Bin" theme="primary" icon="fas fa-plus" />
                </a>
            </h2>

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>Shelve ID</th>
                        <th>Bin ID</th>
                        <th>Name</th>
                        <th>Width</th>
                        <th>Height</th>
                        <th>Depth</th>
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
                 ajax: "{{route('bins.index') }}",
                columns: [
                    {
                        data: 'shelve_id',
                        name: 'shelve_id'
                    },
                    
                    {
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
                        data: 'width',
                        name: 'width'
                    },
                    {
                        data: 'height',
                        name: 'height'
                    },
                    {
                        data: 'depth',
                        name: 'depth'
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
              
                if(!bool) {
                    return false;
                }
                let self = $(this);
                let id = self.attr('data-id');
               
                self.prop('disable', true);


                $.ajax({
                    method: 'post',
                    url: '/inventory/bins/'+id,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "_method": 'DELETE'
                   },
                   response: 'json',
                    success: function (response) {
                        alert('Delete success');
                        location.reload()
                    }, 
                    error: function (response) {

                        
                    }
                });
            });
        });
    </script>
@stop  
