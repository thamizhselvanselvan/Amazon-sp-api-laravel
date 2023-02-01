@extends('adminlte::page')

@section('title', 'Currency')

@section('content_header')

<div class="col text-center">
<h1 class="m-0 text-dark">Currencies</h1>
</div>
@stop

@section('content')

    <div class="row">
    <a href="{{ route('currency.home') }}">
        <x-adminlte-button label="Back" type="submit" theme="primary" icon="fas fa-arrow-left" class="btn btn-primary btn-sm" />
    </a>
        <div class="col">

            <div class="alert_display">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <h2 class="mb-4">
    </h2>
    <table class="table table-bordered yajra-datatable table-sm">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Currency Name</th>
                <th>Code</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

@stop

@section('js')

    <script type="text/javascript">
        let yajra_table = $('.yajra-datatable').DataTable({

            processing: true,
            serverSide: true,

            ajax: "{{ url('/v2/master/store/currency/trash-view') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'code',
                    name: 'code',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $(document).on('click', ".restore", function(e) {
                e.preventDefault();
                let self = $(this);
                let id = self.attr('data-id');     
                $.ajax({
                    method: 'post',
                    url: '/v2/master/store/currency/restore/'+id,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "_method": 'POST'
                    },
                    response: 'json',
                    success: function (response) {
                        $('.yajra-datatable').DataTable().ajax.reload();
                        alert('Restore success');
                    }, 
                    error: function (response) {

                    }
                });

            });
    </script>
@stop
