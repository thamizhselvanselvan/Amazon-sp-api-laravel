@extends('adminlte::page')
@section('title', 'Search Label')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<!-- Download label zip Modal start-->
<div class="modal" id="label_download_zip">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Download Label</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body label_download">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Download label zip Modal end-->
<div class="row">
    <div class="col">
        <h4>
            <a href="{{ route('label.manage') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
            Label Management
        </h4>
    </div>
    <div class="col">
        <form action="">
            @csrf
            <!-- <label>Bag No.:</label> -->
            <div class="input-group">
                <input type="text" class="form-control float-right" name="bag_no" placeholder="Input Bag No." id="bag_no">
                <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="SearchByBag" class="btn-sm ml-2" />
                <x-adminlte-button label="Create Selected PDF" id='download_selected' theme="primary" icon="fas fa-download" class="btn-sm ml-2" />
                <x-adminlte-button label="Print Selected" target="_blank" id='print_selected' theme="primary" icon="fas fa-print" class="btn-sm ml-2" />
                <x-adminlte-button label="Download Label Zip" theme="primary" icon="fas fa-download" class="btn-md ml-2 btn-sm" id='zip-download' data-toggle="modal" data-target='#label_download_zip' />
            </div>
        </form>
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
                <th>Selected All <input type='checkbox' id='selectAll' /></th>
                <th>Store Name</th>
                <th>Order No.</th>
                <th>Awb No.</th>
                <th>Order Date</th>
                <th>SKU</th>
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

<script type="text/javascript">
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
                // console.log(response);

                if (response == '0000-00-00 00:00:00') {

                    $('#download_selected').prop('disabled', true);
                    $('#download_selected').attr("title", "File is downloading...");
                }

            },
        });
        // begin search label
        $('#Searchbox').on('keyup', function() {

            let order_no = $.trim($(this).val());
            let order_no_replace = order_no.replaceAll(/-/g, '_');
            let tr = $('.' + order_no_replace);
            let table = $('#checkTable');

            $(tr.children().children()[0]).prop('checked', true);
            $(tr).addClass('bg-warning');
            tr.prependTo(table);
        });
        // end search label

        function isJsonString(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

        $('#SearchByDate').click(function() {
            if (($('#bag_no').val() == '')) {
                alert('Please Input Bag No.');
            } else {

                $('#showTable').removeClass('d-none');
                let label_date = $('#bag_no').val();
                // alert(label_date);
                $.ajax({
                    method: 'POST',
                    url: "{{ url('/label/select-label') }}",
                    data: {
                        "bag_no": label_date,
                        "_token": "{{ csrf_token() }}",
                    },
                    response: 'json',
                    success: function(response) {
                        // console.log(response);
                        let table = '';

                        $.each(response, function(i, response) {
                            // alert(response);
                            let label_id = response.order_no.replaceAll(/-/g, '_');
                            let change_date = moment(response.purchase_date,
                                    'YYYY-MM-DD ')
                                .format('YYYY-MM-DD');

                            table += "<tr class='" + label_id + "'>";

                            let t = isJsonString(response.shipping_address) ? JSON
                                .parse(response.shipping_address) : null;
                            let t_name = (t) ? t['Name'] : '';
                            if (t_name != '') {

                                table +=
                                    "<td><input class='check_options' type='checkbox' value=" +
                                    response.id + " name='options[]' id='checkid" +
                                    response
                                    .id + "'></td>";
                            } else {

                                table += "<td>  </td>"
                            }

                            table += "<td>" + response.store_name + "</td><td>" +
                                response
                                .order_no + "</td>";

                            table += "<td>" + response.awb_no + "</td><td>" +
                                change_date + "</td><td>" + response.seller_sku +
                                "</td><td>" + t_name + "</td>";
                            if (t_name != '') {
                                table +=
                                    "<td><div class='d-flex'><a href=/label/pdf-template/" +
                                    response.id +
                                    " class='edit btn btn-success btn-sm' target='_blank'><i class='fas fa-eye'></i> View </a><div class='d-flex pl-2'><a href=/label/download-direct/" +
                                    response.id +
                                    "  class='edit btn btn-info btn-sm'><i class='fas fa-download'></i> Download </a>";

                                table +=
                                    "<div class='d-flex pl-2'><a id='edit-address' data-toggle='modal' data-id=" +
                                    response.order_item_identifier +
                                    " data-amazon_order_identifier=" + response
                                    .order_no +
                                    " href='javascript:void(0)' class='edit btn btn-secondary btn-sm'><i class='fas fa-address-card'></i> Address </a></td></tr>"


                            } else {

                                table += "<td> ";
                                table +=
                                    "<div class='d-flex'><a id='edit-address' data-toggle='modal' data-id=" +
                                    response.order_item_identifier +
                                    " data-amazon_order_identifier=" + response
                                    .order_no +
                                    " href='javascript:void(0)' class='edit btn btn-secondary btn-sm '><i class='fas fa-address-card'></i> Address </a></div>"
                                table += "</td></tr>";
                            }
                        });
                        $('#checkTable').html(table);
                        // alert('Export pdf successfully');
                    }
                });
            }
            // <td>Invoice No.</td><td>Invoice Date</td><td>Channel</td><td>Shipped By</td><td>Awb No</td><td>Arn NO.</td><td>Hsn Code</td><td>Quantity</td><td>Product Price</td><td class='text-center'>Action</td></tr></thead><tbody>
        });

        $('#selectAll').change(function() {
            if ($('#selectAll').is(':checked')) {

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
                // window.location.href = '/label/print-selected/' + id;
            });
            // alert(id);
            window.open("/label/print-selected/" + id, "_blank");
        });

        $('#download_selected').click(function() {
            let id = '';
            let count = 0;
            let arr = '';
            let bag_no = $('#bag_no').val();
            let current_page_number = $(".check_options:first").data('current-page');

            if (bag_no == '') {
                alert('Please Input Bag No.');
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
                url: "{{ url('/label/select-download') }}",
                data: {
                    'id': id,
                    'bag_no': bag_no,
                    'current_page_number': current_page_number,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                }
            });
        });
    });

    //Data Table
    $('#SearchByBag').on('click', function() {
        if (($('#bag_no').val() == '')) {
            alert('Please Input Bag No.');
        } else {
            let bag_no = $('#bag_no').val();
            let yajra_table = $('.yajra-datatable').DataTable({
                // dom: '<"top"p s>',
                // dom: '<"top"p>rt<"bottom"flp><"clear">',
                destroy: true,
                processing: true,
                serverSide: true,
                pageLength: 40,
                lengthMenu: [10, 20, 30, 40],
                ajax: {
                    url: "{{ url('label/search-label') }}",
                    type: 'get',
                    data: function(d) {
                        d.bag_no = bag_no;
                    },
                },
                columns: [{
                        data: 'select_all',
                        name: 'select_all',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'store_name',
                        name: 'store_name'
                    },
                    {
                        data: 'order_no',
                        name: 'order_no'
                    },
                    {
                        data: 'awb_no',
                        name: 'awb_no'
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date'
                    },
                    {
                        data: 'seller_sku',
                        name: 'seller_sku'
                    },
                    {
                        data: 'name',
                        name: 'name'
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
    //Data Table end

    $('#zip-download').on('click', function() {
        $('.label_download').empty();
        $.ajax({
            method: 'post',
            url: "{{ route('label.zip.download') }}",
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(result) {
                $('.label_download').append(result);
                //
            }
        });
    });
</script>

@include('label.edit_label_details_master')
@stop