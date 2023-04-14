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

                    <form action="{{ route('shipntrack.label.edit') }}" method="POST" action="javascript:void(0)">
                        <input type="hidden" name="id" id="id">
                        @csrf
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                aria-labelledby="home-tab">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Name:</strong>
                                            <input type="text" name="name" id="Name" class="form-control"
                                                placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <strong>Phone:</strong>
                                            <input type="text" name="phone" id="Phone" class="form-control"
                                                placeholder="Phone">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <strong>City:</strong>
                                            <input type="text" name="city" id="City" class="form-control"
                                                placeholder="City">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <strong>County:</strong>
                                            <input type="text" name="county" id="County" class="form-control"
                                                placeholder="County">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <strong>Country:</strong>
                                            <input type="text" name="country" id="Country" class="form-control"
                                                placeholder="Country">
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>AddressLine1:</strong>
                                            <textarea name="addressLine1" id="AddressLine1" class="form-control" placeholder="AddressLine1"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <strong>Forwarder:</strong>
                                            <input name="forwarder" id="Forwarder" class="form-control"
                                                placeholder="Forwarder"></input>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <strong>Tracking Id:</strong>
                                            <input name="tracking_id" id="Tracking_id" class="form-control"
                                                placeholder="Trackin Id"></input>
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
        var id = $(this).data('id');

        FetchShipntrackData(id);
    });

    function FetchShipntrackData(id) {

        $.get('/shipntrack/label/fetch/record/' + id + '', function(details) {

            var result = details[0];
            $('#id').val(id);
            $('#Name').val(result.customer_name);
            $('#Phone').val(result.phone);
            $('#City').val(result.city);
            $('#County').val(result.county);
            $('#Country').val(result.country);
            $('#AddressLine1').val(result.address);
            $('#Forwarder').val(result.forwarder);
            $('#Tracking_id').val(result.awb_no);

            setTimeout(function() {
                $('#form-content').show();
                $('#spinner-container').hide();
            }, 300); // How long you want the delay to be, measured in milliseconds.

            var order_item_identifier = result.order_item_id;
            var order_item_arrays = order_item_identifier.split(',');
            let dom = '<strong>Order Id: </strong>' + result.order_no;
            $.each(order_item_arrays, function(index, value) {

                dom += "<div class='row'>";
                dom += "<div class = 'col' >";
                dom += "<div class = 'form-group' > ";
                dom += "<strong> Order Item Id: </strong> ";
                dom += "<input name = '' id = '' readonly='' class = 'form-control' value = '" +
                    value + "'></input >";
                dom += "</div></div>";

                dom += "<div class = 'col'>";
                dom += "<div class = 'form-group' > ";
                dom += "<strong> SKU: </strong> ";
                dom +=
                    "<input name = 'sku' id = '' disabled='disabled' class = 'form-control' value ='" +
                    result.sku[index] + "'></input >";
                dom += "</div></div>";

                dom += "<div class = 'col'>";
                dom += "<div class = 'form-group'>";
                dom += "<strong> Quantity: </strong>";
                dom += "<input name = 'qty[]' id = '' class = 'form-control' value ='" + result
                    .quantity[index] +
                    "'></input >";
                dom += "</div></div>";
                dom += "</div>";
            });

            $('#qty').html(dom);
        });
        $('#crud-modal').modal('show');
    }


    $('#closemodal').click(function() {
        $('#crud-modal').modal('hide');
    });
</script>
