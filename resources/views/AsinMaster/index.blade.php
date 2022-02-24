@extends('adminlte::page')

@section('title', 'ASIN Master')

@section('content_header')
<h1 class="m-0 text-dark">ASIN</h1>
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

                    <!-- <a href="add-asin">
                        <x-adminlte-button label="Add Asin" theme="primary" icon="fas fa-plus-circle"/>
                    </a> -->
                    <a href="import-bulk-asin">
                        <x-adminlte-button label="Asin Bulk Import" theme="primary" icon="fas fa-file-import"/>
                    </a>

                </h2>
               
                <table class="table table-bordered yajra-datatable table-striped">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>ASIN</th>
                            <th>Source</th>
                            <th>Destination 1</th>
                            <th>Destination 2</th>
                            <th>Destination 3</th>
                            <th>Destination 4</th>
                            <th>Destination 5</th>
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
            ajax: "{{ url('asin-master') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'asin', name: 'asin'},
                {data: 'source', name: 'source'},
                {data: 'destination_1', name: 'destination_1'},
                {data: 'destination_2', name: 'destination_2'},
                {data: 'destination_3', name: 'destination_3'},
                {data: 'destination_4', name: 'destination_4'}, 
                {data: 'destination_5', name: 'destination_5'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
                
            ]
        });
     
</script>   
@stop