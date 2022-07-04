@extends('adminlte::page')
@section('title', 'Label')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .table td {
            padding: 0;
            padding-left: 5px;

        }

        .btn-group-sm .btn,
        .btn-sm {
            padding-left: 0.2rem 0.2rem;
        }
    </style>
@stop

@section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark col">Label Management</h1>
        <h2 class="mb-4 text-right col">
            <a href="search-label">
                <x-adminlte-button label="Search Label" theme="primary" icon="fas fa-search" class="btn-sm" />
            </a>
            <a href="upload">
                <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
            </a>
            <a href="excel/template">
                <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download" class="btn-sm" />
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
        </div>
    </div>
    <div class="pl-2">
        <table class="table table-bordered yajra-datatable table-striped text-center">
            <thead>
                <tr class="text-bold bg-info">

                    <th>Sr</th>
                    <th>Store Name </th>
                    <th>Order No.</th>
                    <th>Awb No.</th>
                    <th>Order Date</th>
                    <th>SKU</th>
                    <th>Customer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

    @stop

    @section('js')
        <script>
            let yajra_table = $('.yajra-datatable').DataTable({

                processing: true,
                serverSide: true,
                ajax: "{{ url('/label/manage') }}",
                pageLength: 50,
                columns: [{
                        data: 'sn',
                        name: 'sn',
                        orderable: false,
                        searchable: false
                    },
                    // {
                    //     data: 'status',
                    //     name: 'status'
                    // },
                    {
                        data: 'store_name',
                        name: 'store_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'order_no',
                        name: 'order_no',
                    },
                    {
                        data: 'awb_no',
                        name: 'awb_no',
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'seller_sku',
                        name: 'seller_sku',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
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

            $(document).ready(function() {
                $('.check_all').change(function() {

                    if ($('.check_all').is(':checked')) {
                        $('.check_options').prop('checked', true);
                    } else {
                        $('.check_options').prop('checked', false);
                    }
                });

                $('#print_selected').click(function() {
                    // alert('working');
                    let id = '';
                    let count = '';
                    $("input[name='options[]']:checked").each(function() {
                        if (count == 0) {
                            id += $(this).val();
                        } else {
                            id += '-' + $(this).val();
                        }
                        count++;
                        window.location.href = '/label/print-selected/' + id;
                    });
                    // alert(id);
                });

                $('#download_selected').click(function() {
                    let id = '';
                    let count = '';
                    let arr = '';
                    $("input[name='options[]']:checked").each(function() {
                        if (count == 0) {
                            id += $(this).val();
                        } else {
                            id += '-' + $(this).val();
                        }
                        count++;
                    });
                    $.ajax({
                        method: 'POST',
                        url: "{{ url('/label/select-download') }}",
                        data: {
                            'id': id,
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            arr += response;
                            window.location.href = '/label/zip-download/' + arr;
                            // alert('Export pdf successfully');
                        }
                    });
                });
            });
        </script>
    @stop
