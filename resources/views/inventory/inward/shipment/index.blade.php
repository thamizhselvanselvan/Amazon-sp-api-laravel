@extends('adminlte::page')

@section('title', 'Shipment')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
    <h1 class="m-0 text-dark">Shipment</h1>

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
            <a href="{{ route('shipments.create') }}">
                    <x-adminlte-button label="Create Shipment" theme="primary" icon="fas fa-plus" />
                </a>
                <!-- <a href="{{ route('shipments.view') }}">
                    <x-adminlte-button label="View Shipment" theme="primary" icon="fas fa-file" />
                </a> -->
            </h2> 

            </h2> 

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Shipment ID</th>
                        <th>Source</th>
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
                ajax: "{{ route('shipments.index') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ship_id',
                        name: 'ship_id'
                    },
                    {
                        data: 'source_name',
                        name: 'source_name'
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
                    url: '/inventory/shipments/'+id,
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

