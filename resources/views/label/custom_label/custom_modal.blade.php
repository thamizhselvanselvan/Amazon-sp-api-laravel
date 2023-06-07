<div id="custom_label" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Custom Label</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('custom.label.print') }}" method="POST" target="_blank">
                    @csrf
                    <p>Check whatever you want to print.</p>
                    <hr>
                    <div id="checkbox">

                    </div>

                    <div class="row mt-4">

                        <button type="submit" class="btn btn-success btn-sm" value="submit" id="print"><i
                                class="fas fa-print "> Print </i></button>
                    </div>

                </form>
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

            let input = "<div class='mt-4'><input type='hidden' name='order_identifier' value='" + records[0]
                .order_no + "'>";

            console.log(records[0].seller_sku);
            console.log(records[0].qty);
            console.log(records[0].title);

            let seller_sku = records[0].seller_sku.split('-label-sku-');
            let quantity = records[0].qty.split('-label-qty-')
            let product_title = records[0].title.split('-label-title-');

            console.log(seller_sku);
            console.log(quantity);
            console.log(product_title);

            $.each(product_title, function(index, product_name) {

                input +=
                    " <div class='mt-4 mr-4'><input type='checkbox' name='custom_label[]' class='options' value='" +
                    seller_sku[index] + "' >&nbsp;" + '<strong>SKU:</strong> ' +
                    seller_sku[index] + ', &nbsp; <strong>Order ID:</strong> ' + records[0].order_no +
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
</script>
