<!-- <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">...</div>
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div>
</div> -->

<div class="modal fade " id="crud-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="customerCrudModal">Order Address Details Editer</h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div id="spinner-container" class="spinner-border justify-content-center" role="status">
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

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                                type="button" role="tab" aria-controls="home" aria-selected="true">
                                Address Edit
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                                type="button" role="tab" aria-controls="profile" aria-selected="false">
                                Tracking Details Edit
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                                type="button" role="tab" aria-controls="contact" aria-selected="false">
                                Quantity Edit
                            </button>
                        </li>
                    </ul>

                    <form name="orderAddressForm" id="orderAddressForm" method="POST" action="javascript:void(0)">
                        <input type="hidden" name="order_item_identifier" id="order_item_identifier">
                        <input type="hidden" name="amazon_order_identifier" id="amazon_order_identifier">
                        @csrf
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                aria-labelledby="home-tab">
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
                                            <input type="text" name="countryCode" id="countryCode"
                                                class="form-control" placeholder="CountryCode" onchange="validate()">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>AddressType:</strong>
                                            <input type="text" name="addressType" id="addressType"
                                                class="form-control" placeholder="AddressType" onchange="validate()">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>AddressLine1:</strong>
                                            <textarea name="addressLine1" id="addressLine1" class="form-control" placeholder="AddressLine1"
                                                onchange="validate()"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>AddressLine2:</strong>
                                            <textarea name="addressLine2" id="addressLine2" class="form-control" placeholder="AddressLine2"
                                                onchange="validate()"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <strong>Forwarder:</strong>
                                            <input name="forwarder" id="forwarder" class="form-control"
                                                placeholder="Forwarder" onchange="validate()"></input>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <strong>Tracking Id:</strong>
                                            <input name="tracking_id" id="tracking_id" class="form-control"
                                                placeholder="Trackin Id" onchange="validate()"></input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                <div id="qty">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <button type="submit" id="btn-update-order" name="btnsave"
                                    class="btn btn-primary">Update</button>
                                <a id="closemodal" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).on('click', '#edit-address', function() {
        var order_item_identifier = $(this).data('id');
        var amazon_order_identifier = $(this).data('amazon_order_identifier');
        let tracking_id = $(this).closest('tr').find('td:eq(3)').text();
        let forwarder = $(this).closest('tr').find('td:eq(4)').text();

        loadOrderAddressFormFunction(order_item_identifier, amazon_order_identifier, tracking_id, forwarder);

        $('#danger').hide();
        $('#success').hide();
    });

    function loadOrderAddressFormFunction(order_item_identifier, amazon_order_identifier, tracking_id, forwarder) {

        $('#form-content').hide();
        $('#spinner-container').show();
        $.get('/label/edit-order-address/' + amazon_order_identifier + '', function(details) {

            let data = details.address;
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
            $('#tracking_id').val(tracking_id);
            $('#forwarder').val(forwarder);

            setTimeout(function() {
                $('#form-content').show();
                $('#spinner-container').hide();
            }, 500); // How long you want the delay to be, measured in milliseconds.

            let qty_detials = details.qty;
            let sku = (details.sku);
            let product_name = details.title;

            let dom = '<strong>Order Id: </strong>' + amazon_order_identifier;
            $.each(qty_detials, function(index, value) {

                let order_item_id = value.order_item_id;
                let order_id = value.order_no;
                let qty = value.qty;

                dom += "<div class='row'>";
                dom += "<div class = 'col' >";
                dom += "<div class = 'form-group' > ";
                dom += "<strong> Product Name: </strong> ";
                dom += "<input name = 'title[" + order_item_id +
                    "]' id = ''  class = 'form-control' value = '" +
                    product_name[order_item_id] + "'></input >";
                dom += "</div></div></div>";

                dom += "<div class='row'>";
                dom += "<div class = 'col' >";
                dom += "<div class = 'form-group' > ";
                dom += "<strong> Order Item Id: </strong> ";
                dom += "<input name = '' id = '' readonly='' class = 'form-control' value = '" +
                    order_item_id + "'></input >";
                dom += "</div></div>";

                dom += "<div class = 'col'>";
                dom += "<div class = 'form-group' > ";
                dom += "<strong> SKU: </strong> ";
                dom +=
                    "<input name = 'sku' id = '' disabled='disabled' class = 'form-control' value ='" +
                    sku[order_item_id] + "'></input >";
                dom += "</div></div>";

                dom += "<div class = 'col'>";
                dom += "<div class = 'form-group'>";
                dom += "<strong> Quantity: </strong>";
                dom += "<input name = 'qty[" + order_item_id +
                    "]' id = '' class = 'form-control' value ='" + qty + "'></input >";
                dom += "</div></div>";
                dom += "</div>";
            });

            $('#qty').html(dom);
        })

        $('#crud-modal').modal('show');
    }

    $("#orderAddressForm").submit(function() {
        var order_item_identifier = $('#order_item_identifier').val();
        var amazon_order_identifier = $('#amazon_order_identifier').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#btn-update-order').html(
            "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Please wait"
        );
        $("#btn-update-order").attr("disabled", true);
        $.ajax({
            url: "/label/update-order-address/" + amazon_order_identifier,
            type: "PUT",
            data: $('#orderAddressForm').serialize(),
            success: function(response) {
                if (response.status == 400) {
                    $('#success').hide();
                    $('#danger').hide();
                    var errors = '<ul>'
                    $.each(response.errors, function(key, err_values) {
                        errors += '<li>' + err_values + '</li>';
                    });
                    errors += '</ul>'

                    $(
                        `<div id="danger" class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong> Validation Failed!</strong> 
                                    ` + errors + `
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>`
                    ).insertAfter("#warning");
                } else if (response.status == 200) {
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
                    setTimeout(function() {
                            $('#SearchByDate').click();
                            $('#crud-modal').modal('hide');
                        },
                        1000
                    ); // How long you want the delay to be, measured in milliseconds.

                }
                loadOrderAddressFormFunction(order_item_identifier,
                    amazon_order_identifier);
                $("#btn-update-order").attr("disabled", false);
                $('#btn-update-order').html("Update");
            }
        });
    });

    $('#closemodal').click(function() {
        $('#crud-modal').modal('hide');
    });

    error = false

    function validate() {
        // document.orderAddressForm.btnsave.disabled=false;
        if (document.orderAddressForm.name.value != '' && document.orderAddressForm.phone.value != '') {
            // document.orderAddressForm.btnsave.disabled=false;
        } else {
            // document.orderAddressForm.btnsave.disabled=true;
        }
    }
</script>
