@extends('adminlte::page')

@section('title', 'Bin ASIN')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ Route('asin-master') }}" class="btn btn-primary">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col">
            <h1 class="m-0 text-dark text-left h4 font-weight-bold">Bin Asins</h1>
        </div>
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
        $(function() {

            let yajra_table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('trash.view') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'asin',
                        name: 'asin'
                    },
                    {
                        data: 'source',
                        name: 'source'
                    },
                    {
                        data: 'destination_1',
                        name: 'destination_1'
                    },
                    {
                        data: 'destination_2',
                        name: 'destination_2'
                    },
                    {
                        data: 'destination_3',
                        name: 'destination_3'
                    },
                    {
                        data: 'destination_4',
                        name: 'destination_4'
                    },
                    {
                        data: 'destination_5',
                        name: 'destination_5'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });


        });
        $(document).on('click', ".restore", function(e) {
                e.preventDefault();
                
                let bool = confirm('Are you sure you want to restore  this asin?');

                if(!bool) {
                    return false;
                }
                let self = $(this);
                let id = self.attr('data-id');
               
                self.prop('disable', true);
                $.ajax({
                    method: 'post',
                    url: '/seller/asin/restore/'+id,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "_method": 'POST'
                    },
                    response: 'json',
                    success: function (response) {
                        
                        if(response.success) {
                            alert_main.removeClass('d-none alert-danger').addClass('alert-success');
                            alert_message.html(response.success);
                        }
                        
                        location.reload()
                    }, 
                    error: function (response) {

                        // self.prop('disable', false);
                        // // loader.addClass('d-none;');

                        // alert_main.removeClass('d-none alert-success').addClass('alert-danger');
                        // alert_message.html('Oops something went wrong. Contct Admin');

                    }
                });

            });
    </script>
@stop
