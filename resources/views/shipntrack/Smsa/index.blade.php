@extends('adminlte::page')

@section('title', 'Courier Tracking')

@section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark col">Courier Tracking</h1>
        <h2 class="mb-4 text-right col">
            <a href="{{ Route('shipntrack.smsa.upload') }}">
                <x-adminlte-button label="Add New SMSA AWB No." theme="primary" icon="fas fa-file-upload" class="btn-sm" />
            </a>
        </h2>
    </div>
    <div class="row">
        <x-adminlte-select name="source_destination" id="sourceDestination">
            <option>Select Option</option>
            <option value="AE">AE</option>
            <option value="IN">IN</option>
            <option value="KSA">KSA</option>
        </x-adminlte-select>
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
        <table class="table table-bordered yajra-datatable table-striped text-center" style="line-height:12px">
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
