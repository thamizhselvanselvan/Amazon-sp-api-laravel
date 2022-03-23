@extends('adminlte::page')

@section('title', 'ASIN Master')

@section('content_header')
    <h1 class="m-0 text-dark">ASIN</h1>
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

                <!-- <a href="add-asin">
                                    <x-adminlte-button label="Add Asin" theme="primary" icon="fas fa-plus-circle"/>
                                </a> -->
                <a href="import-bulk-asin">
                    <x-adminlte-button label="Asin Bulk Import" theme="primary" icon="fas fa-file-import" />
                </a>
                <a href="export-asin">
                    <x-adminlte-button label="Asin Export" theme="primary" icon="fas fa-file-export" />
                </a>

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"
                    theme="primary"> Download Asin</button>
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Asin Download</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <ul>
                                    <li>
                                        <a href="{{ route('download.asinMaster') }}">
                                            <h4>Download Asin Master</h4>
                                        </a>
                                    </li>
                                </ul>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            </h2>

            <table class="table table-bordered yajra-datatable table-striped">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>ASIN</th>
                        <th>Source</th>
                        <th>Destination 1</th>
                        <th>Destination 2</th>
                        <th>Destination 3</th>
                        <th>Destination 4</th>
                        <th>Destination 5</th>
                        <th>Action</th>
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
            ajax: "{{ url('asin-master') }}",
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
                    data: 'source',
                    name: 'source'
                },
                {
                    data: 'destination_1',
                    name: 'destination_1'
                },
                {
                    data: 'destination_2',
                    name: 'destination_2'
                },
                {
                    data: 'destination_3',
                    name: 'destination_3'
                },
                {
                    data: 'destination_4',
                    name: 'destination_4'
                },
                {
                    data: 'destination_5',
                    name: 'destination_5'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },

            ]
        });
    </script>
@stop
