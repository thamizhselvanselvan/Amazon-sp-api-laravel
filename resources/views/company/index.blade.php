@extends('adminlte::page')

@section('title', 'Company Master')

@section('content_header')

    <h1 class="m-0 text-dark">Company Master</h1>
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
        </div>
    </div>
    <h2 class="mb-4">
        <a href="company/add">
            <x-adminlte-button label="Add Company" theme="primary" icon="fas fa-plus" />
        </a>
        <a href="company/trash-view">
            <x-adminlte-button label="Company Trash View" theme="primary" icon="fas fa-trash" />
        </a>
    </h2>
    <table class="table table-bordered yajra-datatable table-striped">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Company Name</th>
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

            ajax: "{{ url('company') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'company_name',
                    name: 'company_name',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $(document).on('click', ".delete", function(e) {
                e.preventDefault();
                let bool = confirm('Are you sure you want to push this asin to Bin?');

                if(!bool) {
                    return false;
                }
                let self = $(this);
                let id = self.attr('data-id');
               
                self.prop('disable', true);
                $.ajax({
                    method: 'post',
                    url: '/company/trash/'+id,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "_method": 'POST'
                    },
                    response: 'json',
                    success: function (response) {
                        $('.yajra-datatable').DataTable().ajax.reload();
                        alert('Delete success');
                    }, 
                    error: function (response) {

                    }
                });

            });
    </script>
@stop
