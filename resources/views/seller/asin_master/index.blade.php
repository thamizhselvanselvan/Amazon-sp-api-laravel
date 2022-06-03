@extends('adminlte::page')

@section('title', 'ASIN Master')

@section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark">ASIN</h1>
        <h2 class="mb-4 col text-right">
            <a href="/seller/import-bulk-asin">
                <x-adminlte-button label="Upload Asin" theme="primary" icon="fas fa-file-import" />
            </a>
            <a href="/seller/asin/delete">
                <x-adminlte-button label="Delete Asin" theme="danger" icon="fas fa-trash" />
            </a>
            <a href="{{ route('trash.view') }}">
                <x-adminlte-button label="Bin" theme="primary" icon="fas fa-trash" />
            </a>
        </h2>
    </div>
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
                    <tr class="length">
                        <th>S/N</th>
                        <th>ASIN</th>
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

            if (!bool) {
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
                url: '/seller/asin/soft-delete/' + id,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "_method": 'POST'
                },
                response: 'json',
                success: function(response) {
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
                error: function(response) {

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