@extends('adminlte::page')

@section('title', 'Pending Orders')

@section('content_header')

@stop


@section('content')
{{-- tabs switcher ~start --}}
<div class="row  pt-4 justify-content-center">
    <a class="btn btn-lg btn-app bg-secondary" style="width:130px" href="{{url('business/orders/details')}}">
        <i class="fa fa-clock-o"></i> Order Pending
    </a>
    <a class="btn btn-lg btn-app bg-success" style="width:130px" href="{{url('business/booked/details')}}">
        <i class="fa fa-check"></i> Order booked
    </a>
    <a class="btn btn-lg btn-app bg-info" style="width:130px" href="{{url('business/orders/confirm')}}">
        <i class="fa fa-check-circle-o"></i> Order Confirmation
    </a>
    <a class="btn btn-lg btn-app bg-warning" style="width:130px" href="{{url('business/ship/confirmation')}}">
        <i class="fa fa-bell "></i> Shipment Notification
    </a>
</div>
{{-- tabs switcher ~end --}}

<div class="row ">
    <div class="col ">
        <div style="margin: 0.1rem 0; text-align: center">
            <h3>Pending Order Details</h3>
        </div>
    </div>
</div>

<div class="loader d-none">
    <div class="sub-loader position-relative ">
        <div class="lds-hourglass"></div>
        <p>Loading...</p>
    </div>
</div>

<div class="row">
    <div class="col-8">
        @if(session()->has('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session()->get('success') }}
        </x-adminlte-alert>
        @endif

        @if(session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session()->get('error') }}
        </x-adminlte-alert>
        @endif
    </div>
</div>

<div class="modal " id="selectoffer">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h4 class="modal-title text-center">Select Offer </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body offerselect">

            </div>
            <div class="modal-footer">
                <div class="col-2.5 float-righr mt-2">
                    <x-adminlte-button label="Place Order" theme="success" class="btn btn-sm " id="place_order" icon="fas fa-file-export " />
                </div>
                <div class="col-1 float-right mt-2">
                    <button type="button" class="btn btn-sm btn-danger" id="close" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- collapse filter card start --}}
<div class="card card-info " id="filter-card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div>
                    <form class="form-inline" id="form-log-delete" method="get" action="{{ route('business.orders.pending.list') }}">

                        <div class="form-group">

                            <x-adminlte-select name="site_id" id="filterSites" class="form-control form-control-sm">
                                <option value='' selected>Select the Site to Apply filter</option>
                                @foreach ($sites as $site)
                                @if ($site->code == 'in')
                                {{ $site->code = 'India' }}
                                @elseif ($site->code == 'uae')
                                {{ $site->code = 'UAE' }}
                                @endif
                                <option value="{{ $site->siteid }}">{{ $site->code }}</option>
                                @endforeach
                            </x-adminlte-select>


                        </div>

                        <button type="submit" id="clear_log" class="btn btn-warning mx-2 btn-sm">Apply</button>
                        <a class="btn btn-default  btn-sm" href="{{route('business.orders.pending.list')}}">Reset</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- collapse filter card -end --}}

<table class="table table-bordered yajra-datatable table-striped" id='orderspending'>
    <thead>
        <tr class='text-bold bg-info'>
            <th> ID </th>
            <th>ASIN</th>
            <th>Item Name</th>
            <th>Site</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Coupon</th>
            <th>Total Price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="data_display_pending">

    </tbody>
</table>
@stop
@section('js')
<script type="text/javascript">
    const filter = {
        site_id: new URLSearchParams(window.location.search).get('site_id'),
    }

    let url = `{{ route('business.orders.pending.list') }}?`;
    if (!!filter.site_id) {
        url += `site_id=${filter.site_id}`;

        var filterSites = document.getElementById('filterSites'),
            filterSite, i;
        for (i = 0; i < filterSites.length; i++) {
            filterSite = filterSites[i];
            if (filterSite.value == filter.site_id) {
                filterSite.setAttribute('selected', true);
            }
        }
    }

    $(function() {

        $.extend($.fn.dataTable.defaults, {
            pageLength: 50,

        });

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'asin',
                    name: 'asin'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'site',
                    name: 'site'
                },

                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'coupan',
                    name: 'coupan'
                },
                {
                    data: 'total_price',
                    name: 'total_price'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },



            ]
        });

    });

    $(document).on("click", ".offers1", function() {
        let self = $(this);
        let asin = $(this).attr('value');
        let qty = self.parent().parent().prev().prev().text();

        $.ajax({
            method: 'GET',
            url: '/business/offers_view/',
            data: {
                'asin': asin,
                'quantity': qty,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                if (response == '') {
                    response = 'item Might Currently unavailable. please try After some time';
                }
                $('#selectoffer').modal('show');
                $('.offerselect').html(response);
            },
            error: function(response) {
                console.log(response);
                alert('Something Went Wrong.. or No offer found')
            }
        });

    });


    $('#place_order').on("click", function() {
        $('.display-data').addClass('d-block');
        let catalog_asins = $('.Asins').val();
        let source = $('input[name="oid"]:checked').val();

        if (!$('input[name="oid"]:checked').val()) {
            alert('Please choose an Offer');
            return false;
        } else {
            let offerid = $("input[name='oid']").val();
            let asin = $("input[name='asin']").val();
            let name = $("input[name='item_name']").val();
            let quantity = $("input[name='quantity']").val();

            $.ajax({
                method: 'get',
                url: "/business/order/book/",
                data: {
                    "offerid": offerid,
                    "asin": asin,
                    "item_name": name,
                    "quantity": quantity,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    console.log(response);
                    alert('Request sent Successfully Check Order Conformation For More Details')
                    location.reload();
                },
                error: function(response) {
                    console.log(response);
                    alert('Something went Wrong. Order not Booked')
                }
            });

        }
    });

    $('#close').click(function() {
        $('#selectoffer').modal('hide');
    });
</script>
@stop