@extends('adminlte::page')
@section('title', 'Orders List')

@section('content_header')
<div class='row'>
    <h1 class="m-0 text-dark col">Select Store</h1>
    <h2 class="mb-4 text-right col">
        <a href="/orders/list">
            <x-adminlte-button label="Back" theme="primary" icon="fas fa-arrow-alt-circle-left" />
        </a>
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
            <!-- <a href="">
                <x-adminlte-button label="Orders List" theme="primary" icon="fas fa-file-import" />
            </a>
            <a href="{{route('select.store')}}">
                <x-adminlte-button label="Select Store" theme="primary" icon="fas fa-check-circle" />
            </a> -->
        </h2>
        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Store Name</th>
                    <th>Region</th>
                    <th>Order</th>
                    <th>Order Item</th>
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
        ajax: "{{ url('orders/select-store') }}",
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
            }


        ]
    });

    $('#select_store').on('click', function() {

        let selected_store = '';
        let order_item = '';
        let order_count = 0;
        let count = 0;
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

        $.ajax({
            method: 'post',
            url: '/orders/update-store',
            data: {
                "_token": "{{ csrf_token() }}",
                "_method": 'post',
                'selected_store': selected_store,
                'order_item': order_item,
            },
            success: function(response) {

                alert(response.success);
                window.location = '/orders/list';
            }
        })
    });
    let val;
    $(document).ready(function() {
        let count = 0;
        $("input[name='options[]']:checked").each(function() {
            val = $(this).val();
            $('#orderitem' + val).prop('disabled', false);
        });

        $('.order').change(function() {
            $val = $(this).val();
            if ($('#order' + $val).is(':checked')) {
                $('#orderitem' + $val).prop('disabled', false);
            } else {
                $('#orderitem' + $val).prop('checked', false);
                $('#orderitem' + $val).prop('disabled', true);
            }
        });
    });
</script>

@stop