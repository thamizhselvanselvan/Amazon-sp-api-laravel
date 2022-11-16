@extends('adminlte::page')
@section('title', 'Orders  Dashboard')

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
    </div>
</div>


<div class="row justify-content-center mt-4">
    <div class="col-4">
        <div class="form-group">
            <label>Enter Amazon Order id:</label>
            <div class="autocomplete">
                <textarea name="upload_orders" rows="5" placeholder="Enter Amazon Order id ..." type=" text" autocomplete="off" class="form-control amazon_order_id"></textarea>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-4 text-center">
        {{-- <x-adminlte-button label="Search" theme="primary"  icon=" "  /> --}}

        <button class="btn btn-primary" type="button" id="search" type="button">
            <icon class="fas fa-search"></icon> Search
        </button>
    </div>
</div>



<div class="modal fade" id="zoho_lead_display_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header justify-content-center pt-2 pb-2">
          <h5 class="modal-title font-weight-bold" id="exampleModalLabel">Zoho Order Preview</h5>
        </div>
        <div class="modal-body">

            <table class="table table-striped">
                <tr class="pt-2 pb-2">
                    <td colspan="4" class="pt-0 pb-0 text-center">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Title</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 text-break Designation "></p>
                    </td>
                </tr>
                <tr class="pt-2 pb-2">
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Amazon Order Id</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 text-break Alternate_Order_No"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Order Item Id</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Payment_Reference_Number"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">ASIN</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 ASIN"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">SKU</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 SKU"></p>
                    </td>
                </tr>
                <tr class="pt-2 pb-2">
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Lead Name</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 text-break Last_Name"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Lead Source</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 text-break Lead_Source"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Lead Follow Up Status</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 text-break Follow_up_Status"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Lead Status</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 text-break Lead_Status"></p>
                    </td>
                </tr>
                <tr class="pt-2 pb-2">
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Mobile</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Mobile"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Email</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 text-break Email"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Customer_Type</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Customer_Type"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Product_Code</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Product_Code"></p>
                    </td>
                </tr>
                <tr class="pt-2 pb-2">
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Address</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Address"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">City</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 City"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">State</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 State"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Zip_Code</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Zip_Code"></p>
                    </td>
                </tr>
                <tr class="pt-2 pb-2">
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Product_Cost</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Product_Cost"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Product_Category</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Product_Category"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Fulfilment_Channel</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Fulfilment_Channel"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Exchange</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Exchange"></p>
                    </td>
                </tr>
                <tr class="pt-2 pb-2">
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Order_Creation_Date</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Order_Creation_Date"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Weight_in_LBS</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Weight_in_LBS"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Nature</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Nature"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Quantity</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Quantity"></p>
                    </td>
                </tr>
                <tr class="pt-2 pb-2">
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Procurement_URL</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 text-break Procurement_URL"></p>
                    </td>
                    <td class="pt-0 pb-0">
                        <p class="pt-0 pb-0 mt-1 mb-0 font-weight-bold">Product_Link</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 text-break Product_Link"></p>
                    </td>
                    <td class="pt-0 pb-0 font-weight-bold">
                        <p class="pt-0 pb-0 mt-1 mb-0">US_EDD</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 US_EDD"></p>
                    </td>
                    <td class="pt-0 pb-0 font-weight-bold">
                        <p class="pt-0 pb-0 mt-1 mb-0">Amount_Paid_by_Customer</p>
                        <p class="pt-0 pb-0 mt-0 mb-1 Amount_Paid_by_Customer"></p>
                    </td>
                </tr>
            </table>

            <div class="alert alert-warning note_1 d-none"></div>
            <div class="alert alert-warning note_2 d-none"></div>
            <div class="alert alert-warning notes d-none"></div>
            
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-primary zoho_save">Save changes</button>
        </div>
      </div>
    </div>
  </div>

@stop

