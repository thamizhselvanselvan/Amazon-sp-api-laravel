@extends('adminlte::page')

@section('title', 'Regions')

@section('css')



@stop

@section('content_header')
<div class="col text-center">
    <h1 class="m-0 text-dark">Regions</h1>
</div>
@stop

@section('content')

    <div class="row">
        <div class="col">
            <h2 class="mb-4">
                <a href="{{route('regions.create')}}">
                    <x-adminlte-button label="Add Region" theme="primary" icon="fas fa-plus" />
                </a>
             
                 <!-- <a href="{{route('trash-view.region')}} ">
                    <x-adminlte-button label="Bin" theme="primary" icon="fas fa-trash" />
                </a> -->
            </h2> 

            <div class="alert_display">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>
            
            <table class="table table-bordered yajra-datatable table-striped">
                <thead class='table-primary'>
                    <tr>
                        <th>No</th>
                        <th>Region</th>
                        <th>Region Code</th>
                        <th>URLs</th>
                        <th>Marketplace ID</th>
                        <th>Currency</th>
                        <th>Status</th>
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
        $(function () {

            let yajra_table = $('.yajra-datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ url('/v2/master/store/regions') }}",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                        {data: 'region', name: 'region'},
                        {data: 'region_code', name: 'region_code'},
                        {data: 'url', name: 'url'},
                        {data: 'marketplace_id', name: 'marketplace_id'},
                        {data: 'currency', name: 'currency'},
                        {data: 'status', name: 'status'},
                        {data: 'action',name: 'action',orderable: false,searchable: false}
                        
                        
                    ]
            });

            $(document).on('click', ".delete", function(e) {
        e.preventDefault();
        let bool = confirm('Are you sure you want to delete this region?');

        if (!bool) {
            return false;
        }
        let self = $(this);
        let id = self.attr('data-id');
        self.prop('disable', true);
        $.ajax({
            method: 'post',
            url: '/v2/master/store/regions/delete/' + id,
            data: {
                "_token": "{{ csrf_token() }}",
                "_method": 'POST'
            },
            response: 'json',
            success: function(response) {
                $('.yajra-datatable').DataTable().ajax.reload();
                alert('Delete success');
                window.location='/v2/master/store/regions'
            },
            error: function(response) {

            }
        });

    });

            
        });
    </script>
@stop