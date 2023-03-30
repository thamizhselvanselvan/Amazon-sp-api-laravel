@extends('adminlte::page')

@section('title', 'Status Master')

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

    <div class="col-1.5">
        <h1 class="m-0 text-dark">Select Courier :</h1>
    </div>
    <div class="col-2">
        <div style="margin-top: -1.6rem;">
            <x-adminlte-select name="courier_select" id="courier_select" label="">
                <option value="0">Select Courier</option>
                @foreach($courier_partner as $Courier)
                <option value="{{$Courier->id}}" {{ $request_courier_id == $Courier->id ? "selected" : '' }}>{{$Courier->courier_name}}</option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>
    <div class="col text-right m-3">
        <div style="margin-top: -0.8rem;">
            <h1 class="m-0 text-dark">Courier Status Master</h1>
        </div>
    </div>
    <div class="col text-right m-3 ">
        <div style="margin-top: -0.8rem;">
            <x-adminlte-button label='Save' class="save_btn" theme="primary" icon="fas fa-file-upload" id='update' />
        </div>
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
            @if (request('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{request('success')}}</strong>
            </div>
            @endif
        </div>
        </h2>

        <table class="table table-bordered yajra-datatable table-striped" id="report_table">
            <thead>
                <tr class="table-info">
                    <th>ID</th>
                    <th>Courier Partner</th>
                    <th>Courier Status</th>
                    <th>Status</th>
                    <th>Stop Tracking</th>
                    <th>Stop-API-Display</th>
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
    $(function() {
        $.extend($.fn.dataTable.defaults, {
            pageLength: 100,
        });


        $('#courier_select').on('change', function() {
            window.location = "/shipntrack/status/manager/" + $(this).val();
        });


        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            lengthChange: false,
            stateSave: true,
            // searching: false,
            ajax: {
                url: "{{ url($url) }}",
                type: 'get',
                headers: {
                    'content-type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    courier_id = $('#courier_select').val();
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'courier_id',
                    name: 'courier_id'
                },
                {
                    data: 'courier_status',
                    name: 'courier_status'
                },
                {
                    data: 'booking_master_id',
                    name: 'booking_master_id'
                },
                {
                    data: 'tracking_stop',
                    name: 'tracking_stop'
                },
                {
                    data: 'api_stop',
                    name: 'api_stop'
                },
            ]
        });


        $('#update').on('click', function() {



            var checkboxValues = [];
            var book_stats = [];
            let obj = {};
            let courier_id = $("#courier_select").val();
            if (courier_id == 0) {
                alert('please Select The Courier and Update The Status');
                return false;
            }
            //Stop Tracking
            let stop_enable_count = 0;
            let stop_enable = '';
            $("input[name='stats_store[]']:checked").each(function() {
                if (stop_enable_count == 0) {
                    stop_enable += $(this).val();
                } else {
                    stop_enable += '-' + $(this).val();
                }
                stop_enable_count++;
            });

            //stop api Checkbox
            let stop_api_count = 0;
            let stop_api_enable = '';
            $("input[name='api_stop[]']:checked").each(function() {
                if (stop_api_count == 0) {
                    stop_api_enable += $(this).val();
                } else {
                    stop_api_enable += '-' + $(this).val();
                }
                stop_api_count++;
            });


            let self = $(this);
            let table = $("#report_table tbody tr");
            let data = '';
            table.each(function(index, elm) {

                let cnt = 0;
                let td = $(this).find('td');

                data += '|' + ('status[]', $(td[3]).find('select').val());

            });


            // let self = $(this);
            // let selectOption = [];
            // selectOption = $(self.children("select")).children("option:selected").text();

            // let booking_status = $(self.children("select")).children("option:selected").val();
            // book_stats.push(booking_status);

            // obj[self.val()] = booking_status;
            // $('input[type=checkbox]:checked').each(function() {
            //     let self = $(this);
            //     let selectOption = [];
            //     // selectOption = $(self.parent().prev().children("select")).children("option:selected").text();
            //     checkboxValues.push(self.val());
            //     let booking_status = $(self.parent().prev().children("select")).children("option:selected").val();
            //     book_stats.push(booking_status);

            //     obj[self.val()] = booking_status;
            // });

            $.ajax({
                url: "{{route('shipntrack.courier.status.store')}}",
                method: "get",
                data: {
                    "status": data,
                    "stop_enable": stop_enable,
                    "stop_api": stop_api_enable,
                    "courier_id": courier_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    console.log(response);
                    alert('Courier Status Updated Successfully');
                    window.location.reload();
                    // window.location.href = '/shipntrack/status/manager?success=Status has been Updated successfully'
                },
                error: function(result) {
                    alert('error');
                }
            });

        });
    });
</script>
@stop