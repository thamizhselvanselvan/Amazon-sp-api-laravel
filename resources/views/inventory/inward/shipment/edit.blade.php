@extends('adminlte::page')

@section('title', ' Inward Shipment')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
<style>
    .content-header {
        padding: 15px 15px 0 15px;
    }

    .alert {
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 0;
        border: 1px solid transparent;
        border-radius: 0.25rem;
    }
</style>
@stop

@section('content_header')
<div class="row">

    <div class="col-0.5">
        <a href="{{ route('shipments.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
    <div class="col-3">
        <h1 class="m-0 text-dark">Edit Inwarded Shipment</h1>
    </div>

</div>
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>Please Enter The ASIN's of Shipment ID {{$ship_id}}</strong>
</div>
<div class="row">
    <div class="col">
        <div class="alert_display">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
            @if($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('content')

<div class="row">
    <div class="col-2">
        <div class="form-group">
            <label>Enter ASIN's:</label>
            <div class="autocomplete" style="width:400px;">
                <textarea name="upload_orders" rows="5" placeholder="Enter ASIN's here..." type=" text" autocomplete="off" class="form-control up_asins"></textarea>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-0.5">
        <x-adminlte-button label="Search" theme="primary" id="asin_search" icon=" fas fa-search " class="btn-sm" type="button" />
    </div>
    <div class="col-2">
        <a href="#">
            <x-adminlte-button label="ASIN Import" theme="info" icon="fas fa-file-upload" class="btn-sm" type="button" />
        </a>
    </div>
    <div class="col-2 d-none">
        <x-adminlte-button value="{{$ship_id}}" class="ship_id" type="button" />
    </div>
</div>
<div class="row">
    <div class="col-2">
        <h4></h4>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-12">
        <table class="table table-bordered yajra-datatable table-striped d-none" id="report_table">
            <thead>
                <tr class="bordercss  table-info">
                    <th>Shipment ID</th>
                    <th>ASIN</th>
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
    $("#asin_search").on('click', function(e) {
        let asin = $('.up_asins').val();
        let ship_id = $('.ship_id').val();

        if (asin == "") {
            alert("Enter ASIN's");
            return false;
        }
        $('#report_table_body').empty();
        $.ajax({
            method: 'get',
            url: "{{route('inward.shipment.edit')}}",
            data: {

                'ship_id': ship_id,
                'asin': asin,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                console.log(response);


                // let html = '';
                // $.each(response.data, function(index, alldata) {
                //     $.each(alldata, function(index, value) {
                //         html += "<tr class='table_row'>";
                //         html += "<td>&ensp;&nbsp;" + value.amazon_order_identifier + "</td>";
                //         html += "<td>&ensp;&nbsp;" + value.order_item_identifier + "</td>";
                //         html += '<td> <a href="/orders/bulk/edit/' + value.order_item_identifier + '" target="_blank"><button type="button" class="btn btn-info btn-sm">View Or Edit</button></a></td>';
                //         html += "</tr>";

                //     });
                //     $("#report_table").append(html);
                // });
            },
            error: function(response) {
                console.log(response.data);
                alert('Something Went Wrong');
            }
        });
    });
</script>
@stop