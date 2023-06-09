@extends('adminlte::page')
@section('title', 'SNT Invoice')

@section('content_header')

    <div class="row ">
        <h1>ShipnTrack Invoice Management</h1>
        <div class="col"></div>
        <div class="col-6 d-flex justify-content-end ">
            <div class="form-group">
                <x-adminlte-select name="mode" id="mode">
                    <option value="0">Select Mode</option>
                    @foreach ($values as $value)
                        <option value="{{ $value['destination'] }}">
                            {{ $value['source'] . '2' . $value['destination'] }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>
            <div class="form-group ">
                <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="search" class=" ml-2" />

            </div>
            <div class="form-group ">
                <x-adminlte-button label="Print Selected" target="_blank" id='print_selected' theme="success"
                    icon="fas fa-print" class=" ml-2" />

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
                <tr class="text-bold bg-info">
                    <th>Select All <input type="checkbox" id="selectAll" /></th>
                    <th>Invoice No.</th>
                    <th>AWB No.</th>
                    <th>Invoice Date</th>
                    <th>Channel</th>
                    <th>Shipped By</th>
                    <th>Store Name</th>
                    <th>Bill To Name</th>
                    <th>Ship To Name</th>
                    <th>SKU</th>
                    <th>QTY</th>
                    <th>Price</th>
                    <th class='text-center'>Action</th>
                </tr>
            </thead>
            <tbody id='checkTable'>
            </tbody>
        </table>
    </div>
@stop

@section('js')

    <script type="text/javascript">
        $('#selectAll').click(function() {

            if ($(this).is(':checked')) {
                $('.check_options').prop('checked', true);
            } else {
                $('.check_options').prop('checked', false);
            }

        });


        $('#search').click(function() {

            $('#selectAll').prop('checked', false);

            let yajra_table = $('.yajra-datatable').DataTable({

                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('shipntrack.invoice.home') }}",
                    dataType: "json",
                    type: 'GET',
                    data: function(d) {
                        d.destination = $('#mode').val();
                    }
                },
                pageLength: 40,
                searching: false,
                bLengthChange: false,
                columns: [{
                        data: 'select_all',
                        name: 'select_all',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'awb_no',
                        name: 'awb_no',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'channel',
                        name: 'channel',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'shipped_by',
                        name: 'shipped_by',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'store_name',
                        name: 'store_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'bill_to_name',
                        name: 'bill_to_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ship_to_name',
                        name: 'ship_to_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sku',
                        name: 'sku',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'quantity',
                        name: 'quantity',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'price',
                        name: 'price',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

        });

        $('#print_selected').click(function() {

            let ids = '';
            let count = 0;
            let destination = $('#mode').val();

            $("input[name='all[]']:checked").each(function() {
                if (count == 0) {
                    ids = $(this).val();
                } else {
                    ids += '-' + $(this).val();
                }
                count++;
            });
            window.open("/shipntrack/invoice/template/" + destination + "/" + ids, "_blank");

        });
    </script>
@stop
