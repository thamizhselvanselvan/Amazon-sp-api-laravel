@extends('adminlte::page')
@section('title', 'Store List')

@section('content_header')
<div class='row'>
    <h1 class="m-0 text-dark col">Select Store</h1>
    <h2 class="mb-4 text-right col">
        <!-- <a href="/orders/list">
                                                                                                                                                                                                                                                                                        <x-adminlte-button label="Back" theme="primary" icon="fas fa-arrow-alt-circle-left" />
                                                                                                                                                                                                                                                                                    </a> -->
        <x-adminlte-button label="Save Store" id='select_store' theme="primary" icon="fas fa-check-circle" />
    </h2>
</div>
@stop

@section('css')
<style>
    .table td {
        padding: 0.1rem;
    }
</style>
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

        <h2 class="mb-4">
        </h2>
        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Store Name</th>
                    <th>Region</th>
                    <th>Order</th>
                    <th>Order Item</th>
                    <th>ShipNTrack</th>
                    <th>Zoho|Courier Booking|AWB Upload</th>
                    <th>BuyBox Stores</th>
                    <th>Courier Partner</th>
                    <th>Source</th>
                    <th>Destination</th>
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
    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('admin/stores') }}",
        pageLength: 50,
        lengthMenu: [10, 50, 100, 500],
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'store_name',
                name: 'store_name'
            },
            {
                data: 'region',
                name: 'region'
            },
            {
                data: 'order',
                name: 'order',
                orderable: false,
                searchable: false
            },
            {
                data: 'order_item',
                name: 'order_item',
                orderable: false,
                searchable: false
            },
            {
                data: 'enable_snt',
                name: 'enable_snt',
                orderable: false,
                searchable: false
            },
            {
                data: 'zoho',
                name: 'zoho',
                orderable: false,
                searchable: false
            },
            {
                data: 'buybox_stores',
                name: 'buybox_stores',
                orderable: false,
                searchable: false
            },
            {
                data: 'partner',
                name: 'partner',
                orderable: false,
                searchable: false
            },
            {
                data: 'source',
                name: 'source',
                orderable: false,
                searchable: false
            },
            {
                data: 'destination',
                name: 'destination',
                orderable: false,
                searchable: false
            },
        ],
        "initComplete": function(settings, json) {

            $(".order").each(function() {
                let self = $(this);

                if (self.is(":checked")) {
                    self.parent().parent().next().find('.order_item').prop('disabled', false);
                    self.parent().parent().next().next().find('.shipntrack').prop('disabled',
                        false);
                    self.parent().parent().next().next().next().find('.zoho').prop('disabled',
                        false);
                }
            });

            $('.order').on("click", function() {
                let self = $(this);
                let bool = true;

                if (self.is(":checked")) {
                    bool = false;
                }

                self.parent().parent().next().find('.order_item').prop('disabled', bool);
                self.parent().parent().next().next().find('.shipntrack').prop('disabled', bool);
                self.parent().parent().next().next().next().find('.zoho').prop('disabled', bool);
            });
        }
    });

    $('#select_store').on('click', function() {

        let selected_store = '';
        let order_item = '';
        let order_count = 0;
        let shipntrack = '';
        let shipntrack_count = 0;
        let zoho_enable_count = 0;
        let zoho_enable = '';

        let bb_store_enable_count = 0;
        let bb_store_enable = '';

        let count = 0;
        let courier_count = 0;
        let courier = '';
        let desti_count = 0;
        let destination = '';
        let source_count = 0;
        let source = '';

        $("input[name='options[]']:checked").each(function() {
            if (count == 0) {

                selected_store += $(this).val();
            } else {
                selected_store += '-' + $(this).val();
            }
            count++;
        });

        $("input[name='orderItem[]']:checked").each(function() {

            if (order_count == 0) {

                order_item += $(this).val();
            } else {
                order_item += '-' + $(this).val();
            }
            order_count++;
        });
        $("input[name='shipntrack[]']:checked").each(function() {

            if (shipntrack_count == 0) {

                shipntrack += $(this).val();
            } else {
                shipntrack += '-' + $(this).val();
            }
            shipntrack_count++;
        });
        $("input[name='zoho[]']:checked").each(function() {

            if (zoho_enable_count == 0) {

                zoho_enable += $(this).val();
            } else {
                zoho_enable += '-' + $(this).val();
            }
            zoho_enable_count++;
        });

        $("input[name='bb_store[]']:checked").each(function() {

            if (bb_store_enable_count == 0) {

                bb_store_enable += $(this).val();
            } else {
                bb_store_enable += '-' + $(this).val();
            }
            bb_store_enable_count++;
        });






        courier = '';
        $(".courier_class option:selected").each(function() {
            if ($(this).val() != 'NULL') {
                if (courier_count == 0) {
                    courier += $(this).val();
                } else {
                    courier += '-' + $(this).val();
                }
                courier_count++;
            }
        });

        source = '';
        $(".source option:selected").each(function() {
            if ($(this).val() != 'NULL') {
                if (source_count == 0) {
                    source += $(this).val();
                } else {
                    source += '-' + $(this).val();
                }
                source_count++;
            }
        });

        destination = '';
        $(".destination option:selected").each(function() {
            if ($(this).val() != 'NULL') {
                if (desti_count == 0) {
                    destination += $(this).val();
                } else {
                    destination += '-' + $(this).val();
                }
                desti_count++;
            }
        });

        // if (selected_store == '') {
        //     alert('Please Select Store');
        //     return false;
        // }

        $.ajax({
            method: 'post',
            url: '/admin/update-store',
            data: {
                "_token": "{{ csrf_token() }}",
                "_method": 'post',
                'selected_store': selected_store,
                'order_item': order_item,
                'shipntrack': shipntrack,
                'zoho_enable': zoho_enable,
                'bb_store_enable': bb_store_enable,
                'courier_partner': courier,
                'source': source,
                'destination': destination
            },
            success: function(response) {

                alert(response.success);
                window.location = '/admin/stores';
            }
        })
    });
</script>

@stop