@extends('adminlte::page')

@section('title', 'Amazon Orders Statistics')

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

    .click {
        color: green;
    }

    .not {
        color: black;
    }

    .under {
        color: blue;
    }

    .wrong {
        color: red;
    }

    :root {
        --main-color: #111;
        --loader-color: #4CAF50;
        --back-color: #A5D6A7;
        --time: 60s;
        --size: 3px;
    }

    .loader {
        background-color: transparent;
        overflow: hidden;
        width: 100%;
        height: 10%;
        position: inherit;
        top: 59px;
        left: 0;
        display: flex;
        align-items: center;
        align-content: center;
        justify-content: flex-start;
        z-index: 100000;
    }

    .loader__element {
        height: var(--size);
        width: 100%;
        background: var(--back-color);

    }

    .loader__element:before {
        content: '';
        display: block;
        background-color: var(--loader-color);
        height: var(--size);
        width: 0;
        animation: getWidth var(--time) ease-in infinite;
    }

    @keyframes getWidth {
        100% {
            width: 100%;
        }
    }
</style>
@stop

@section('content_header')
<div class="loader">
    <div class="loader__element"></div>
</div>



<div class="row">
    <div class="col-1.5">
        <div style="margin-top: 1.6rem;">
            <h3 class="m-0 text-dark font-weight-bold">
                Orders Status: &nbsp;
            </h3>
        </div>
    </div>

    <!-- <form class="row"> -->
    <div class="col-2.5">

        <x-adminlte-select name="ware_id" id="store_select" label="">
            <option value="">Select Store</option>
            @foreach($stores as $store)
            <option value="{{$store->store_id}}" {{ $request_store_id == $store->store_id ? "selected" : '' }}>{{$store->store_name}}</option>
            @endforeach
        </x-adminlte-select>
    </div>
    <!-- </form> -->
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
    </div>
</div>

<table class="table table-bordered yajra-datatable table-striped" id="detail_table">
    <thead>
        <tr class="table-info">
            <th>ID</th>
            <th>Store Name</th>
            <th>Order Date</th>
            <th>Amazon Order ID</th>
            <th>Order Item ID</th>
            <th>Courier</th>
            <th>AWB</th>
            <th>Booking</th>
            <th>Zoho</th>
            <th>Amazon</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
@stop

@section('js')

<script type="text/javascript">
    setInterval(function() {
        window.location.reload(1);
    }, 60000);

    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $.extend($.fn.dataTable.defaults, {
        pageLength: 100,
    });

    $('#store_select').on('change', function() {
        window.location = "/orders/statistics/" + $(this).val();
    });


    $(document).on('click', '#clipboard', function() {
        data = $(this).attr('value');
        navigator.clipboard.writeText(data);
    });

    $(document).on('click', '#zoho_clipboard', function() {
        data = $(this).attr('value');
        navigator.clipboard.writeText(data);
    });

    $(document).on('click', "#order_retry", function() {
        let self = $(this);
        let order_row_id = self.data("id");
        let couriername = self.data("couriername");
        let awb = self.data("awb");

        self.prop("disabled", true);

        $.ajax({
            method: 'post',
            url: "{{ route('orders.retry') }}",
            data: {
                'id': order_row_id,
                'couriername': couriername,
                'courier': awb,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                console.log(response);
                self.prop("disabled", false);

                if(response.hasOwnProperty("success")) {
                    alert(response.success);
                    self.attr("display", "none");
                }

                if(response.hasOwnProperty("error")) {
                    alert("please retry sometime later!");
                }
            },
            error: function(response) {
                self.prop("disabled", false);
                alert('something went wrong Please Contact Admin');
            }
        });


        console.log(order_row_id, couriername, awb);

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
                d.store_id = $('#store_select').val();
            },
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'store_name',
                name: 'store_name',
                orderable: false,
                searchable: false
            },
            {
                data: 'order_date',
                name: 'order_date',
                orderable: false,
                searchable: false
            },
            {
                data: 'amazon_order_id',
                name: 'amazon_order_id'
            },
            {
                data: 'order_item_id',
                name: 'order_item_id'
            },
            {
                data: 'courier_name',
                name: 'courier_name',
                orderable: false,
                searchable: false
            },
            {
                data: 'courier_awb',
                name: 'courier_awb'
            },
            {
                data: 'booking_status',
                name: 'booking_status',
                orderable: false,
                searchable: false
            },
            {
                data: 'zoho_status',
                name: 'zoho_status',
            },
            {
                data: 'order_feed_status',
                name: 'order_feed_status',
                orderable: false,
                searchable: false
            }
        ]
    });
</script>
@stop