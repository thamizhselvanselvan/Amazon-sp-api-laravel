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
        .side-nav{
            display:none;
        }
        .light-close-bg{
            position: fixed;
            top:0;
            left: 0;
            height:100vh;
            width:100vw;
            background:rgba(0, 0, 0, .4);;
            z-index:1100;
        }
        .form-section{
            background: white;
            position: absolute;
            top: 57px;
            right: 0;
            width:50%;
            padding:10px;
            overflow-y: auto;
            z-index:1100;
            z-index: 1100;
            padding: 10px;
        }

        .shipNtrack-grid-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-gap: 10px;
        }
    </style>
@stop

@section('content_header')

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

    <div class="d-flex justify-content-end my-3">
        <x-adminlte-button theme="primary" label="Add" icon="fa fa-plus-circle" class="add" />
    </div>

                <div class="side-nav">
                    <div class="light-close-bg"></div>
                    <div class="form-section">
                        <a class="close"><i class="fa fa-times" aria-hidden="true"></i></a>
                        <h5 class="text-center mb-4 font-weight-bold">ShipNTrack Label Management</h5>

                        <form action="{{ route('shipntrack.label.submit') }}" method="POST" class="shipNtrack-grid-form">
                            @csrf

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Order Number" type="text" name="order_no"
                                        id="order_no" placeholder="Order Number" onblur="validate(1)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Order Item Number" type="text"
                                        name="order_item_id" id="order_item_id" placeholder="Order Item Number"
                                        onblur="validate(2)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Bag No." type="text" name="bag_no"
                                        id="bag_no" placeholder="Bag No." onblur="validate(3)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Forwarder Name" type="text" name="forwarder"
                                        id="forwarder" placeholder="Forwarder Name" onblur="validate(4)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="AWB No." type="text" name="awb_no"
                                        id="awb_no" placeholder="AWB No." onblur="validate(5)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Order Date" type="date" name="order_date"
                                        id="order_date" placeholder="Order Date" min="1997-01-01" max="2030-12-31"
                                        onblur="validate(6)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Customer Name" type="text"
                                        name="customer_name" id="customer_name" placeholder="Customer Name"
                                        onblur="validate(7)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Address" type="text" name="address"
                                        id="address" placeholder="Address" onblur="validate(8)" />
                                </div>


                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="City" type="text" name="city"
                                        id="city" placeholder="City" onblur="validate(9)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="County" type="text" name="county"
                                        id="county" placeholder="County" onblur="validate(10)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Country" name="country" id="country"
                                        placeholder="Country" onblur="validate(11)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Phone" type="text" name="phone"
                                        id="phone" placeholder="Phone" onblur="validate(12)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Product Name" type="text"
                                        name="product_name" id="product_name" placeholder="Product Name"
                                        onblur="validate(13)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="SKU" name="sku" id="sku"
                                        placeholder="SKU" onblur="validate(14)" />
                                </div>

                                <div>

                                    <x-adminlte-input class="mb-0 px-3" label="Quantity" name="quantity" id="quantity"
                                        placeholder="Quantity" onblur="validate(15)" />
                                </div>

                                <div>
                                    <x-adminlte-button label="Submit" type="submit" theme="primary" />
                                </div>
                        </form>

                    </div>
                </div>

    <div class="row ">
        <div class="col"></div>
        <div class="col text-right">

            <x-adminlte-button label="Print Selected" target="_blank" id='print_selected' theme="success"
                icon="fas fa-print" class="btn-sm ml-2" />

            <x-adminlte-button label="Download Selected" target="_blank" id='download_selected' theme="success"
                icon="fas fa-download" class="btn-sm ml-2" />
        </div>
    </div>

    <table class="table table-striped yajra-datatable table-bordered text-center table-sm mt-2">

        <thead class="table-info">
            <th>Select All <input type='checkbox' id='select_all'></th>
            <th>Order No.</th>
            <th>Awb No.</th>
            <th>Courier Name</th>
            <th>Order Date</th>
            <th>Customer Name</th>
            <th>Bag No.</th>
            <th>Action</th>
        </thead>

    </table>
@stop

