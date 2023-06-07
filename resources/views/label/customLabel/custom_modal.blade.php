<div id="custom_label" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Custom Label</h4>
            </div>
            <div class="modal-body">
                <p>Check whatever you want to print.</p>
                <hr>
                <div id="checkbox">

                </div>

                <div class="row mt-4">

                    <button type="submit" class="btn btn-success btn-sm" value="submit" id="print"><i
                            class="fas fa-print "> Print </i></button>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn sm" id="close-modal">Close</button>
            </div>
        </div>

    </div>
</div>


<script type="text/javascript">
    $(document).on('click', '#custom_print_modal', function() {

        let order_no = $(this).data('order_no');
        getCustomLabelRecords(order_no);

    });

    function getCustomLabelRecords(order_no) {

        $('#checkbox').empty();
        $.get("/label/custom/get/" + order_no, function(records) {

            let input = "<div class='mt-4'><input type='hidden' name='order_identifier' id='order_id' value='" +
                records[0]
                .order_no + "'>";

            console.log(records);
            console.log(records[0].sku);
            console.log(records[0].qty);
            console.log(records[0].title);

            let sku = records[0].sku.split('-label-sku-');
            let quantity = records[0].qty.split('-label-qty-')
            let product_title = records[0].title.split('-label-title-');

            console.log(sku);
            console.log(quantity);
            console.log(product_title);

            $.each(product_title, function(index, product_name) {

                input +=
                    " <div class='mt-4 mr-4'><input type='checkbox' name='custom_label[]'  id='custom_sku' value='" +
                    sku[index] + "' >&nbsp;" + '<strong>SKU:</strong> ' +
                    sku[index] + ', &nbsp; <strong>Order ID:</strong> ' + records[0].order_no +
                    ',&nbsp; <strong>Quantity:</strong> ' +
                    quantity[index] + ', &nbsp; <strong>Product:</strong> ' + ((product_name.length >
                            100) ? product_name.slice(0, 100 - 1) + '...' :
                        product_name);
                input += "</div><br>";
            });

            input += "</div>";
            $("#checkbox").html(input);
        });

        $('#custom_label').modal('show');
    }

    $('#close-modal').click(function() {
        $('#custom_label').modal('hide');
    });


    $('#print').click(function() {

        let order_identifier = $('#order_id').val();
        let sku = '';
        let count = 0;
        $("input[name='custom_label[]']:checked").each(function() {
            if (count == 0) {
                sku += $(this).val();
            } else {
                sku += '-' + $(this).val();
            }
            count++;

        });
        window.open("/label/custom/print/" + order_identifier + "/" + sku, "_blank");

    });
</script>
