@extends('adminlte::page')

@section('title', ' Inward Shipment')

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

</style>
@stop

@section('content_header')
<h1 class="m-0 text-dark"> Inward Shipment</h1>

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
            @if($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
            @if($message = Session::get('warning'))
     
                <div class="alert alert-warning alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong class="er_asin">{{ $message }}</strong>
                </div>
         
            @endif
        </div>
        <h2 class="mb-4">
            <a href="{{ route('shipments.create') }}">
                <x-adminlte-button label="Create Shipment" class="btn-sm" theme="primary" icon="fas fa-plus" />
            </a>

            <!-- <a href="{{ route('inventory.inward.csv') }}"> -->
            <x-adminlte-button label="Upload CSV" theme="info" class="btn-sm" icon="fas fa-upload" id="new_asin" data-toggle="modal" data-target="#cliqnshop_new_asin_modal" />
            <!-- </a> -->
        </h2>

        </h2>
        <!-- Csv upload model -->
        <div class="modal fade" id="cliqnshop_new_asin_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="new_asin">Upload Inventory Inward CSV</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                        Please download <strong>CSV Templete</strong> and upload the data in csv format only.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form id="multi-file-upload" method="POST" action="{{ route('inventory.inward.csv') }}" accept-charset="utf-8" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <x-adminlte-input label="Choose CSV File" name="inventory_csv" id="files" type="file" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <a href="{{route('inventory.download.template')}}">
                                        <x-adminlte-button label="Download Template" theme="info" icon="fas fa-file-download" class="btn-sm ml-2" />
                                    </a>

                                    <x-adminlte-button label="Submit" theme="primary" class="add_ btn-sm" icon="fas fa-upload" type="submit" id="order_upload" />
                                </div>
                                <div class="col text-right">
                                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
                                        <i class="fas fa-window-close" aria-hidden="true"></i>
                                        Close
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end of csv upload model -->
        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr class="table-info">
                    <th>ID</th>
                    <th>Date</th>
                    <th>Shipment ID</th>
                    <th>Source</th>
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
    $(function() {

        $.extend($.fn.dataTable.defaults, {
            pageLength: 50,
        });

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('shipments.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'ship_id',
                    name: 'ship_id'
                },
                {
                    data: 'source_name',
                    name: 'source_name'
                },

                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
        $(document).on('click', ".delete", function(e) {
            e.preventDefault();
            let bool = confirm('Are you sure you want to delete?');

            if (!bool) {
                return false;
            }
            let self = $(this);
            let id = self.attr('data-id');

            self.prop('disable', true);


            $.ajax({
                method: 'post',
                url: '/inventory/shipments/' + id,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "_method": 'DELETE'
                },
                response: 'json',
                success: function(response) {
                    alert('Delete success');
                    location.reload()
                },
                error: function(response) {


                }
            });
        });
    });
</script>
@stop