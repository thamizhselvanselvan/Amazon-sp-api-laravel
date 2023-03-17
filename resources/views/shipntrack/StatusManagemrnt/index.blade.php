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
    <div class="col text-left">
        <h1 class="m-0 text-dark">Courier Status Master</h1>
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
        </div>
        </h2>

        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr class="table-info">
                    <th>ID</th>
                    <th>Courier Partner</th>
                    <th>Status</th>
                    <th>Booking Status</th>
                    <th>Stop</th>
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

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('status.master.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'courier_partner_id',
                    name: 'courier_partner_id'
                },
                {
                    data: 'courier_partner_status',
                    name: 'courier_partner_status'
                },
                {
                    data: 'booking_master_id',
                    name: 'booking_master_id'
                },
                {
                    data: 'action',
                    name: 'action'
                },

            ]
        });

        $('#update').on('click', function() {
            var checkboxValues = [];
            var book_stats = [];
            let obj = {};

            $('input[type=checkbox]:checked').each(function() {
                let self = $(this);
                let selectOption = [];
                // selectOption = $(self.parent().prev().children("select")).children("option:selected").text();
                checkboxValues.push(self.val());
                let booking_status = $(self.parent().prev().children("select")).children("option:selected").val();
                book_stats.push(booking_status);

                obj[self.val()] = booking_status;
            });

            $.ajax({
                url: "{{route('shipntrack.courier.status.store')}}",
                method: "get",
                data: {
                    "status": obj,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    alert('Courier Status Updated Successfully');
                },
                error: function(result) {
                    alert('error');
                }
            });
        });
    });
</script>
@stop