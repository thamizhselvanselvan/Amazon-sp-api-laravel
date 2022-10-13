@extends('adminlte::page')

@section('title','Inventory Reports')
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
<h1 class="m-0 text-dark"> Weekly Inventory Reports</h1>
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
</div>
<div class="row">
    <div class="col-2">
        <input type="radio" name="size" id="entire">
        <label for=" entire"> Entire Warehouse Report</label>
    </div>
    <div class="col-2">
        <input type="radio" name="size" id="ware">
        <label for="ware"> Warehouse Wise Report</label>
    </div>
    <div class="col-7">
        <input type="radio" name="size" id="tag">
        <label for="tag"> Tag Wise Report</label>
    </div>


    <div class="col-1 justify-content-right">
        <form class="row" action="/export/weekly">
            <h2>
                <div style="margin-top: -1rem;">
                    <x-adminlte-button type="submit" label="Export" theme="primary" icon="fas fa-file-export " id="export" />
                </div>
            </h2>
        </form>
    </div>
</div>
<div class="row" id="warehouse">
    <div class="col-2">
        <x-adminlte-select name="ware_id" label="Select Warehouse" class="war">
            <option value="0">Select Warehouse</option>
            @foreach ($ware_lists as $ware_list)
            <option value="{{ $ware_list->id }}">{{ $ware_list->name }}</option>
            @endforeach
        </x-adminlte-select>

    </div>

    <div class="col-1 justify-content-right">
        <h2>
            <div style="margin-top: 1.8rem;">
                <x-adminlte-button type="submit" label="Export" theme="primary" icon="fas fa-file-export " id="export_ware" />
            </div>
        </h2>
    </div>
</div>

<div class="row" id="tag_select">
    <div class="col-2">
        <x-adminlte-select name="tag_id" label="Select Tag" class="tagss">
            <option value="0">Select Tag</option>
            @foreach ($tag_lists as $tag_list)
            <option value="{{ $tag_list->id }}">{{ $tag_list->name }}</option>
            @endforeach
        </x-adminlte-select>

    </div>

    <div class="col-1 justify-content-right">
        <h2>
            <div style="margin-top: 1.8rem;">
                <x-adminlte-button type="submit" label="Export" theme="primary" icon="fas fa-file-export " id="export_tag" />
            </div>
        </h2>
    </div>
</div>

<table class="table table-bordered yajra-datatable table-striped " id="report_table" width="100%">

    <thead>
        <tr>
            <th id="detail">Date</th>
            <th id="detail">Opening Stock</th>
            <th id="detail">Open Stock Amt.</th>
            <th id="detail">Inwarded</th>
            <th id="detail">Inv.Inwarded Amt.</th>
            <th id="detail"> Outwarded</th>
            <th id="detail">Inv.Outwarded Amt.</th>
            <th id="detail">Closing Stock</th>
            <th id="detail">Closing Stock Amt.</th>
        </tr>
    </thead>

    <tbody id="week_append">

    </tbody>
