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
                <a href="/seller/import-bulk-asin">
                    <x-adminlte-button label="Asin Bulk Import" theme="primary" icon="fas fa-file-import" />
                </a>
                <!-- <a href="/seller/export-asin">
                    <x-adminlte-button label="Asin Export" theme="primary" icon="fas fa-file-export" />
                </a>


                <x-adminlte-button label="Download Asin" theme="primary" icon="fas fa-file-download" data-toggle="modal"
                    data-target="#exampleModal"></x-adminlte-button> -->


                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Asin Download</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <ul>
                                    <li>
                                        <a href="{{ route('download.asinMaster') }}">
                                            <h4>Download Asin Master</h4>

                                    </li>
                                </ul>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
                <a href="{{ route('trash.view') }}">
                    <x-adminlte-button label="Bin" theme="primary" icon="fas fa-trash" />
                </a>
            </h2>

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr class="length">
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
            ajax: "{{ url('seller/asin-master') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'asin',
                    name: 'asin',
                    orderable: false
                },
                {
                    data: 'source',
                    name: 'source',
                    orderable: false
                },
                {
                    data: 'destination_1',
                    name: 'destination_1',
                    orderable: false
                },
                {
                    data: 'destination_2',
                    name: 'destination_2',
                    orderable: false
                },
                {
                    data: 'destination_3',
                    name: 'destination_3',
                    orderable: false
                },
                {
                    data: 'destination_4',
                    name: 'destination_4',
                    orderable: false
                },
                {
                    data: 'destination_5',
                    name: 'destination_5',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },

            ]
        });
        // $(document).on('click', ".delete", function(e) {
        //     alert('On click on Delete');
        // });
        $(document).on('click', ".delete", function(e) {
                e.preventDefault();
                let bool = confirm('Are you sure you want to push this asin to Bin?');

                if(!bool) {
                    return false;
                }
                let self = $(this);
                let id = self.attr('data-id');
               
                self.prop('disable', true);
                // let loader = $('.loader');

                // let alert_dislay_div = $('.alert_display');
                // let alert_template = `<div class="alert alert-block d-none alert_main">
                //                         <button type="button" class="close" data-dismiss="alert">×</button>
                //                         <strong class="alert_message"></strong>
                //                     </div>`;
                // alert_dislay_div.html(alert_template);

                // let alert_message = $('.alert_message');
                // let alert_main = $('.alert_main');

                // loader.removeClass('d-none');

                $.ajax({
                    method: 'post',
                    url: '/seller/asin/soft-delete/'+id,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "_method": 'POST'
                    },
                    response: 'json',
                    success: function (response) {
                        alert('Delete success');
                        location.reload()
                        // self.prop('disable', false);
                        // // loader.addClass('d-none');

                        // yajra_table.ajax.reload();

                        // if(response.success) {
                        //     alert_main.removeClass('d-none alert-danger').addClass('alert-success');
                        //     alert_message.html(response.success);
                        // }

                    }, 
                    error: function (response) {

                        // self.prop('disable', false);
                        // // loader.addClass('d-none;');

                        // alert_main.removeClass('d-none alert-success').addClass('alert-danger');
                        // alert_message.html('Oops something went wrong. Contct Admin');

                    }
                });

            });

        // });
    </script>
@stop