@section('js')
    <script>
        $(document).ready(function(){
            $(document).on('click', '.add', function() { 
                $('.side-nav').show();
            });
            $(document).on('click', '.close,.light-close-bg', function() { 
                $('.side-nav').hide();
            });
        });

        let yajra_table = $('.yajra-datatable').DataTable({

            processing: true,
            serverSide: true,
            ajax: "{{ route('shipntrack.label.index') }}",
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
                    data: 'order_no',
                    name: 'order_no',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'awb_no',
                    name: 'awb_no',
                },
                {
                    data: 'forwarder',
                    name: 'forwarder',
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
                    data: 'bag_no',
                    name: 'bag_no',
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

        function validate(val) {
            v1 = document.getElementById("order_no");
            v2 = document.getElementById("order_item_id");
            v3 = document.getElementById("bag_no");
            v4 = document.getElementById("forwarder");
            v5 = document.getElementById("awb_no");
            v6 = document.getElementById("order_date");
            v7 = document.getElementById("customer_name");
            v8 = document.getElementById("address");
            v9 = document.getElementById("city");
            v10 = document.getElementById("county");
            v11 = document.getElementById("country");
            v12 = document.getElementById("phone");
            v13 = document.getElementById("product_name");
            v14 = document.getElementById("sku");
            v15 = document.getElementById("quantity");

            flag1 = true;
            flag2 = true;
            flag3 = true;
            flag4 = true;
            flag5 = true;
            flag6 = true;
            flag7 = true;
            flag8 = true;
            flag9 = true;
            flag10 = true;
            flag11 = true;
            flag12 = true;
            flag13 = true;
            flag14 = true;
            flag15 = true;

            if (val >= 1 || val == 0) {
                if (v1.value == "") {
                    v1.style.borderColor = "red";
                    flag1 = false;
                } else {
                    v1.style.borderColor = "green";
                    flag1 = true;
                }
            }

            if (val >= 2 || val == 0) {
                if (v2.value == "") {
                    v2.style.borderColor = "red";
                    flag2 = false;
                } else {
                    v2.style.borderColor = "green";
                    flag2 = true;
                }
            }
            if (val >= 3 || val == 0) {
                if (v3.value == "") {
                    v3.style.borderColor = "red";
                    flag3 = false;
                } else {
                    v3.style.borderColor = "green";
                    flag3 = true;
                }
            }
            if (val >= 4 || val == 0) {
                if (v4.value == "") {
                    v4.style.borderColor = "red";
                    flag4 = false;
                } else {
                    v4.style.borderColor = "green";
                    flag4 = true;
                }
            }
            if (val >= 5 || val == 0) {
                if (v5.value == "") {
                    v5.style.borderColor = "red";
                    flag5 = false;
                } else {
                    v5.style.borderColor = "green";
                    flag5 = true;
                }
            }
            if (val >= 6 || val == 0) {
                if (v6.value == "") {
                    v6.style.borderColor = "red";
                    flag6 = false;
                } else {
                    v6.style.borderColor = "green";
                    flag6 = true;
                }
            }
            if (val >= 7 || val == 0) {
                if (v7.value == "") {
                    v7.style.borderColor = "red";
                    flag7 = false;
                } else {
                    v7.style.borderColor = "green";
                    flag7 = true;
                }
            }

            if (val >= 8 || val == 0) {
                if (v8.value == "") {
                    v8.style.borderColor = "red";
                    flag8 = false;
                } else {
                    v8.style.borderColor = "green";
                    flag8 = true;
                }
            }

            if (val >= 9 || val == 0) {
                if (v9.value == "") {
                    v9.style.borderColor = "red";
                    flag9 = false;
                } else {
                    v9.style.borderColor = "green";
                    flag9 = true;
                }
            }
            if (val >= 10 || val == 0) {
                if (v10.value == "") {
                    v10.style.borderColor = "red";
                    flag10 = false;
                } else {
                    v10.style.borderColor = "green";
                    flag10 = true;
                }
            }
            if (val >= 11 || val == 0) {
                if (v11.value == "") {
                    v11.style.borderColor = "red";
                    flag11 = false;
                } else {
                    v11.style.borderColor = "green";
                    flag11 = true;
                }
            }
            if (val >= 12 || val == 0) {
                if (v12.value == "") {
                    v12.style.borderColor = "red";
                    flag12 = false;
                } else {
                    v12.style.borderColor = "green";
                    flag12 = true;
                }
            }
            if (val >= 13 || val == 0) {
                if (v13.value == "") {
                    v13.style.borderColor = "red";
                    flag13 = false;
                } else {
                    v13.style.borderColor = "green";
                    flag13 = true;
                }
            }
            if (val >= 14 || val == 0) {
                if (v14.value == "") {
                    v14.style.borderColor = "red";
                    flag14 = false;
                } else {
                    v14.style.borderColor = "green";
                    flag14 = true;
                }
            }
            if (val >= 15 || val == 0) {
                if (v15.value == "") {
                    v15.style.borderColor = "red";
                    flag15 = false;
                } else {
                    v15.style.borderColor = "green";
                    flag15 = true;
                }
            }

            flag = flag1 && flag2 && flag3 && flag4 && flag5 && flag6 && flag7 && flag8 && flag9 && flag10 && flag11 &&
                flag12 && flag13 && flag14 && flag15;

            return flag;
        }

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

    @include('shipntrack.Operation.Label.edit_label_page')
@stop