</table>
@stop
@section('js')

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //entire export

    $(function() {

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            searching: false,
            paging: false,
            bPaginate: false,

            bInfo: false,

            ajax: "{{ route('reports.index') }}",
            columns: [{
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'opeaning_stock',
                    name: 'opeaning_stock'
                },
                {
                    data: 'opeaning_stock_amt',
                    name: 'opeaning_stock_amt'
                },
                {
                    data: 'inwarding',
                    name: 'inwarding'
                },
                {
                    data: 'inward_amt',
                    name: 'inward_amt'
                },
                {
                    data: 'outwarding',
                    name: 'outwarding'
                },
                {
                    data: 'outward_amount',
                    name: 'outward_amount'
                },
                {
                    data: 'closing_stock',
                    name: 'closing_stock'
                },
                {
                    data: 'cls_amount',
                    name: 'cls_amount'
                },
            ]
        });


    });
    //hide and show//
    $(function() {

        $("#warehouse").hide();
        $("#report_table").hide();
        $("#export").hide();
        $("#week_table").hide();
        $("#export_ware").hide();
        $("#export_tag").hide();
        $("#tag_select").hide();

        $("#ware ").on('click', function(e) {
            $("#warehouse").show();
            $("#export").hide();
        });
        $("#warehouse ").on('change', function(e) {
            $("#report_table,#export_ware").show();
            $("#export").hide();

        });
        $("#entire ").on('click', function(e) {
            $("#warehouse,#tag_select").hide();
        });
        $("#ware").on('click', function(e) {
            $("#report_table,#export,#tag_select,#report_table").hide();

        });
        $("#entire ").on('click', function(e) {
            $("#report_table").show();
        });
        $("#entire ").on('change', function(e) {
            $("#export").show();
        });
        $("#tag ").on('click', function(e) {
            $("#warehouse,#report_table").hide();
            $("#tag_select").show();
        });
    });

    //warehouse display//
    $("#warehouse ").on('change', function(e) {

        let ware_id = $('.war').val();
        if (ware_id == 0) {
            alert("Please Select Warehouse And Then Export");
            return false;
        }
        $("#week_table").show();
        $.ajax({
            method: 'GET',
            url: '/export/weekly/display',
            data: {
                'ware_id': ware_id,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                console.log(response)
                let html = '';

                if (response.hasOwnProperty("success")) {

                    $.each(response[0], function(index, value) {
                        console.log(value);



                        html += "<tr>";

                        html += "<td>" + value.date + "</td>";
                        html += "<td>" + value.opeaning_stock + "</td>";
                        html += "<td>" + value.opeaning_amount + "</td>";
                        html += "<td>" + value.inwarding + "</td>";
                        html += "<td>" + value.inw_amount + "</td>";
                        html += "<td>" + value.outwarding + "</td>";
                        html += "<td>" + value.outw_amount + "</td>";
                        html += "<td>" + value.closing_stock + "</td>";
                        html += "<td>" + value.closing_amount + "</td>";
                        html += "</tr>";

                    });
                    $("#week_append").html(html);

                    return true;
                }

                alert("No Data exists");

            },
            error: function(response) {
                alert('error');
                console.log(response);
            }
        });

    });

    //warehouse export//
    $("#export_ware").on('click', function(e) {
        let ware_id = $('.war').val();
        if (ware_id == 0) {
            alert("Please Select Warehouse And Then Export");
            return false;
        }
        $.ajax({
            method: 'GET',
            url: '/export/weekly/warehousewise/',
            data: {
                'ware_id': ware_id,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                window.location.href = '/export/weekly/weekwareexpo/' + ware_id;

            },
            error: function(response) {
                alert('error');
                console.log(response);
            }
        });
    });

    //tag Diplay//
    $("#tag_select ").on('change', function(e) {
        $("#export_tag,#report_table").show();
        let tag_id = $('.tagss').val();

        if (tag_id == 0) {
            alert("Please Select Tag And Then Export");
            return false;
        }
        $("#week_table").show();
        $.ajax({
            method: 'GET',
            url: '/tag/weekly/display',
            data: {
                'tag_id': tag_id,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                let html = '';

                if (response.hasOwnProperty("success")) {

                    $.each(response[0], function(index, value) {
                        console.log(value);
                        html += "<tr>";
                        html += "<td>" + value.date + "</td>";
                        html += "<td>" + value.opeaning_stock + "</td>";
                        html += "<td>" + value.opeaning_amount + "</td>";
                        html += "<td>" + value.inwarding + "</td>";
                        html += "<td>" + value.inw_amount + "</td>";
                        html += "<td>" + value.outwarding + "</td>";
                        html += "<td>" + value.outw_amount + "</td>";
                        html += "<td>" + value.closing_stock + "</td>";
                        html += "<td>" + value.closing_amount + "</td>";
                        html += "</tr>";

                    });
                    $("#week_append").html(html);
                    return true;
                }

                alert("No Data exists");

            },
            error: function(response) {
                alert('error');
                console.log(response);
            }
        });

    });

    //tag Export//
    $("#export_tag").on('click', function(e) {
        let tag_id = $('.tagss').val();
        if (tag_id == 0) {
            alert("Please Select Tag And Then Export");
            return false;
        }
        $.ajax({
            method: 'GET',
            url: "{{Route('inventory.tagswise.weekly.export')}}",
            data: {
                'tag_id': tag_id,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
             window.location.href = '/export/weekly/tags/' + tag_id;

            },
            error: function(response) {
                alert('error');
               
            }
        });
    });
</script>
@stop