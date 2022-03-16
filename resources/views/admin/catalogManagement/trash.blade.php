@extends('adminlte::page')

@section('title', 'Bin Users')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href=" " class="btn btn-primary">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
        <div class="col">
            <h1 class="m-0 text-dark text-center">Bin Users</h1>
        </div>
        <div class="col"></div>
    </div>
@stop

@section('content')

<div class="loader d-none">
    <div class="sub-loader position-relative ">
        <div class="lds-hourglass"></div>
        <p>Loading...</p>
    </div>
</div>

<div class="row">
    <div class="col">
        <h2 class="mb-4"></h2>

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
                    <th title="User ID">ID</th>
                    <th>User Name</th>
                    <th>Email</th>
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
                    ajax: "{{ url('admin/users/trash') }}",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                        {data: 'name', name: 'name'},
                        {data: 'user_name', name: 'user_name'},
                        {data: 'email', name: 'email'},
                    ]
            });
            
            $(document).on('click', ".restore", function(e) {
                e.preventDefault();

                let bool = confirm('Are you sure you wanna restore it?');

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
                    url: '/admin/sellers/'+id+"/restore",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "_method": 'PUT'
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