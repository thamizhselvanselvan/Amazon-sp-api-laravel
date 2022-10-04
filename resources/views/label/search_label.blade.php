@extends('adminlte::page')
@section('title', 'Search Label')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">

@stop
@section('content_header')


    <!--  Edit address modal start -->
    <div class="modal fade " id="crud-modal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="customerCrudModal">Order Address Details Editer</h4>
                </div>
                <div class="modal-body">
                        
                        <div class="text-center">
                            <div id="spinner-container" class="spinner-border justify-content-center"  role="status" >
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        
                        <div id="form-content" style="display: none">
                            <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Be carefull!</strong> changes canot be reverted back ....
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <form name="orderAddressForm" id="orderAddressForm"  method="POST"  action="javascript:void(0)">
                            <input type="hidden" name="order_item_identifier" id="order_item_identifier">
                            <input type="hidden" name="amazon_order_identifier" id="amazon_order_identifier">
                            @csrf
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Name:</strong>
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Name" onchange="validate()">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <strong>Phone:</strong>
                                        <input type="text" name="phone" id="phone" class="form-control"
                                            placeholder="Phone" onchange="validate()">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <strong>City:</strong>
                                        <input type="text" name="city" id="city" class="form-control"
                                            placeholder="City" onchange="validate()">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <strong>County:</strong>
                                        <input type="text" name="county" id="county" class="form-control"
                                            placeholder="County" onchange="validate()">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <strong>CountryCode:</strong>
                                        <input type="text" name="countryCode" id="countryCode" class="form-control"
                                            placeholder="CountryCode" onchange="validate()">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>AddressType:</strong>
                                        <input type="text" name="addressType" id="addressType" class="form-control"
                                            placeholder="AddressType" onchange="validate()" >
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>AddressLine1:</strong>
                                        <textarea name="addressLine1" id="addressLine1" class="form-control"
                                            placeholder="AddressLine1" onchange="validate()" ></textarea>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>AddressLine2:</strong>
                                        <textarea name="addressLine2" id="addressLine2" class="form-control"
                                            placeholder="AddressLine2" onchange="validate()" ></textarea>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                    <button type="submit" id="btn-update-order" name="btnsave" class="btn btn-primary"
                                        >Update</button>
                                    <a id="closemodal"  class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </form>
                      </div>
                    
                    
                </div>
            </div>
        </div>
    </div>
    <!--  Edit address modal start -->


    <div class="row">
        <h1 class="m-0 text-dark col">Label Management</h1>
        <h2 class="mb-4 text-right col"></h2>
        <label>
            Search:<input type="text" id="Searchbox" placeholder="Search label" autocomplete="off">
        </label>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="{{ route('label.manage') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
            <a href="zip/download">
                <x-adminlte-button label="Download Label Zip" theme="primary" icon="fas fa-download"
                    class="btn-md ml-2 btn-sm" id='zip-download' />
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col">

            <div class="alert_display">
                @if ($message = Session::get('success'))
                    <div class="alert alert-warning alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="container-fluid label-search-box">
        <div class="row">
            <div class="col">

            </div>
            <div class="col">
                <form action="">
                    @csrf
                    <div class="form-group">
                        <label>Bag No.:</label>
                        <div class="input-group">
                            <!-- <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div> -->

                            <input type="text" class="form-control float-right" name="bag_no"
                                placeholder="Input Bag No." id="bag_no">
                            <!-- <input type="text" class="form-control float-right datepicker" name="label_date" placeholder="Select Date Range" autocomplete="off" id="label_date"> -->
                            <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="SearchByDate"
                                class="btn-sm ml-2" />
                            <x-adminlte-button label="Download Selected" id='download_selected' theme="primary"
                                icon="fas fa-download" class="btn-sm ml-2" />
                            <x-adminlte-button label="Print Selected" target="_blank" id='print_selected'
                                theme="primary" icon="fas fa-print" class="btn-sm ml-2" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="showTable" class="d-none">
        <table class='table table-bordered table-striped text-center'>
            <thead>
                <tr class='text-bold bg-info'>
                    <th>Selected All <br><input type='checkbox' id='selectAll' /></th>
                    <th>Store Name</th>
                    <th>Order No.</th>
                    <th>Awb No.</th>
                    <th>Order Date</th>
                    <th>SKU</th>
                    <th>Customer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='checkTable'>

            </tbody>
        </table>

    </div>

