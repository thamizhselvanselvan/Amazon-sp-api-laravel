@extends('adminlte::page')

@section('title', 'Amazon other product')

@section('content_header')
<h1 class="m-0 text-dark">Amazon Other Products</h1>
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
                <h2 class="mb-4">

                    <!-- <a href="">
                        <x-adminlte-button label="Product Export" theme="primary" icon="fas fa-file-export" data-target="#exampleModal"/>
                    </a> -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#productExport">
                       Product Export
                    </button>
             <!-- Modal -->
                    <div class="modal fade" id="productExport" tabindex="-1" role="dialog" aria-labelledby="productExportModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productExportModalLabel">Select Headers</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body ">
                                <div class="form-check d-flex">
                                    <div class="ml-1 mr-3">
                                        <input class="form-check-input all" type="checkbox" value="all" name='options[]' id="all" ><h6>Select All</h6>
                                    </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="hit" name='options[]' id="hit"><h6>Hit</h6>
                                     </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="asin" name='options[]' id="asin"><h6>Asin</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="sku" name='options[]' id="sku"><h6>Sku</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="hs_code" name='options[]' id="hs_code"><h6>HS Code</h6>
                                     </div>
                                </div><hr>
                                <div class="form-check d-flex">
                                    <div class="ml-1 mr-3">
                                        <input class="form-check-input header_options" type="checkbox" value="gst" name='options[]' id="gst"><h6>GST</h6>
                                    </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="update_time" name='options[]' id="update_time"><h6>Update Time</h6>
                                     </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="availability" name='options[]' id="availability"><h6>Availability</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="price" name='options[]' id="price"><h6>Price</h6>
                                     </div>
                                </div><hr>

                                <div class="form-check d-flex">
                                    <div class="ml-1 mr-3">
                                        <input class="form-check-input header_options" type="checkbox" value="list_price" name='options[]' id="list_price"><h6>List Price</h6>
                                    </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="price1" name='options[]' id="price_1"><h6>Price 1</h6>
                                    </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options " type="checkbox" value="price_inr" name='options[]' id="price_inr"><h6>Price INR</h6>
                                     </div>
                                     <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="list_price_inr" name='options[]' id="list_price_inr"><h6>List Price INR</h6>
                                     </div>
                                </div><hr>
                                
                                <div class="form-check d-flex">
                                    <div class="ml-1 mr-3">
                                        <input class="form-check-input header_options" type="checkbox" value="price_aed" name='options[]' id="price_aed"><h6>Price AED</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="list_price_aed" name='options[]' id="list_price_aed"><h6>List Price AED</h6>
                                     </div>
                                     <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="shipping_weight" name='options[]' id="shipping_weight"><h6>Shipping Weight</h6>
                                    </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="image_t" name='options[]' id="image_t"><h6>Image T</h6>
                                     </div>
                                </div><hr>
                                <div class="form-check d-flex">
                                    <div class="ml-1 mr-3">
                                        <input class="form-check-input header_options" type="checkbox" value="id" name='options[]' id="id"><h6>ID</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="title" name='options[]' id="title"><h6>Title</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="image_p" name='options[]' id="image_p"><h6>Image P</h6>
                                    </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="image_d" name='options[]' id="image_d"><h6>Image D</h6>
                                    </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="all_category" name='options[]' id="all_category"><h6>All Category</h6>
                                     </div>
                                </div><hr>
                                <div class="form-check d-flex">
                                    <div class="ml-1 mr-3">
                                        <input class="form-check-input header_options" type="checkbox" value="category" name='options[]' id="category"><h6>Category</h6>
                                     </div>
                                
                                    
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="description" name='options[]' id="description"><h6>Description</h6>
                                     </div>
                                     <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="flipkart" name='options[]' id="flipkart"><h6>Flipkart</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="amazon" name='options[]' id="amazon"><h6>Amazon</h6>
                                     </div>
                                    
                                </div><hr>
                                <div class="form-check d-flex">
                                    <div class="ml-1 mr-3"> 
                                        <input class="form-check-input header_options" type="checkbox" value="length" name='options[]' id="length"><h6>Length</h6>
                                    </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="height" name='options[]' id="height"><h6>Height</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="width" name='options[]' id="width"><h6>Width</h6>
                                     </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="weight" name='options[]' id="weight"><h6>Weight</h6>
                                    </div>
                                    <div class="mx-4">
                                    <input class="form-check-input header_options" type="checkbox" value="upc" name='options[]' id="upc"><h6>UPC</h6>
                                    </div>
                                   
                                </div><hr>
                                <div class="form-check d-flex">
                                    <div class="ml-1 mr-3">
                                        <input class="form-check-input header_options" type="checkbox" value="manufacturer" name='options[]' id="manufacturer"><h6>Manufacturer</h6>
                                     </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="latency" name='options[]' id="latency"><h6>Latency</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="uae_latency" name='options[]' id="uae"><h6>UAE Latency</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="b2c_latency" name='options[]' id="b2c_latency"><h6>B2C Latency</h6>
                                     </div>
                                </div><hr>
                                <div class="form-check d-flex">
                                    <div class="ml-1 mr-3">
                                        <input class="form-check-input header_options" type="checkbox" value="ean" name='options[]' id="ean"><h6>EAN</h6>
                                    </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="color" name='options[]' id="color"><h6>Color</h6>
                                     </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="model" name='options[]' id="model"><h6>Model</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="mpn" name='options[]' id="mpn"><h6>MPN</h6>
                                     </div>
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="detail_page_url" name='options[]' id="detail_page_url"><h6>Detail page URL</h6>
                                     </div>
                                </div><hr>
                                <div class="form-check d-flex">
                                    <div class="ml-1 mr-3">
                                        <input class="form-check-input header_options" type="checkbox" value="creation_time" name='options[]' id="creationg_time"><h6>Creation Time</h6>
                                    </div>
                                
                                    <div class="mx-4">
                                        <input class="form-check-input header_options" type="checkbox" value="page" name='options[]' id="page"><h6>Page</h6>
                                     </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id='exportToCsv'>Export to CSV</button>
                            </div>
                            </div>
                        </div>
                    </div>
            <!--End of Modal -->
                </h2>
               
                <table class="table table-bordered yajra-datatable table-striped">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>ASIN</th>
                            <th>SKU</th>
                            <th>Title</th>
                            <th>List Price</th>
                            <th>Price INR</th>
                            <th>List Price INR</th>
                            <th>Weight</th>
                            <th>Availability</th>
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
$.extend( $.fn.dataTable.defaults, {
                pageLength: 100,
});
          
