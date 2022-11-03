@extends('adminlte::page')
@section('title', 'Search Invoice')

@section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark col">Invoice Management</h1>
        <h6 class="mb-4 text-right col">
            <div>
                <label class="">
                    Search:
                </label>
                <input type="text" id="Searchbox" class="d-inline-block" placeholder="search invoice" autocomplete="off" />
            </div>
        </h6>
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
    <div class="container-fluid search-box">
        <div class="row">
            <div class="col pt-2">
                <div class="mt-4">
                    <a href="upload">
                        <x-adminlte-button label="Add Records" theme="primary" icon="fas fa-file-upload"
                            class="btn-md ml-2 " />
                    </a>
                    <a href="template/download">
                        <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-download"
                            class="btn-md ml-1 " />
                    </a>
                    <a>
                        <x-adminlte-button label="Download Invoice Zip" theme="primary" icon="fas fa-download"
                            class="btn-md ml-2 " id='zip-download' data-toggle="modal"
                            data-target='#invoice_download_zip' />
                    </a>
                </div>
            </div>


            <div class="col-7 d-flex justify-content-end">
                <div class="form-group mr-2">
                    <x-adminlte-select label="Mode: " name="mode" id="mode" class="float-right">
                        <option value='NULL'>Select Mode</option>
                        @foreach ($mode as $value)
                            <option value="{{ $value->mode }} ">{{ $value->mode }}</option>
                        @endforeach
                    </x-adminlte-select>
                    <p class="vmode" id="vmode"></p>
                </div>
                <div class="form-group bag_no mr-2">
                    <x-adminlte-input label="Bag No.:" name="bag_no" id="bag_no" placeholder="Bag No.">

                    </x-adminlte-input>
                </div>
                <div class="form-group">
                    <label>Invoice Date:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control float-right datepicker" name='invoice_date'
                            placeholder="Select Date Range" autocomplete="off" id="invoice_date">
                        <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="search"
                            class="btn-sm ml-2" />
                        <x-adminlte-button label="Download Selected" id="selected-download" theme="primary"
                            icon="fas fa-download" class="btn-sm ml-2" />
                        <x-adminlte-button label="Print Selected" id='select_print' theme="primary" icon="fas fa-print"
                            class="btn-sm ml-2" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="invoice_download_zip">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Download Invoice</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body invoice_download">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="showTable" class="">
        <table class='table table-bordered  yajra-datatable table-striped text-center'>
            <thead>
                <tr class='text-bold bg-info'>
                    <th>Select All <input type='checkbox' id='selectAll'></th>
                    <th>Invoice No.</th>
                    <th>Invoice Date</th>
                    <th>Mode</th>
                    <th>Channel</th>
                    <th>Shipped By</th>
                    <th>AWB No.</th>
                    <th>Store Name</th>
                    <th>Bill To Name</th>
                    <th>Ship To Name</th>
                    <th>SKU</th>
                    <th>QTY</th>
                    <th>Price</th>
                    <th class='text-center'>Action</th>
                </tr>
            </thead>
            <tbody id='checkTable'>
            </tbody>
        </table>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            //start search invoice
            // $('#zip-download').hide();
            $("#Searchbox").on('keyup', function() {
                let self = $(this);
                let invoice_no = $.trim(self.val());
                let invoice_no_re = invoice_no.replaceAll(/-/g, '_');
                let tr = $("." + invoice_no_re);
                let table = $("#checkTable");

                $(tr.children().children()[0]).prop('checked', true);
                $(tr).addClass('bg-warning');
                tr.prependTo(table);
            });
            //end search invoice

            // $('#showTable').css("display", "none");
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

            $('#mode').on('change', function() {
                if ($('#mode').val() != 'NULL') {
                    var id = document.getElementById('mode');
                    id.style = ' none';
                    document.getElementById('vmode').innerHTML = '';
                }
            });

            $('#search').click(function() {

                if ($('#mode').val() == 'NULL') {

                    var id = document.getElementById('mode');
                    id.style = 'border: 2px solid red';
                    let text = 'Mode must be filled out';
                    document.getElementById('vmode').innerHTML = text;
                    document.getElementById('vmode').style.color = 'red';

                } else {

                    let bag_no = $('#bag_no').val();
                    let invoice_mode = $('#mode').val();
                    let invoice_date = $('#invoice_date').val();

                    let yajra_table = $('.yajra-datatable').DataTable({

                        destroy: true,
                        processing: true,
                        serverSide: true,
                        pageLength: 40,
                        lengthMenu: [10, 20, 30, 40],
                        ajax: {
                            url: "{{ url('invoice/manage') }}",
                            type: 'get',
                            data: function(d) {
                                d.invoice_mode = invoice_mode;
                                d.bag_no = bag_no;
                                d.invoice_date = invoice_date;
                            },
                        },
                        columns: [{
                                data: 'select_all',
                                name: 'select_all',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'invoice_no',
                                name: 'invoice_no'
                            },
                            {
                                data: 'invoice_date',
                                name: 'invoice_date'
                            },
                            {
                                data: 'mode',
                                name: 'mode'
                            },
                            {
                                data: 'channel',
                                name: 'channel'
                            },
                            {
                                data: 'shipped_by',
                                name: 'shipped_by'
                            },
                            {
                                data: 'awb_no',
                                name: 'awb_no'
                            },
                            {
                                data: 'store_name',
                                name: 'store_name',
                            },
                            {
                                data: 'bill_to_name',
                                name: 'bill_to_name',
                            },
                            {
                                data: 'ship_to_name',
                                name: 'ship_to_name',
                            },
                            {
                                data: 'sku',
                                name: 'sku',
                            },
                            {
                                data: 'qty',
                                name: 'qty',
                            },
                            {
                                data: 'product_price',
                                name: 'product_price',
                            },
                            {
                                data: 'action',
                                name: 'action',
                            },
                        ],
                    });
                }
            });


            $('#selected-download').click(function() {

                alert('Invoice is downloading please wait.');

                let invoice_mode = $('#mode').val();
                let invoice_date = $('#invoice_date').val();
                var url = $(location).attr('href');
                let current_page_number = $(".check_options:first").data('current-page');
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
                $.ajax({
                    method: 'POST',
                    url: "{{ url('/invoice/select-download') }}",
                    data: {
                        'id': id,
                        "invoice_date": invoice_date,
                        "invoice_mode": invoice_mode,
                        'current_page_no': current_page_number,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        // arr += response;
                        // window.location.href = '/invoice/zip-download/' + arr;
                        // alert('Export pdf successfully');
                    },
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
                    // window.location.href = '/invoice/selected-print/' + id;

                });
                window.open("/invoice/selected-print/" + id, "_blank");
            });

        });
        $('#selectAll').change(function() {

            if ($('#selectAll').is(':checked')) {
                $('.check_options').prop('checked', true);
            } else {
                $('.check_options').prop('checked', false);
            }
        });
        $("input[name='options[]']").on('change', function() {

            let input_checkbox = $("input[name='options[]'] ").length;
            let total_input_checkbox = $("input[name='options[]']:checked").length;
            alert(input_checkbox);
            alert(total_input_checkbox);
            if (input_checkbox === total_input_checkbox) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });

        $('#zip-download').on('click', function() {

            $('.invoice_download').empty();
            $.ajax({
                method: 'post',
                url: "{{ route('invoice.zip.download') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(result) {
                    $('.invoice_download').append(result);
                    //
                }
            });
        });
    </script>
@stop
