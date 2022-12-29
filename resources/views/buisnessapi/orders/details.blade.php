@extends('adminlte::page')

@section('title', 'Pending Orders')

@section('content_header')

@stop


@section('content')

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
<div class="row">
    <div class="col">
        <div style="margin-top: 1.0rem;">
            <h3>Pending Order Details</h3>
        </div>
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
<table class="table table-bordered yajra-datatable table-striped" id='orderspending'>
    <thead>
        <tr class='text-bold bg-info'>
            <th> ID </th>
            <th>ASIN</th>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="data_display_pending">

    </tbody>
</table>
@stop
@section('js')
<script type="text/javascript">
    $(function() {

        $.extend($.fn.dataTable.defaults, {
            pageLength: 50,

        });

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('business.orders.pending.list') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'prodcode',
                    name: 'prodcode'
                },
                {
                    data: 'name',
                    name: 'name'
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