@extends('adminlte::page')
@section('title', 'Label')

@section('css')
    <style>
        .card {
            padding: 10px 40px;
            margin-top: 02px;
            margin-bottom: 10px;
            border: none !important;
            box-shadow: 0 6px 12px 0 rgba(0, 0, 0, 0.2);
        }

        .side-nav {
            display: none;
        }

        .light-close-bg {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 100vw;
            background: rgba(0, 0, 0, .4);
            ;
            z-index: 1100;
        }

        .form-section {
            background: white;
            position: absolute;
            top: 57px;
            right: 0;
            width: 50%;
            padding: 10px;
            overflow-y: auto;
            z-index: 1100;
            z-index: 1100;
            padding: 10px;
        }

        .shipNtrack-grid-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 10px;
        }

        .submit-button {
            margin-top: 31px;
        }

        .form-group {
            margin-bottom: 0;
        }

        #destination {
            height: 33px;
        }
    </style>
@stop

@section('content_header')

    <div class="side-nav">
        <div class="light-close-bg"></div>
        <div class="form-section">
            <a class="close"><i class="fa fa-times" aria-hidden="true"></i></a>
            <h5 class="text-center mb-4 font-weight-bold">ShipNTrack Label Management</h5>

        </div>
    </div>


    <div class="row mt-2">
        <div class="col"></div>
        <div class="col d-flex justify-content-end align-items-center py-0">

            <x-adminlte-select name="destination" id="destination" class=" ml-2">
                <option value="0">Select Option</option>
                @foreach ($values as $value)
                    <option value="{{ $value['destination'] }}">{{ $value['source'] . '-' . $value['destination'] }}
                    </option>
                @endforeach
            </x-adminlte-select>


            <x-adminlte-button label="Print Selected" target="_blank" id='print_selected' theme="success"
                icon="fas fa-print" class="btn-sm ml-2" />

            <x-adminlte-button label="Download Selected" target="_blank" id='download_selected' theme="success"
                icon="fas fa-download" class="btn-sm ml-2" />

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

    <table class="table table-striped yajra-datatable table-bordered text-center table-sm mt-2">

        <thead class="table-info">
            <th>Select All <input type='checkbox' id='select_all'></th>
            <th>Purchase ID</th>
            <th>Order No.</th>
            <th>Awb No.</th>
            <th>Courier Name</th>
            <th>Order Date</th>
            <th>Customer Name</th>
            <th>Action</th>
        </thead>

    </table>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.add', function() {
                $('.side-nav').show();
            });
            $(document).on('click', '.close,.light-close-bg', function() {
                $('.side-nav').hide();
            });
        });

        $('#destination').on("change", function() {

            let yajra_table = $('.yajra-datatable').DataTable({

                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('shipntrack.label.index') }}",
                    dataType: "json",
                    type: 'GET',
                    data: function(d) {
                        d.destination = $('#destination').val();
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
                        data: 'purchase_tracking_id',
                        name: 'purchase_tracking_id',
                    },
                    {
                        data: 'order_no',
                        name: 'order_no',
                    },
                    {
                        data: 'awb_no',
                        name: 'awb_no',
                    },
                    {
                        data: 'courier_name',
                        name: 'courier_name',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'order_date',
                        name: 'order_date',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },

                ],
            });
        });

        $(document).on('click', '.label_view', function() {

            let table = $('#destination').val();
            alert(table);
            return false;
        });

        $('#select_all').change(function() {

            if ($('#select_all').is(':checked')) {

                $('.check_options').prop('checked', true);
            } else {

                $('.check_options').prop('checked', false);
            }
        });

        $('#print_selected').click(function() {

            let id = '';
            let count = '';
            $("input[name='options[]']:checked").each(function() {
                if (count == 0) {
                    id += $(this).val();
                } else {
                    id += '-' + $(this).val();
                }
                count++;
            });
            window.open("/shipntrack/label/template/" + id, "_blank");
        });

        $('#download_selected').click(function() {
            let id = '';
            let count = '';
            $("input[name='options[]']:checked").each(function() {
                if (count == 0) {
                    id += $(this).val();
                } else {
                    id += '-' + $(this).val();
                }
                count++;
            });
            window.location.href = "/shipntrack/label/pdf/download/" + id;
        });
    </script>

    @include('shipntrack.Operation.LabelManagement.Label.edit_label_page')
@stop
