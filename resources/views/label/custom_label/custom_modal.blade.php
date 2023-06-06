<div id="custom_label" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
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

                        <button type="submit" class="btn btn-success btn-sm" value="submit"
                            id="print">Print</button>
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
            let input = "<div class='row'><input type='hidden' name='order_identifier' value='" + records[0]
                .order_no + "'>";
            let seller_sku = records[0].seller_sku.split(',');

            $.each(seller_sku, function(index, sku) {

                input +=
                    "<div class='col-6'> <input type='checkbox' name='custom_label[]' class='options' value='" +
                    sku +
                    "' >&nbsp;" +
                    sku;
                input += "</div>";
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