@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            // begin search label
            $('#Searchbox').on('keyup', function() {

                let order_no = $.trim($(this).val());
                let order_no_replace = order_no.replaceAll(/-/g, '_');
                let tr = $('.' + order_no_replace);
                let table = $('#checkTable');

                $(tr.children().children()[0]).prop('checked', true);
                $(tr).addClass('bg-warning');
                tr.prependTo(table);
            });
            // end search label

            function isJsonString(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }

            $('#SearchByDate').click(function() {
                if (($('#bag_no').val() == '')) {
                    alert('Please Input Bag No.');
                } else {

                    $('#showTable').removeClass('d-none');
                    let label_date = $('#bag_no').val();
                    // alert(label_date);
                    $.ajax({
                        method: 'POST',
                        url: "{{ url('/label/select-label') }}",
                        data: {
                            "bag_no": label_date,
                            "_token": "{{ csrf_token() }}",
                        },
                        response: 'json',
                        success: function(response) {
                            // console.log(response);
                            let table = '';

                            $.each(response, function(i, response) {
                                // alert(response);
                                let label_id = response.order_no.replaceAll(/-/g, '_');
                                let change_date = moment(response.purchase_date,
                                        'YYYY-MM-DD ')
                                    .format('YYYY-MM-DD');

                                table += "<tr class='" + label_id + "'>";

                                let t = isJsonString(response.shipping_address) ? JSON
                                    .parse(response.shipping_address) : null;
                                let t_name = (t) ? t['Name'] : '';
                                if (t_name != '') {

                                    table +=
                                        "<td><input class='check_options' type='checkbox' value=" +
                                        response.id + " name='options[]' id='checkid" +
                                        response
                                        .id + "'></td>";
                                } else {

                                    table += "<td>  </td>"
                                }

                                table += "<td>" + response.store_name + "</td><td>" +
                                    response
                                    .order_no + "</td>";

                                table += "<td>" + response.awb_no + "</td><td>" +
                                    change_date + "</td><td>" + response.seller_sku +
                                    "</td><td>" + t_name + "</td>";
                                if (t_name != '') {
                                    table +=
                                        "<td><div class='d-flex'><a href=/label/pdf-template/" +
                                        response.id +
                                        " class='edit btn btn-success btn-sm' target='_blank'><i class='fas fa-eye'></i> View </a><div class='d-flex pl-2'><a href=/label/download-direct/" +
                                        response.id +
                                        "  class='edit btn btn-info btn-sm'><i class='fas fa-download'></i> Download </a>";

                                    table +=
                                        "<div class='d-flex pl-2'><a id='edit-address' data-toggle='modal' data-id=" +
                                            response.order_item_identifier +
                                        " data-amazon_order_identifier=" + response.order_no +" href='javascript:void(0)' class='edit btn btn-secondary btn-sm'><i class='fas fa-address-card'></i> Address </a></td></tr>"


                                } else {

                                    table += "<td> ";
                                        table +=
                                        "<div class='d-flex'><a id='edit-address' data-toggle='modal' data-id=" +
                                            response.order_item_identifier +
                                        " data-amazon_order_identifier=" + response.order_no +" href='javascript:void(0)' class='edit btn btn-secondary btn-sm '><i class='fas fa-address-card'></i> Address </a></div>"
                                    table += "</td></tr>";
                                }
                            });
                            $('#checkTable').html(table);
                            // alert('Export pdf successfully');
                        }
                    });
                }
                // <td>Invoice No.</td><td>Invoice Date</td><td>Channel</td><td>Shipped By</td><td>Awb No</td><td>Arn NO.</td><td>Hsn Code</td><td>Quantity</td><td>Product Price</td><td class='text-center'>Action</td></tr></thead><tbody>
            });

            $('#selectAll').change(function() {
                if ($('#selectAll').is(':checked')) {

                    $('.check_options').prop('checked', true);
                } else {
                    $('.check_options').prop('checked', false);

                }
            });

            $('#print_selected').click(function() {
                // alert('working');
                let id = '';
                let count = '';
                $("input[name='options[]']:checked").each(function() {
                    if (count == 0) {
                        id += $(this).val();
                    } else {
                        id += '-' + $(this).val();
                    }
                    count++;
                    window.location.href = '/label/print-selected/' + id;
                });
                // alert(id);
            });

            $('#download_selected').click(function() {
                alert('Label is downloading please wait.');
                let id = '';
                let count = '';
                let arr = '';
                $("input[name='options[]']:checked").each(function() {
                    if (count == 0) {
                        id += $(this).val();
                    } else {
                        id += '-' + $(this).val();
                    }
                    count++;
                });
                // alert(id);
                $.ajax({
                    method: 'POST',
                    url: "{{ url('/label/select-download') }}",
                    data: {
                        'id': id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        // arr += response;
                        // window.location.href = '/label/zip-download/' + arr;
                        // alert('Export pdf successfully');
                    }
                });
            });


            $('#checkTable').on('click', '#edit-address', function() {                 

                var order_item_identifier = $(this).data('id'); 
                var amazon_order_identifier = $(this).data('amazon_order_identifier');   
                loadOrderAddressFormFunction( order_item_identifier,amazon_order_identifier);
                  
                $('#danger').hide();
                $('#success').hide();
            });

            
            function loadOrderAddressFormFunction( order_item_identifier,amazon_order_identifier )
            {                
                
                $('#form-content').hide();
                $('#spinner-container').show();
                $.get('edit-order-address/'+order_item_identifier+'', function (data) {
                    
                    
                    $('#order_item_identifier').val(order_item_identifier);
                    $('#amazon_order_identifier').val(amazon_order_identifier);
                    $('#name').val(data.Name);
                    $('#phone').val(data.Phone);
                    $('#county').val(data.County);
                    $('#countryCode').val(data.CountryCode);
                    $('#city').val(data.City);
                    $('#addressType').val(data.AddressType);
                    $('#addressLine1').val(data.AddressLine1);
                    $('#addressLine2').val(data.AddressLine2);

                    setTimeout(function (){                        
                        $('#form-content').show();
                        $('#spinner-container').hide();                                
                    }, 500); // How long you want the delay to be, measured in milliseconds.

                })                                
                $('#crud-modal').modal('show');
            }

            $("#orderAddressForm").submit(function()
            {
                var order_item_identifier = $('#order_item_identifier').val();
                var amazon_order_identifier = $('#amazon_order_identifier').val();
                
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    });
                $('#btn-update-order').html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Please wait");
                $("#btn-update-order"). attr("disabled", true);
                $.ajax({
                            url: "update-order-address/"+amazon_order_identifier,
                            type: "PUT",
                            data: $('#orderAddressForm').serialize(),
                                success: function( response ) {
                                    if (response.status == 400) 
                                    {
                                        $('#success').hide();
                                        $('#danger').hide();
                                        var errors = '<ul>'
                                        $.each(response.errors,function(key, err_values){
                                             errors +=  '<li>'+err_values+'</li>';
                                        });
                                        errors += '</ul>'
                                        
                                        $(
                                            `<div id="danger" class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong> Validation Failed!</strong> 
                                                `+errors+`
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>`
                                        ).insertAfter("#warning");
                                    }
                                    else if(response.status == 200)
                                    {
                                        $('#danger').hide();
                                        $('#success').hide();
                                        $(
                                            `<div id="success" class="alert alert-success alert-dismissible fade show" role="alert">
                                                <strong>Updated!</strong> Thanks ....
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>`
                                        ).insertAfter("#warning");
                                        
                                        // closing the modal after form update
                                        setTimeout(function (){ 
                                            $('#SearchByDate').click();                       
                                            $('#crud-modal').modal('hide');                               
                                            }, 1000); // How long you want the delay to be, measured in milliseconds.
                                                
                                    }
                                    loadOrderAddressFormFunction( order_item_identifier,amazon_order_identifier );                                    
                                    $("#btn-update-order"). attr("disabled", false);
                                    $('#btn-update-order').html("Update");
                                    
                                    
                                    
                                }
                            });
            });            

            $('#closemodal').click(function() {
                $('#crud-modal').modal('hide');
            });  
        });

        error=false        
        function validate()
        {
            // document.orderAddressForm.btnsave.disabled=false;
            if(document.orderAddressForm.name.value !='' && document.orderAddressForm.phone.value !='')
            {
                // document.orderAddressForm.btnsave.disabled=false;
            }
            else
            {
                // document.orderAddressForm.btnsave.disabled=true;
            }
        }


    </script>
    
   

@stop
