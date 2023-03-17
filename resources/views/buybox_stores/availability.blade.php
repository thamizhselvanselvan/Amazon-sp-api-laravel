@extends('adminlte::page')

@section('title', 'Stores')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .table td {
            padding: 1;
            padding-left: 1px;
        }

        .table th {
            padding: 2;
            padding-left: 5px;
        }
    </style>
@stop

@section('content_header')

    <div class="row">
        <div style="margin-top: 0.4rem;">
            <h3 class="m-0 text-dark font-weight-bold">
                Select Region: &nbsp;
            </h3>
        </div>
        <div class="col-1.5 region">
            <div style="margin-top: -1.2rem;" class="">
                <x-adminlte-select name="region_select" id="region_select" label="">
                    <option value="">Select Region</option>
                    <option value="IN">IN</option>
                    <option value="AE">AE</option>
                    <option value="SA">SA</option>
                </x-adminlte-select>
            </div>
        </div>

        &nbsp; &nbsp; &nbsp;
        <div style="margin-top: 0.4rem;">
            <h3 class="m-0 text-dark font-weight-bold select_store d-none">
                Select Store: &nbsp;
            </h3>
        </div>
        <div class="col-1.5 region d-none">
            <div style="margin-top: -1.2rem;">
                <x-adminlte-select name="store_select" id="store_select" label="">
                    <option value="">Select Store</option>

                </x-adminlte-select>
            </div>
        </div>

        {{-- <div class="col-3">
            <h2>
                <x-adminlte-button type="button" label="Update" theme="primary" icon="fas fa-refresh" id="update_price" />
            </h2>
        </div> --}}

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

            <table class="table table-bordered yajra-datatable table-striped table-sm">
                <thead>
                    <tr class="table-info">
                        <th>ID</th>
                        <th>ASIN</th>
                        <th>Product SKU</th>
                        <th>Current Availability</th>
                        <th>Push Availability</th>
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

            $('#region_select').on('change', function() {
                let region = $(this).val();
                if (region != '') {
                    $('.region').removeClass("d-none")
                    $('.select_store').removeClass("d-none")
                }

                $.ajax({
                    url: "/stores/region/fetch",
                    method: "GET",
                    data: {
                        "region": region,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $('#store_select').empty();
                        let store_data = '<option >Select Store</option>';
                        $.each(response, function(i, response) {
                            store_data += "<option value='" + response.seller_id +
                                "'>" + response
                                .store_name + "</option>";
                        });
                        $('#store_select').append(store_data);
                    },
                    error: function(response) {
                        console.log(response);
                    },
                });
            });

            $('#store_select').on('change', function() {
                $('.table').DataTable().ajax.reload();
            });

            let yajra_table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 100,
                ajax: {
                    url: "{{ url($url) }}",
                    type: 'get',
                    headers: {
                        'content-type': 'application/x-www-form-urlencoded',
                        "_token": "{{ csrf_token() }}",
                    },
                    data: function(d) {
                        d.store_id = $('#store_select').val();
                    },
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'asin',
                        name: 'asin'
                    },
                    {
                        data: 'product_sku',
                        name: 'product_sku'
                    },
                    {
                        data: 'current_availability',
                        name: 'current_availability',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'availability',
                        name: 'availability',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

        });

        /*
        $(document).on('click', '#price_availability', function() {

            // let bool = confirm("Are you sure you wanna change the availability?");
            // if (!bool) {
            //     return false;
            // }

            var availability = $(this).val();
            if (availability == '1') {
                availability = 0;
            } else {
                availability = 1;
            }
            $(this).val(availability);
            $(this).parent().next().children("a").attr("data-availability", availability);
            console.log($(this).parent().next().children("a").attr("data-availability"));

        }); */

        $(document).on('click', '#update_availability', function() {

            let region = $('#region_select').val();
            let product_id = $(this).data('product_id');
            let seller_id = $(this).data('seller_id');
            let asin = $(this).data('asin');
            let product_sku = $(this).data('product_sku');
            let current_availability = $(this).data('current_availability');
            let availability = $(this).data('availability');

            $.ajax({
                method: "post",
                url: "{{ route('buybox.store.price.push.availability') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "region": region,
                    "id": product_id,
                    "seller_id": seller_id,
                    "asin": asin,
                    "product_sku": product_sku,
                    "current_availability": current_availability,
                    "availability": availability
                },
                success: function(response) {
                    console.log(response);


                },
            });

        });
    </script>
@stop
