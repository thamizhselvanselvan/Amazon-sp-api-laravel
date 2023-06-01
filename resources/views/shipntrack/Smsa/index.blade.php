@extends('adminlte::page')

@section('title', 'Courier Tracking')
<style>
    tbody {
        background: white;
    }

    table {
        width: 100% !important;
    }

    .table td,
    .table th {
        padding: 0.55rem !important;
        border-top: 1px solid #dee2e6;
        font-size: 1rem;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: inherit !important;
    }

    .table thead {
        background: #ededed;
    }

    .table thead th {
        border-bottom: 1px solid #dee2e6 !important;
    }

    .form-control {
        height: inherit !important;
        padding: 0.25rem 0.75rem !important;
    }

    .content .container-fluid {
        background: white;
        padding: 10px;
        border-radius: 5px;
    }
</style>
@section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark col">Courier Tracking</h1>
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

            <div class="alert_display">
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="pl-2">
        <h2 class="text-right col d-flex justify-content-end">
            <x-adminlte-select name="source_destination" id="sourceDestination" class="mr-2">
                <option>Select Option</option>
                <option value="AE">AE</option>
                <option value="IN">IN</option>
                <option value="SA">SA</option>
            </x-adminlte-select>
            {{-- <a href="{{ Route('shipntrack.smsa.upload') }}">
                <x-adminlte-button label="Add New SMSA AWB No." theme="primary" icon="fas fa-file-upload" class="btn-sm" />
            </a> --}}
        </h2>
        <table class="table table-bordered yajra-datatable table-striped text-center">
            <thead>
                <tr>
                    <th>AWB No.</th>
                    <th>Forwarder1 AWB No.</th>
                    <th>Forwarder2 AWB No.</th>
                    <th>Forwarder3 AWB No.</th>
                    <th>Forwarder4 AWB No.</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $('#sourceDestination').change(function() {

            let sourceDestination = $(this).val();
            if (sourceDestination == 'NULL') {

            } else {

                let yajra_table = $('.yajra-datatable').DataTable({

                    destroy: true,
                    processing: true,
                    serverSide: true,
                    bLengthChange: false,
                    ajax: {

                        url: "{{ route('shipntrack.courier.tracking') }}",
                        method: 'GET',
                        data: function(d) {
                            d.sourceDestination = sourceDestination;
                        },
                    },
                    pageLength: 100,
                    columns: [{
                            data: 'awb_number',
                            name: 'awb_number',
                            orderable: false,
                            searchable: false

                        },
                        {
                            data: 'forwarder1_awb',
                            name: 'forwarder1_awb',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'forwarder2_awb',
                            name: 'forwarder2_awb',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'forwarder3_awb',
                            name: 'forwarder3_awb',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'forwarder4_awb',
                            name: 'forwarder4_awb',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'created_date',
                            name: 'created_date'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }

        });
    </script>
@stop
