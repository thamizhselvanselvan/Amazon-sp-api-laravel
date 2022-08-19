@extends('adminlte::page')
@section('title', 'Search Label')

@section('css')
<link rel="stylesheet" href="/css/styles.css">

@stop
@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Label Management</h1>
    <h2 class="mb-4 text-right col"></h2>
    <label>
        Search:<input type="text" id="Searchbox" placeholder="Search label">
    </label>
</div>
<div class="row">
    <div class="col">
        <a href="{{ route('label.manage') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
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
<div class="container-fluid label-search-box">
    <div class="row">
        <div class="col">
            <a href="zip/download">
                <x-adminlte-button label="Download Label Zip" theme="primary" icon="fas fa-download" class="btn-md ml-1" id='zip-download' />
            </a>
        </div>
        <div class="col">
            <form action="">
                @csrf
                <div class="form-group">
                    <label>Bag No.:</label>
                    <div class="input-group">
                        <!-- <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div> -->

                        <input type="text" class="form-control float-right" name="bag_no" placeholder="Input Bag No." id="bag_no">
                        <!-- <input type="text" class="form-control float-right datepicker" name="label_date" placeholder="Select Date Range" autocomplete="off" id="label_date"> -->
                        <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="SearchByDate" class="btn-sm ml-2" />
                        <x-adminlte-button label="Download Selected" id='download_selected' theme="primary" icon="fas fa-download" class="btn-sm ml-2" />
                        <x-adminlte-button label="Print Selected" target="_blank" id='print_selected' theme="primary" icon="fas fa-print" class="btn-sm ml-2" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="showTable" class="d-none">
    <table class='table table-bordered table-striped text-center'>
        <thead>
            <tr class='text-bold bg-info'>
                <th>Selected All <br><input type='checkbox' id='selectAll' /></th>
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
                    url: "{{ url('/label/select-label')}}",
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

                            let t = isJsonString(response.shipping_address) ? JSON.parse(response.shipping_address) : null;
                            let t_name = (t) ? t['Name'] : '';
                            if (t_name != '') {

                                table +=
                                    "<td><input class='check_options' type='checkbox' value=" +
                                    response.id + " name='options[]' id='checkid" + response
                                    .id + "'></td>";
                            } else {

                                table += "<td>  </td>"
                            }

                            table += "<td>" + response.store_name + "</td><td>" +
                                response
                                .order_no + "</td>";

                            table += "<td>" + response.awb_no + "</td><td>" + change_date + "</td><td>" + response.seller_sku + "</td><td>" + t_name + "</td>";
                            if (t_name != '') {
                                table += "<td><div class='d-flex'><a href=/label/pdf-template/" +
                                    response.id +
                                    " class='edit btn btn-success btn-sm' target='_blank'><i class='fas fa-eye'></i> View </a><div class='d-flex pl-2'><a href=/label/download-direct/" +
                                    response.id +
                                    "  class='edit btn btn-info btn-sm'><i class='fas fa-download'></i> Download </a></td> </tr>";

                            } else {

                                table += "<td> </td></tr>";
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
                window.location.href = '/label/print-selected/' + id;
            });
            // alert(id);
        });

        $('#download_selected').click(function() {
            alert('Label is downloading please wait.');
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
            // alert(id);
            $.ajax({
                method: 'POST',
                url: "{{ url('/label/select-download')}}",
                data: {
                    'id': id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    // arr += response;
                    // window.location.href = '/label/zip-download/' + arr;
                    // alert('Export pdf successfully');
                }
            });
        });

    });
</script>
@stop