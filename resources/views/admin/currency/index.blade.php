@extends('adminlte::page')

@section('title', 'Currencies')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
    <h1 class="m-0 text-dark">Currencies</h1>
@stop

@section('content')

    <div class="row">
        <div class="col">

            <h2 class="mb-4">
                <a href="{{ route('currencys.create') }}">
                    <x-adminlte-button label="Add Currency" theme="primary" icon="fas fa-plus" />
                </a>
                <a href="{{ route('currencys.trashview') }}">
                    <x-adminlte-button label="Bin" theme="primary" icon="fas fa-trash" />
                </a>
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
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Code</th>
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
                    ajax: "{{ url('admin/currencys') }}",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'code', name: 'code'},
                        {data: 'status', name: 'status'},
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    ]
            });

            $(document).on('click', ".delete", function(e) {
                e.preventDefault();

                let bool = confirm('Are you sure you wanna delete this currency?');

                if(!bool) {
                    return false;
                }

                let self = $(this);
                let id = self.attr('data-id');
                self.prop('disable', true);
                let loader = $('.loader');

                let alert_dislay_div = $('.alert_display');
                let alert_template = `<div class="alert alert-block d-none alert_main">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong class="alert_message"></strong>
                                        </div>`;
                alert_dislay_div.html(alert_template);

                let alert_message = $('.alert_message');
                let alert_main = $('.alert_main');
                
                loader.removeClass('d-none');

                $.ajax({
                    method: 'post',
                    url: '/admin/currencys/'+id,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "_method": 'DELETE'
                    },
                    response: 'json',
                    success: function (response) {
                        self.prop('disable', false);
                        loader.addClass('d-none');

                        yajra_table.ajax.reload();

                        if(response.success) {
                            alert_main.removeClass('d-none alert-danger').addClass('alert-success');
                            alert_message.html(response.success);
                        }

                    }, 
                    error: function (response) {

                        self.prop('disable', false);
                        loader.addClass('d-none;');

                        alert_main.removeClass('d-none alert-success').addClass('alert-danger');
                        alert_message.html('Oops something went wrong. Contct Admin');
             
                    }
                });

            });
            
        });
    </script>
@stop