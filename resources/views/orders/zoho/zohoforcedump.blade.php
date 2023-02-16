@extends('adminlte::page')

@section('title', 'Zoho Dump')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark"> Zoho Dump And Sync</h1>
        </div>
        <h2 class="ml-2">
            <x-adminlte-button label=" Zoho operations" class="btn-sm" theme="primary" icon="fa fa-tasks " id="zoho"
                data-toggle="modal" data-target="#zohomodal" />
        </h2>
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
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                @if ($message = Session::get('warning'))
                    <div class="alert alert-warning alert-block alert">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="zohomodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><b>Zoho Operations</b></h5>
                    <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="fasle">&times;</span>
                    </button>
                </div>
                <div class="modal-body " style="font-size:15px">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane"
                                aria-selected="true">
                                Dump order from Amazon to App360
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab"
                                data-bs-target="#profile-tab-pane" type="button" role="tab"
                                aria-controls="profile-tab-pane" aria-selected="false">
                                Sync order from App360 to Zoho
                            </button>
                        </li>
                    </ul>

                    <!--zoho dump Tab -->
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                            tabindex="0">
                            <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                                Zoho<strong> DUMP </strong> Tab (10 orderID's Acceptet At a time)
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ Route('orders.zoho.force.dump') }}" method="POST" id="admin_user">
                                @csrf
                                <div class="row">
                                    <div class="col-12">

                                        <x-adminlte-select name="country_code" label="Select Store:" id="store_select_sync">
                                            <option value="0">Select Store</option>
                                            @foreach ($stores as $store)
                                                <option value="{{ $store->seller_id . '_' . $store->country_code }}">
                                                    {{ $store->store_name }}</option>
                                            @endforeach
                                        </x-adminlte-select>

                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-6" id="order_id">
                                        <div class="form-group">
                                            <label>Enter order ID's:</label>
                                            <div class="autocomplete" style="width:470px;">
                                                <textarea name="order_ids" rows="5" placeholder="Add order id's here..." id="" type=" text"
                                                    autocomplete="off" class="form-control up_asin_sync"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload"
                                            id="upload_sync" class="btn-sm upload_asin_btn" type="submit" />
                                    </div>
                                </div>
                            </form>
                        </div>


                        <!--zoho sync Tab -->
                        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
                            tabindex="0">
                            <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                                Zoho<strong> SYNC </strong> Tab (10 orderID's Acceptet At a time)
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form class="" id="multi-file-upload" method="POST"
                                action="{{ route('orders.zoho.sync') }}" accept-charset="utf-8"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <x-adminlte-select name="store_data" label="Select Store:" id="store_select">
                                            <option value="0">Select Store</option>
                                            @foreach ($stores as $store)
                                                <option value="{{ $store->seller_id . '_' . $store->country_code }}">
                                                    {{ $store->store_name }}</option>
                                            @endforeach
                                        </x-adminlte-select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4" id="order_id">
                                        <div class="form-group">
                                            <label>Enter order ID's:</label>
                                            <div class="autocomplete" style="width:470px;">
                                                <textarea name="order_ids" rows="5" placeholder="Add order id's here..." id="" type=" text"
                                                    autocomplete="off" class="form-control up_asin"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload"
                                            id="upload" class="btn-sm upload_asin_btn" type="submit" />
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- End of zoho sync  -->
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fas fa-window-close" aria-hidden="true"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')

    <script type="text/javascript">
        $(document).ready(function() {

            document.getElementById("zoho").click();
        });
        $("#upload").on('click', function(e) {


            let data = $('.up_asin').val();
            let store = $('#store_select').val();
            if (store == '0') {
                alert('Please Select Store');
                return false;
            } else if (data == '') {
                alert('please Enter Order ID');
                return false;
            }

        });

        $("#upload_sync").on('click', function(e) {
            let data_sync = $('.up_asin_sync').val();
            let store_sync = $('#store_select_sync').val();
            if (store_sync == '0') {
                alert('Please Select Store');
                return false;
            } else if (data_sync == '') {
                alert('please Enter Order ID');
                return false;
            }
        });

        // $(document).ready(function() {

        //     $.ajax({
        //             url: "{{ route('orders.zoho.force.dump.view') }}",
        //             method: 'get',
        //             data: {
        //                 'command': 'command',
        //                 "_token": "{{ csrf_token() }}",
        //             },
        //             success: function(result) {

        //             if (result.data.hasOwnProperty("error")) {
        //                 if ((result['data']['error']['0']['status']) == '0') {
        //                     alert('Previous Order ID is Still Processing Please Wait...');
        //                     document.getElementById("upload").disabled = true;
        //                 }
        //             }
        //         },
        //         error: function() {
        //             alert('ERROR');
        //         }
        //     });

        // });
    </script>

@stop
