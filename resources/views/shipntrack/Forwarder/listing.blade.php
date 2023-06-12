@extends('adminlte::page')

@section('title', 'Booking Details')

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
    <div class="col-2">
        <div style="margin-top: -1.0rem;">

            <x-adminlte-select name="destination" label="Source-Destination" id="destination">
                <option value="0">Source-Destination</option>
                @foreach ($destinations as $destination)
                <option value={{ $destination['id'] }}_{{ $destination['destination'] }}_{{ $destination['process_id'] }}>
                    {{ $destination['source'] . '-' . $destination['destination'] }}
                </option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>
    <div class="col-3">
    </div>
    <div class="col-3">
        <h1 class="m-0 text-dark">Booking Details</h1>
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
        </div>
        </h2>

        <table class="table table-bordered yajra-datatable table-striped d-none" id="table">
            <thead>
                <tr class="table-info">
                    <!-- <th>ID</th> -->
                    <th>AwB Number</th>
                    <th>Reference ID </th>
                    <th>Consignor</th>
                    <th>Consignee</th>
                    <th>Forwarder 1 </th>
                    <th>Forwarder 1 AWB</th>
                    <!-- <th>Forwarder 1 Flag</th> -->
                    <th>Forwarder 2 </th>
                    <th>Forwarder 2 AWB</th>
                    <!-- <th>Forwarder 2 Flag</th> -->
                    <th>Forwarder 3 </th>
                    <th>Forwarder 3 AWB</th>
                    <!-- <th>Forwarder 3 Flag</th> -->
                    <th>Forwarder 4 </th>
                    <th>Forwarder 4 AWB</th>
                    <!-- <th>Forwarder 4 Flag</th> -->
                    <th>Status</th>

                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')

<script type="text/javascript">
    $.extend($.fn.dataTable.defaults, {
        pageLength: 100,
    });

    $.extend($.fn.dataTable.defaults, {
        pageLength: 100,
        // orderable: false,
        // searchable: false
    });


    $('#select_mode').on('change', function(d) {

        $('#table').dataTable().fnDestroy();

        let mode = $(this).val();
        $(".table").removeClass("d-none")

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            lengthChange: false,
            stateSave: true,
            // searching: false,
            ajax: {
                url: "{{ route('shipntrack.forwarder.mapped.details') }}",
                type: 'get',
                headers: {
                    'content-type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.mode = $('#select_mode').val();

                },
            },
            columns: [
                // {
                //     data: 'id',
                //     name: 'id'
                // },
                {
                    data: 'awb_number',
                    name: 'awb_number',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'reference_id',
                    name: 'reference_id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'consignor',
                    name: 'consignor'
                },
                {
                    data: 'consignee',
                    name: 'consignee'
                },
                {
                    data: 'forwarder_1',
                    name: 'forwarder_1',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'forwarder_1_awb',
                    name: 'forwarder_1_awb'
                },
                // {
                // data: 'forwarder_1_flag',
                // name: 'forwarder_1_flag',
                // orderable: false,
                // searchable: false
                // },
                {
                    data: 'forwarder_2',
                    name: 'forwarder_2',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'forwarder_2_awb',
                    name: 'forwarder_2_awb'
                },
                // {
                // data: 'forwarder_2_flag',
                // name: 'forwarder_2_flag',
                // orderable: false,
                // searchable: false
                // },
                {
                    data: 'forwarder_3',
                    name: 'forwarder_3',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'forwarder_3_awb',
                    name: 'forwarder_3_awb'
                },
                // {
                // data: 'forwarder_3_flag',
                // name: 'forwarder_3_flag',
                // orderable: false,
                // searchable: false
                // },
                {
                    data: 'forwarder_4',
                    name: 'forwarder_4',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'forwarder_4_awb',
                    name: 'forwarder_4_awb'
                },
                // {
                //     data: 'forwarder_4_flag',
                //     name: 'forwarder_4_flag',
                //     orderable: false,
                //     searchable: false
                // },
                {
                    data: 'status',
                    name: 'status',
                },

            ]
        });
    });
</script>
@stop