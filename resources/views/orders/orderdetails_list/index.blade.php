@extends('adminlte::page')

@section('title', 'Order Search')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    #report_table {
        font-size: large;
        padding: 20px;
        text-align: left;
    }


    .bordercss {
        border: 1px green;
    }
</style>
@stop
@section('content_header')
<h1 class="m-0 text-dark">Search Order</h1>
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
    <div class="alert_display">
        @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-2">
        <div class="form-group">
            <label>Enter OrderID's:</label>
            <div class="autocomplete" style="width:400px;">
                <textarea name="upload_orders" rows="5" placeholder="Enter OrderID's here..." type=" text" autocomplete="off" class="form-control up_order"></textarea>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-0.5">
        <x-adminlte-button label="Search" theme="primary" id="ord_search" icon=" fas fa-search" type="button" />
    </div>
    <div class="col-2">
        <a href="/orders/csv/import">
            <x-adminlte-button label="Order Import" theme="info" icon="fas fa-file-upload" type="button" />
        </a>
    </div>
</div>

<div class="row">
    <div class="col-2">
        <h4></h4>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12">
        <table class="table table-bordered yajra-datatable table-striped" id="report_table">
            <thead>
                <tr class="bordercss">
                    <th>Amazon Order Identifier</th>
                    <th>Order Item Identifier</th>
                    <th>Marketplace Identifier:</th>
                    <th>ASIN</th>
                    <th>Store Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="report_table_body">
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script type="text/javascript">
    $("#report_table").hide();
    $("#ord_search").on('click', function(e) {
        let order_ids = $('.up_order').val();
        if (order_ids == "") {
            alert("Enter OrderID");
            return false;
        }
       $('#report_table_body').empty();
        $("#report_table").show();
        $.ajax({
            method: 'POST',
            url: '/orders/bulk/search',
            data: {
                'orderid': order_ids,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                // console.log(response.data);

                let html = '';
                $.each(response.data, function(index, alldata) {
                    $.each(alldata, function(index, value) {
                        html += "<tr class='table_row'>";
                        html += "<td>&ensp;&nbsp;" + value.amazon_order_identifier + "</td>";
                        html += "<td>&ensp;&nbsp;" + value.order_item_identifier + "</td>";
                        html += "<td>&ensp;&nbsp;" + value.marketplace_identifier + "</td>";
                        html += "<td>&ensp;&nbsp;" + value.asin + "</td>";
                        html += "<td>&ensp;&nbsp;" + value.store_name + "</td>";
                        html += '<td> <a href="/orders/bulk/edit/' + value.order_item_identifier + '" target="_blank"><button type="button" class="btn btn-info btn-sm">View Or Edit</button></a></td>';
                        html += "</tr>";
                        // markup = "<tr><td><b>Amazon Order Identifier &nbsp;=&nbsp;</b>" +
                        //     value.amazon_order_identifier +
                        //     "</td>+<td><b>Order Item Identifier &nbsp;=&nbsp;</b>" +
                        //     value.order_item_identifier +
                        //     "</td>+<td><b>ASIN &nbsp;=&nbsp;</b>" +
                        //     value.asin + "</td>" +
                        //     "</td>+<td><b>Store Name &nbsp;=&nbsp;</b>" +
                        //     value.store_name + "</td>" +
                        //     '<td> <a href="/orders/bulk/edit/' + value.order_item_identifier + '" target="_blank"><button type="button" class="btn btn-info btn-sm">View Or Edit</button></a></td>' +
                        //     "</tr>";
                        // tableBody = $("table tbody");
                        // tableBody.append(markup);
                    });
                    $("#report_table").append(html);
                });
            },
            error: function(response) {
                alert('Something Went Wrong');
            }
        });
    });
</script>
@stop