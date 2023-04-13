@extends('adminlte::page')
@section('title', 'SNT Invoice')

@section('content_header')
<div class="row">

    <div class="col-2">
        <div style="margin-top: -1.0rem;">
            <div class="form-group">
                <x-adminlte-select name="mode" label="Select Mode:" id="mode">
                    <option value="0">Select Mode</option>
                    @foreach ($modes as $data)
                    <option value="{{ $data->mode }}" {{ $request_mode ==  $data->mode  ? "selected" : '' }}>{{$data->mode }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>
        </div>
    </div>
    <div class="col-0.5">
        <div style="margin-top: 1.0rem;">
            <a href="{{ route('shipntrack.invoice.add.view') }}" class="add">
                <x-adminlte-button label="Add Records" theme="primary" icon="fa fa-plus" id="add" class="add_btn " />
            </a>
        </div>
    </div>
    <div class="col-5">
        <div style="margin-top: 1.0rem;">
            <h1 class="m-0 text-dark text-center col">ShipNtrack Invoice Management</h1>
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
    </div>
</div>


<div id="showTable" class="">
    <table class='table table-bordered yajra-datatable table-striped text-center'>
        <thead>
            <tr class="table-info">
                <!-- <th>Select All <input type='checkbox' id='selectAll'></th> -->
                <th>Invoice No.</th>
                <th>Invoice Date</th>
                <th>Mode</th>
                <th>Channel</th>
                <th>Shipped By</th>
                <th>AWB No.</th>
                <th>Store Name</th>
                <th>Bill To Name</th>
                <th>Ship To Name</th>
                <th>SKU</th>
                <th>QTY</th>
                <th>Price</th>
                <th class='text-center'>Action</th>
            </tr>
        </thead>
        <tbody id='checkTable' class="">
        </tbody>
    </table>
</div>
@stop

@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('#mode').on('change', function() {
            // $('$mode').removeClass("d-none");
            window.location = "/shipntrack/invoice/" + $(this).val();
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
                    d.mode = $('#mode').val();
                    console.log(mode);
                },
            },
            columns: [{
                    data: 'invoice_no',
                    name: 'invoice_no',
                },
                {
                    data: 'invoice_date',
                    name: 'invoice_date',
                },
                {
                    data: 'mode',
                    name: 'mode',
                },
                {
                    data: 'channel',
                    name: 'channel',
                },
                {
                    data: 'shipped_by',
                    name: 'shipped_by',
                },
                {
                    data: 'awb_no',
                    name: 'awb_no',
                },
                {
                    data: 'store_name',
                    name: 'store_name',
                },
                {
                    data: 'bill_to_name',
                    name: 'bill_to_name',
                },
                {
                    data: 'ship_to_name',
                    name: 'ship_to_name',
                },
                {
                    data: 'sku',
                    name: 'sku',
                },
                {
                    data: 'quantity',
                    name: 'quantity',
                },
                {
                    data: 'product_price',
                    name: 'product_price',
                },
                {
                    data: 'action',
                    name: 'action',
                },
            ]
        });


    });
</script>
@stop