let yajra_table = $('.yajra-datatable').DataTable({
    
            processing: true,
            serverSide: true,
            ajax: "{{ url('other-product/amazon_com') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'asin', name: 'asin'},
                {data: 'sku', name: 'sku'},
                {data: 'title', name: 'title'},
                {data: 'list_price', name: 'list_price'},
                {data: 'price_inr', name: 'price_inr'},
                {data: 'list_price_inr', name: 'list_price_inr'}, 
                {data: 'weight', name: 'weight'},
                {data: 'availability', name: 'availability'},
                
            ]

        });

    $('.all').change(function()
    {   
        if($('.all').is(':checked')){
            $(".header_options").prop("checked", false);
            $(".header_options").attr("disabled", true);
        }
        else{
            $(".header_options").removeAttr("disabled");
        }
    });

    $('#exportToCsv').on('click', function(){

        let select_header =[] ; let count =0;
        $("input[name='options[]']:checked").each( function () {
            if(count == 0){

                select_header += $(this).val();
            }
            else{
                select_header += '-'+$(this).val();
            }
            count++;
        });
        $.ajax({
                method: 'post',
                url: '/other-product/export',
                data:{
                    "_token": "{{ csrf_token() }}",
                    "_method": 'post',
                    'selected': select_header,
                },
                success: function(response) {
                   
                    // yajra_table.ajax.reload();
                    
                }
            })   
            $('#productExport').modal('hide')  ;
    }).get();
     
</script>   
@stop