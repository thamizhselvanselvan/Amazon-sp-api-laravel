@extends('adminlte::page')

@section('title','Outward Shipment Store')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;
    }

    .table th {

        padding: 2px;
        padding-left: 5px;
    }
</style>
@stop

@section('content_header')
<div class="row">
    <div class="col-3">
        <h3>Outwarding Store</h3>
    </div>
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

            <div class="row">

                <div class="col-0">
                    <div style="margin-top:-1.0rem;">
                        <a href="{{ route('outwardings.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-long-arrow-alt-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="col-3">
                    <div style="margin-top:-1.0rem;">
                        <x-adminlte-button label="Store" class="btn btn-primary btn-sm" theme="primary" name="store_bin" id="store_shipments" icon="fas fa-plus" />
                        </a>
                    </div>
                </div>
            </div>
            <div style="margin-top:1.3rem;">
                <table class="table table-bordered yajra-datatable table-striped" id="reduce_table">
                    <thead>
                        <tr>
                            <th> Shipment ID</th>
                            <th> ASIN</th>
                            <th>Item Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Bin</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $value=json_decode($reduce['items'],true);
                        $value=(count($value)>0) ? $value : [];
                        @endphp

                        @foreach($value as $key => $val)
                        <tr class='table_row'>
                            <td name='ship_id[]' id="ship">{{$reduce['ship_id']}}</td>

                            <td name="asin[]" id="quantity">{{$val['asin']}}</td>
                            <td name="name[]">{{$val['item_name']}}</td>
                            <td name="quantity[]" id="quantity">{{$val['quantity']}}</td>
                            <td name="price[]">{{$val['price']}}</td>

                            <td> <input type="text" value="" name="bin[]" placeholder="Enter Bin" class="form-control"> </td>
                        </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop


<!-- @section('js')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function pullback() {
            window.location.href = '/inventory/shipments'
        }
    $("#store_shipments").on("click", function() {
        let self = $(this);
        let table = $("#store_table tbody tr");

        let data = new FormData();

        table.each(function(index, elm) {

            let cnt = 0;
            let td = $(this).find('td');

            data.append('ship_id[]', td[0].innerText);
            data.append('asin[]', td[1].innerText);
            // data.append('name[]', td[2].innerText);
            // data.append('quantity[]', td[3].innerText);
            data.append('bin[]', td[5].children[0].value);
        });

        // let bin = $('#enter_bin').val();
        // data.append('bin', bin);
        console.log(data);
        $.ajax({
            method: 'POST',
            url: '/shipment/place',
            data: data,
            processData: false,
            contentType: false,
            response: 'json',
            success: function(response) {

                console.log(response);

                if (response.success) {
                    pullback();
                }
            },
            error: function(response) {
                console.log(response);
            }
        });
    });
</script>
@stop -->