@extends('adminlte::page')
@section('title', 'Invoice')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;
    }

    .btn-group-sm .btn,
    .btn-sm {
        padding: 0.2rem 0.2rem;
    }
</style>
@stop
@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Invoice Management</h1>
    <h2 class="mb-4 text-right col">
        <a href="search-invoice">
            <x-adminlte-button label="Search Invoice" theme="primary" icon="fas fa-search" class="btn-sm" />
        </a>

        <a href="upload">
            <x-adminlte-button label="Upload Invoice Excel" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <a href="template/download">
            <x-adminlte-button label="Download Invoice Template" theme="primary" icon="fas fa-file-download" class="btn-sm" />
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
                <th>S/N</th>
                <th>Invoice No.</th>
                <th>Invoice Date</th>
                <th>Channel</th>
                <th>Shipped By</th>
                <th>Awb No</th>
                <th>Arn No.</th>
                <th>Hsn Code</th>
                <th>Quantity</th>
                <th>Product Price</th>
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
            ajax: "{{ url('/invoice/manage') }}",
            pageLength: 1000,
            searching: false,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'invoice_no',
                    name: 'invoice_no'
                },
                {
                    data: 'invoice_date',
                    name: 'invoice_date',
                    orderable: false,
                },
                {
                    data: 'channel',
                    name: 'channel'
                },
                {
                    data: 'shipped_by',
                    name: 'shipped_by',
                },

                {
                    data: 'awb_no',
                    name: 'awb_no',
                },
                {
                    data: 'arn_no',
                    name: 'arn_no'
                },
                {
                    data: 'hsn_code',
                    name: 'hsn_code'
                },
                {
                    data: 'qty',
                    name: 'qty'
                },
                {
                    data: 'product_price',
                    name: 'product_price'
                },
                {
                    data: 'action',
                    name: 'action'
                },

            ],
        });
        $(document).ready(function() {

            $('#selected-download').click(function() {
                var url = $(location).attr('href');
                let id = '';
                let count = 0;
                let arr = '';
                $("input[name='options[]']:checked").each(function() {
                    if (count == 0) {
                        id += $(this).val();
                    } else {
                        id += '-' + $(this).val();
                    }

                    count++;
                });
                // alert(id);
                $.ajax({
                    method: 'POST',
                    url: "{{ url('/invoice/select-download')}}",
                    data: {
                        'id': id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        arr += response;
                        window.location.href = '/invoice/zip-download/' + arr;
                        // alert('Export pdf successfully');
                    }
                });

            });

            $('#select_print').click(function() {
                var url = $(location).attr('href');
                let id = '';
                let count = 0;
                let arr = '';
                $("input[name='options[]']:checked").each(function() {
                    if (count == 0) {
                        id += $(this).val();
                    } else {
                        id += '-' + $(this).val();
                    }

                    count++;
                    window.location.href = '/invoice/selected-print/' + id;
                });
            });

            $('.check_all').change(function() {
                // alert('working');
                if ($('.check_all').is(':checked')) {
                    $('.check_options').prop('checked', true);
                } else {
                    $('.check_options').prop('checked', false);
                }
            });
            $('.check_options').change(function() {
                let count = 0;
                $("input[name='options[]']:checked").each(function() {
                    count++;
                    alert(count);
                });
                if (count === 122) {
                    $('.check_all').prop('checked', true)
                } else {
                    $('.check_all').prop('checked', false);
                }
            });
        });
    </script>
    @stop