@extends('adminlte::page')
@section('title', 'Search Label')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content')
    <div class="row">
        <div class="col">
            <div class="alert_display">
                @if ($message = Session::get('success'))
                    <div class="alert alert-warning alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div id="showTable">
        <table class='table table-bordered yajra-datatable table-striped text-center'>
            <thead>
                <tr class='text-bold bg-info'>
                    <th>Store Name</th>
                    <th>Order No.</th>
                    <th>Awb No.</th>
                    <th>Forwarder</th>
                    <th>Order Date</th>
                    <th>Customer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='checkTable'>

            </tbody>
        </table>
    </div>

@stop

@section('js')
    <script text='javascript/text'>
        let yajra_table = $('.yajra-datatable').DataTable({

            processing: true,
            serverSide: true,
            bFilter: false,
            lengthChange: false,
            ajax: "{{ route('custom.label.index') }}",
            pageLength: 100,
            columns: [{
                    data: 'store_name',
                    name: 'store_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'order_no',
                    name: 'order_no',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'awb_no',
                    name: 'awb_no',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'forwarder',
                    name: 'forwarder',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'purchase_date',
                    name: 'purchase_date',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },

            ],
        });
    </script>

    @include('label.custom_label.custom_modal')
@stop
