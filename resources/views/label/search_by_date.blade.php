@extends('adminlte::page')
@section('title', 'Label')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row mb-4">
    <div class="col-0.5">
        <div style="margin-top: 0.3rem;">
            <a href="{{ route('label.manage') }}">
                <x-adminlte-button label='Back' class="btn-sm" theme="primary" icon="fas fa-long-arrow-alt-left" type="submit" />
            </a>
        </div>
    </div>
    <div class="col-3">
        <h1 class=" text-dark text-center">Label Search By Date</h1>
    </div>
</div>

@stop
@section('content')

@csrf
<div class="row">
    <div class="col">
        <div class="row">

            <div class="col float-left mt-2">
                <label>Select Date:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control float-right datepicker" name='export_date' placeholder="Select Date Range" autocomplete="off" id="search_date">

                </div>
            </div>
            <div class="col float-left mt-2">
                <div style="margin-top: 2.2rem;">
                    <x-adminlte-button label="search" theme="success" class="btn btn-sm " icon="fas fa-search" type="submit" id="label_search" />
                </div>
            </div>
        </div>
    </div>

    <div class="col"></div>

    <div class="col " style="margin-top: 2.4rem;">
        <div class="row float-right">
            <x-adminlte-button label="Print Selected" id="print_selected" theme="primary" icon="fas fa-print" class="btn-sm ml-1" />

            <x-adminlte-button label="Create Selected Zip" id="download_selected" theme="primary" icon="fas fa-download" class="btn-sm ml-1 " />

            <x-adminlte-button label="Download Label Zip" theme="primary" icon="fas fa-download" class="btn-sm ml-1" id='zip-download' data-toggle="modal" data-target='#label_download_zip' />

        </div>
    </div>

</div>

<!-- Download label zip Modal start-->
<div class="modal" id="label_download_zip">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Download Label</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body day_wise_label_download">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Download label zip Modal end-->

{{-- <div id="showTable" class="d-none">
    <table class='table table-bordered table-striped text-center ' id='label_search'>
        <thead>
            <tr class='text-bold bg-info'>

                <th>Store Name</th>
                <th>Order No.</th>
                <th>Awb No.</th>
                <th>Courier Name</th>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id='label_table'>
        </tbody>
    </table>
</div> --}}

<table class="table table-striped yajra-datatable table-bordered text-center table-sm mt-2">

    <thead class="table-info">
        <th>Select All <input type='checkbox' id='select_all'></th>
        {{-- <th>Id</th> --}}
        <th>Store Name</th>
        <th>Order No.</th>
        <th>Awb No.</th>
        <th>Courier Name</th>
        <th>Order Date</th>
        <th>Customer</th>
        <th>Action</th>
    </thead>

</table>
@stop

@section('js')
<script type="text/javascript">
    $('#select_all').change(function() {

        if ($('#select_all').is(':checked')) {
            $('.check_options').prop('checked', true);
        } else {
            $('.check_options').prop('checked', false);
        }
    });

    $('#download_selected').click(function() {
        let id = '';
        let count = 0;
        let arr = '';
        let select_date = $('#search_date').val();
        let current_page_number = $(".check_options:first").data('current-page');


        if (select_date == '') {
            alert('Please Select Date Range.');
            return false;
        }
        $("input[name='options[]']:checked").each(function() {
            if (count == 0) {
                id += $(this).val();
            } else {
                id += '-' + $(this).val();
            }
            count++;
        });
        if (count == 0) {
            alert('Please Select Label Details to Download');
            return false;
        }

        alert('Label is downloading please wait.');
        $('#download_selected').attr('disabled', true);
        $('#download_selected').attr("title", "File is downloading...");
        $.ajax({
            method: 'POST',
            url: "{{ route('label.download.selected') }}",
            data: {
                'id': id,
                'date': select_date,
                'current_page_number': current_page_number,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {

            }
        });
    });

    $('#print_selected').click(function() {

        let id = '';
        let count = '';
        $("input[name='options[]']:checked").each(function() {
            if (count == 0) {
                id += $(this).val();
            } else {
                id += '-' + $(this).val();
            }
            count++;

        });
        if (count == 0) {
            alert('Please select label details, Which you want to print.');
        } else {
            window.open("/label/print-selected/" + id, "_blank");
        }

    });

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
        let selected_date = $('#search_date').val();
        if (selected_date == '') {
            alert('No date Selected Please Select The Date');
            return false;
        } else {

            let selected_date = $('#search_date').val();
            let yajra_table = $('.yajra-datatable').DataTable({

                destroy: true,
                processing: true,
                serverSide: true,
                pageLength: 40,
                searching: false,
                bLengthChange: false,
                ajax: {
                    method: 'POST',
                    url: "{{ route('lable.search.date') }}",
                    data: function(data) {
                        data.selected_date = selected_date;
                        data._token = "{{ csrf_token() }}"
                    },

                },
                columns: [{
                        data: 'check_box',
                        name: 'check_box',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'store_name',
                        name: 'store_name',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'order_no',
                        name: 'order_no',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'awb_no',
                        name: 'awb_no',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'courier_name',
                        name: 'courier_name',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'order_date',
                        name: 'order_date',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    },
                ],
            });

        }

    });

    $('#zip-download').on('click', function() {
        $('.day_wise_label_download').empty();
        $.ajax({
            method: 'post',
            url: "{{ route('label.day.wise.zip.download') }}",
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(result) {
                console.log(result);
                $('.day_wise_label_download').append(result);
                //
            }
        });
    });

    $(document).ready(function() {
        $.ajax({
            method: 'get',
            // url: "/label/file/management/monitor/",
            url: "{{ route('label.file.management.monitor') }}",
            data: {
                "module_type": "EXPORT_LABEL",
                "_token": "{{ csrf_token() }}",
            },
            response: 'json',
            success: function(response) {
                console.log(response);
                if (response == '0000-00-00 00:00:00') {

                    $('#download_selected').prop('disabled', true);
                    $('#download_selected').attr("title", "File is downloading...");
                }

            },
        });
    });
</script>
@include('label.edit_label_details_master')
@stop