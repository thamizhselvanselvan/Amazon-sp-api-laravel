@extends('adminlte::page')
@section('title', 'Search Label')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

    <div class="row justify-content-center">
        <h1 class=" text-dark text-center">Custom Label Print</h1>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="row">

                <div class="col float-left mt-2">
                    <label>Select Date:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control float-right datepicker" name='export_date'
                            placeholder="Select Date Range" autocomplete="off" id="search_date">

                    </div>
                </div>
                <div class="col float-left mt-2">
                    <div style="margin-top: 2.2rem;">
                        <x-adminlte-button label="search" theme="success" class="btn btn-sm " icon="fas fa-search"
                            type="submit" id="label_search" />
                    </div>
                </div>
            </div>
        </div>


    </div>

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
        $(".datepicker").daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
            },
        });
        $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                'YYYY-MM-DD'));
        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#label_search').click(function() {

            let date_range = $("#search_date").val();

            if (date_range == '') {
                alert("No date Selected Please Select The Date");
                return false;
            } else {

                let yajra_table = $('.yajra-datatable').DataTable({

                    processing: true,
                    serverSide: true,
                    bFilter: false,
                    destroy: true,
                    lengthChange: false,
                    ajax: {
                        url: "{{ route('custom.label.index') }}",
                        method: "GET",
                        data: function(data) {
                            data.date = date_range,
                                data.token = "{{ csrf_token() }}"
                        },
                    },
                    pageLength: 40,
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
            }
        });
    </script>

    @include('label.customLabel.custom_modal')
@stop
