@extends('adminlte::page')

@section('title', 'Credential')

@section('content_header')
<h1 class="m-0 text-dark">Credentials</h1>
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

                <table class="table table-bordered yajra-datatable table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Store Name</th>
                            <th>Merchant ID </th>
                            <!-- <th>Regions</th> -->
                            <!-- <th>Marketplace ID</th>
                            <th>Currencies Name</th> -->
                            <th>Verified</th>
                            <th>Status</th>
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
     $(function () {

            let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('admin/credentials') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'store_name', name: 'store_name'},
                {data: 'merchant_id', name: 'merchant_id'},
                // {data: 'region', name: 'region'},
                // {data: 'marketplace_id', name: 'marketplace_id'},
                // {data: 'name', name: 'name'},
                {data: 'verified', name: 'verified'}, 
                {data: 'status', name: 'status'},
               
            ]
        });
     });


</script>   
@stop