@section('js')

    <script>

        $(document).ready(function () {

            $('#zoho_lead_display_modal').on('hidden.bs.modal', function () {

                $(".Order_Creation_Date").html('');
                $(".Alternate_Order_No").html('');
                $(".Follow_up_Status").html('');
                $(".Last_Name").html('');
                $(".Lead_Source").html('');
                $(".Lead_Status").html('');
                $(".Mobile").html('');
                $(".Address").html('');
                $(".City").html('');
                $(".State").html('');
                $(".Zip_Code").html('');
                $(".Email").html('');
                $(".Customer_Type").html('');
                $(".Fulfilment_Channel").html('');
                $(".Designation").html('');
                $(".Product_Code").html('');
                $(".Product_Cost").html('');
                $(".Procurement_URL").html('');
                $(".Nature").html('');
                $(".Product_Category").html('');
                $(".Quantity").html('');
                $(".Product_Link").html('');
                $(".US_EDD").html('');
                $(".ASIN").html('');
                $(".SKU").html('');
                $(".Product_Cost").html('');
                $(".Weight_in_LBS").html('');
                $(".Payment_Reference_Number").html('');
                $(".Exchange").html('');
                $(".Amount_Paid_by_Customer").html('');

                $(".note_1").addClass('d-none').removeClass('d-block').html('');
                $(".note_2").addClass('d-none').removeClass('d-block').html('');
                $(".notes").addClass('d-none').removeClass('d-block').html('');
                
            });

            $(".zoho_save").on('click', function() {
                let self = $(this);
                let btn_content = 'Save changes';
                let amazon_order_id = $(".amazon_order_id").val();
                let modal = $("#zoho_lead_display_modal");

                self.prop('disabled', true);
                self.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> saving...');

                if(amazon_order_id.length < 10) {
                    self.prop('disabled', false);
                    self.html(btn_content);
                    alert("Please enter valid Amazon order id");
                    return false;
                }   

                $.ajax({
                    url: "/zoho/save",
                    method: "GET",
                    dataType: 'json',
                    data: {'amazon_order_id': amazon_order_id},
                    success: function(response) {
                        self.prop('disabled', false);
                        self.html(btn_content);

                        console.log(response);

                        if(response.hasOwnProperty('success')) {
                            alert("Zoho Created Successfully for Amazon Order ID "+ amazon_order_id);
                            modal.modal('hide');
                            return true;
                        }

                        if(response.hasOwnProperty('notes')) {
                            let html = '';
                            $.each(response.notes, function(index, elem) {
                                html += elem + " \n";
                            });
                     
                            $(".notes").addClass('d-block').removeClass('d-none').html(html);
                        } else {
                            $(".notes").addClass('d-none').removeClass('d-block').html('');
                        }

                    },
                    error: function(response) {
                        self.prop('disabled', false);
                        self.html(btn_content);
                    }
                });

            });

            $("#search").on("click", function() {
                let self = $(this);
                let btn_content = '<icon class="fas fa-search"></icon> Search';
                let amazon_order_id = $(".amazon_order_id").val();
                let modal = $("#zoho_lead_display_modal");

                self.prop('disabled', true);
                self.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

                if(amazon_order_id.length < 10) {
                    self.prop('disabled', false);
                    self.html(btn_content);
                    alert("Please enter valid Amazon order id");
                    return false;
                }              
            
                $.ajax({
                    url: "/zoho/preview",
                    method: "GET",
                    dataType: 'json',
                    data: {'amazon_order_id': amazon_order_id},
                    success: function(response) {
                        self.prop('disabled', false);
                        self.html(btn_content);

                        console.log(response);

                        if(response.hasOwnProperty('data')) {

                            $(".Order_Creation_Date").html(response.data.Order_Creation_Date);
                            $(".Alternate_Order_No").html(response.data.Alternate_Order_No);
                            $(".Follow_up_Status").html(response.data.Follow_up_Status);
                            $(".Last_Name").html(response.data.Last_Name);
                            $(".Lead_Source").html(response.data.Lead_Source);
                            $(".Lead_Status").html(response.data.Lead_Status);
                            $(".Mobile").html(response.data.Mobile);
                            $(".Address").html(response.data.Address);
                            $(".City").html(response.data.City);
                            $(".State").html(response.data.State);
                            $(".Zip_Code").html(response.data.Zip_Code);
                            $(".Email").html(response.data.Email);
                            $(".Customer_Type").html(response.data.Customer_Type);
                            $(".Fulfilment_Channel").html(response.data.Fulfilment_Channel);
                            $(".Designation").html(response.data.Designation);
                            $(".Product_Code").html(response.data.Product_Code);
                            $(".Product_Cost").html(response.data.Product_Cost);
                            $(".Procurement_URL").html(response.data.Procurement_URL);
                            $(".Nature").html(response.data.Nature);
                            $(".Product_Category").html(response.data.Product_Category);
                            $(".Quantity").html(response.data.Quantity);
                            $(".Product_Link").html(response.data.Product_Link);
                            $(".US_EDD").html(response.data.US_EDD);
                            $(".ASIN").html(response.data.ASIN);
                            $(".SKU").html(response.data.SKU);
                            $(".Product_Cost").html(response.data.Product_Cost);
                            $(".Weight_in_LBS").html(response.data.Weight_in_LBS);
                            $(".Payment_Reference_Number").html(response.data.Payment_Reference_Number);
                            $(".Exchange").html(response.data.Exchange);
                            $(".Amount_Paid_by_Customer").html(response.data.Amount_Paid_by_Customer);

                            modal.modal('show');
                        } else {
                            
                            $(".Order_Creation_Date").html('');
                            $(".Alternate_Order_No").html('');
                            $(".Follow_up_Status").html('');
                            $(".Last_Name").html('');
                            $(".Lead_Source").html('');
                            $(".Lead_Status").html('');
                            $(".Mobile").html('');
                            $(".Address").html('');
                            $(".City").html('');
                            $(".State").html('');
                            $(".Zip_Code").html('');
                            $(".Email").html('');
                            $(".Customer_Type").html('');
                            $(".Fulfilment_Channel").html('');
                            $(".Designation").html('');
                            $(".Product_Code").html('');
                            $(".Product_Cost").html('');
                            $(".Procurement_URL").html('');
                            $(".Nature").html('');
                            $(".Product_Category").html('');
                            $(".Quantity").html('');
                            $(".Product_Link").html('');
                            $(".US_EDD").html('');
                            $(".ASIN").html('');
                            $(".SKU").html('');
                            $(".Product_Cost").html('');
                            $(".Weight_in_LBS").html('');
                            $(".Payment_Reference_Number").html('');
                            $(".Exchange").html('');
                            $(".Amount_Paid_by_Customer").html('');

                            $(".notes").addClass('d-none').removeClass('d-block').html('');
                        }

                        if(response.hasOwnProperty('note_1')) {
                            $(".note_1").addClass('d-block').removeClass('d-none').html(response.note_1);
                        } else {
                            $(".note_1").addClass('d-none').removeClass('d-block').html('');
                        }

                        if(response.hasOwnProperty('note_2')) {
                            $(".note_2").addClass('d-block').removeClass('d-none').html(response.note_2);
                        } else {
                            $(".note_2").addClass('d-none').removeClass('d-block').html('');
                        }

                        if(response.hasOwnProperty('notes')) {
                            $(".notes").addClass('d-block').removeClass('d-none').html(response.notes);
                        } else {
                            $(".notes").addClass('d-none').removeClass('d-block').html('');
                        }                        

                    },
                    error: function(response) {
                        self.prop('disabled', false);
                        self.html(btn_content);

                    }
                });
            });
        });

    </script>

@stop