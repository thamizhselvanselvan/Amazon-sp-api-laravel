@extends('adminlte::page')
@section('title', 'Label')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row mb-4">
    <div class="col-1">
        <a href="{{ route('label.manage') }}">
            <x-adminlte-button label='Back' class="btn-sm" theme="primary" icon="fas fa-long-arrow-alt-left" type="submit" />
        </a>
    </div>
    <div class="col">
        <h1 class=" text-dark text-center">Label Search By</h1>
    </div>
</div>

<div class="row mt-4">
    <div class="col-2 ">
        <input type="radio" class="order_id radio_button" name="priority" value="Amazon Order Id" checked />
        <label for="P3">Amazon Order Id</label>
    </div>
    <div class="col-2 ">
        <input type="radio" class="tracking_id radio_button" name="priority" value="Outward Awb No">
        <label for="P3">Outward AWB No.</label>
    </div>
    <div class="col-2 ">
        <input type="radio" class="inward_tracking_id radio_button" name="priority" value="Inward Awb No">
        <label for="P3">Inward AWB No.</label>
    </div>
</div>

@stop
@section('content')
@csrf

<div class="row ">
    <div class="col search_by_order_id">
        <label class="txt_label">Amazon Order Id</label>
        <textarea class="form-control search_id" rows="4" placeholder="Amazon Order Id" name="order_id" id='order_id'></textarea>
        <div class="text-right m-2">
            <x-adminlte-button label='Search' class="btn-sm search-amazon-order-id" theme="primary" icon="fas fa-file-upload" type="submit" onclick="search()" />
            <x-adminlte-button label="Print Selected" target="_blank" id='print_selected' theme="primary" icon="fas fa-print" class="btn-sm ml-2 d-none" />
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
                <th>Courier Name</th>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id='checkTable'>
        </tbody>
    </table>
</div>

<div id="showTableMissing" class="d-none">
    <table class='table table-bordered table-striped text-center'>
        <thead>
            <tr class='text-bold bg-info'>
                <th>Order No.</th>
                <th>AWB No.</th>
                <th>Courier Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id='checkTableMissing'>
        </tbody>
    </table>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {

        $(document).on('click', '.radio_button', function() {
            $value = $(this).val();
            $('.txt_label').html($value);
            $('.search_id').val('');
            $(".search_id").attr("placeholder", $value).blur();
            $('#checkTable').html('');
            $('#showTable').addClass('d-none');
            $('#print_selected').addClass('d-none');
            $('#showTableMissing').addClass('d-none');
        });

        $(document).on('click', '.update', function() {

            let order_id = $(this).attr("ID");
            let tracking_id = $('#tracking' + order_id).val();
            let courier = $('#courier' + order_id).val();

            $.ajax({
                method: 'POST',
                url: "{{ route('lable.update.tracking-details') }}",
                data: {
                    'order_id': order_id,
                    'tracking_id': tracking_id,
                    'courier': courier,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    $('.search-amazon-order-id').click();
                }
            });
        });
    });

    $('#order_id').on('keyup', function(e) {
        var form_data = $('.search_id').val();
        if (form_data.length == 19 && !e.ctrlKey) {

            $('.search-amazon-order-id').click();
        }
    });

    function search() {

        $('#checkTable').html('');
        $('#showTable').addClass('d-none');
        $('#showTableMissing').addClass('d-none');
        $('#print_selected').addClass('d-none');

        let awb_no = $('.search_id').val();
        let value = $('input[name=priority]:checked').val();
        if (awb_no == '') {
            alert('Please Enter ' + value);
        } else {
            $.ajax({
                method: 'POST',
                url: "{{ route('lable.search.amazon-order-id') }}",
                data: {
                    data_type: value,
                    value: awb_no,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.success) {

                        $('#showTable').removeClass('d-none');
                        $('#checkTable').html(response.success);
                        $('#print_selected').removeClass('d-none');
                        let view = $('.view').attr('href');
                        window.open(view, '_blank');
                    } else {
                        $('#showTable').removeClass('d-none');
                        let no_data = "<tr><td colspan='7'>No data available in table</td> </tr>";
                        $('#checkTable').html(no_data);
                    }
                    if (response.missing) {
                        $('#showTableMissing').removeClass('d-none');
                        $('#checkTableMissing').html(response.missing);
                    }
                }
            });
        }
    }

    $('#checkTable').on('click', '#search_awb', function() {
        var order_item_identifier = $(this).data('order_item_identifier');
        var amazon_order_identifier = $(this).data('amazon_order_identifier');

        console.log(order_item_identifier)
        console.log(amazon_order_identifier)

        loadOrderAddressFormFunction(order_item_identifier, amazon_order_identifier);

        $('#danger').hide();
        $('#success').hide();

    });

    $('#selectAll').change(function() {
        if ($('#selectAll').is(':checked')) {

            $('.check_options').prop('checked', true);
        } else {
            $('.check_options').prop('checked', false);

        }
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
            // window.location.href = '/label/print-selected/' + id;
        });
        // alert(id);
        window.open("/label/print-selected/" + id, "_blank");
    });
</script>
@include('label.edit_label_details_master')
@stop