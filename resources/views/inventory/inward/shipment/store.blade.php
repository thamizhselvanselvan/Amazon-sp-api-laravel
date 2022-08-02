@extends('adminlte::page')

@section('title', 'Inwardings')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;
    }

    .table th {
        padding: 2;
        padding-left: 5px;
    }
</style>

@stop

@section('content_header')
<div class="row">
    <div class="col-3">
        <h3>Store Inwarding Shipment's</h3>
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
                        <a href="{{ route('shipments.index') }}" class="btn btn-primary btn-sm">
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
                <table class="table table-bordered yajra-datatable table-striped" id="store_table">
                    <thead>
                        <tr>
                            <th>Shipment ID</th>
                            <th>ASIN</th>
                            <th>Item Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Select Rack</th>
                            <th>Select Shelve</th>
                            <th>Select Bin</th>


                        </tr>
                    </thead>
                    <tbody>
    
                        @foreach ($store as $key => $val)

                        <tr class='table_row'>
                            <td name="ship_id[]" id="ship">{{$val['ship_id']}}</td>
                            <td name="asin[]" id="quantity">{{$val['asin']}}</td>
                            <td name="name[]">{{$val['item_name']}}</td>
                            <td name="price[]">{{$val['price']}}</td>
                            <td name="quantity[]" id="quantity">{{$val['quantity']}}</td>

                            <td>
                                <x-adminlte-select name="rack_id" class='rack_id'>
                                    <option value="0">--Select--</option>
                                    @foreach ($rack as $racks)
                                    <option value="{{$racks->rack_id}}">{{$racks->rack_id . '/' .$racks->name}}</option>
                                    
                                    @endforeach
                                </x-adminlte-select>
                            </td>
                            <td class="shelve">
                                <x-adminlte-select name="shelve[]" class='shelve_id'>
                                    <option value=""></option> 

                                </x-adminlte-select>
                            </td>
                            <td>
                                <x-adminlte-select  input type="text"  name="bin[]" class='bin_id'>
                                    <option value="">  </option>

                                </x-adminlte-select>
                            </td>
                            <!-- <td> <input type="text" value="" name="shelve[]" placeholder="Enter shelve" class="form-control"> </td> -->
                            <!-- <td> <input type="text" value="" name="bin[]" placeholder="Enter Bin" class="form-control"> </td> -->
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop


@section('js')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.rack_id').change(function() {
            var id = $(this).val();
            let self = $(this);
            $.ajax({
                url: '/Shelves/' + id,
                method: 'POST',
                data: {
                    'id': id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(result) {
                    self.parent().parent().parent().next().find('.shelve_id').empty();
                    let shelve_data = '<option>--Select--</option>';
                    $.each(result, function(i, result) {
                        shelve_data += "<option value='" + result.shelve_id + "'>" + result.shelve_id + "/" + result.name + "</option>";
                    });
                    self.parent().parent().parent().next().find('.shelve_id').append(shelve_data);
                },
                error: function() {
                    alert('ERROR');
                }
            });
        });

        $('.shelve_id').change(function() {
            var id = $(this).val();
            let self = $(this);
            // alert(id);
            $.ajax({
                url: '/Bins/' + id,
                method: 'POST',
                data: {
                    'id': id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(result) {
            
                    self.parent().parent().parent().next().find('.bin_id').empty();
                    let bin_data = '<option>--Select--</option>';
                    $.each(result, function(i, result) {
                        bin_data += "<option value='" + result.bin_id + "'>"   + result.name + "</option>";
                    });
               
                    self.parent().parent().parent().next().find('.bin_id').append(bin_data);
                },
                error: function() {
                    alert('ERROR');
                }
            });
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
            // console.log($(td[7]).find('.bin_id').val());

            data.append('bin[]', $(td[7]).find('.bin_id').val());
           
        });
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
@stop