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

                    <a href="{{route('export.other-product')}}">
                        <x-adminlte-button label="Product Export" theme="primary" icon="fas fa-file-export"/>
                    </a>
                   <button type="button" class="btn btn-success file_download_modal_btn">
                      Download
                    </button>
                     </h2>
<div class="modal fade" id="file_download_modal" tabindex="-1" role="dialog" aria-labelledby="FileDownloadModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Download Amazon Other Products</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
           
        <div class="file_download_display">
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       
      </div>
    </div>
  </div>
</div>
             
               <!--end model-->


                <table class="table table-bordered yajra-datatable table-striped">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>hit</th>
                            <th>ASIN</th>
                            <th>SKU</th>
                            <th>hs_code / gst  </th>
                            <th>update_time </th>
                            <th>Availability</th> 
                            <th>price</th>
                            <th>List Price</th>
                            <th>price1 /  Price INR</th>
                            <th>List Price INR</th>
                            <th>price_aed </th>
                            <th>list_price_aed   </th>
                            <th>shipping_weight </th>
                            <th>image_t </th>
                            <th>ID</th>
                            <th>Title</th>
                            <th>image_p / image_d</th>
                            <th>category  </th>
                            <th>All category </th>
                            <th>Description </th>
                            <th>height /length / width </th>
                            <th>Weight</th>
                            <th>Flipkart / Amazon</th>
                            <th>upc</th>
                            <th>Manufacturer</th>
                            <th>latency</th>
                            <th>uae latency / b2c latancy</th>
                            <th>ean</th>
                            <th>color</th>
                            <th>model / mpn</th>
                            <th>Detail page url</th>
                            <th> creation time</th>
                            <th>page</th>

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

$(".file_download_modal_btn").on('click', function(e) {

    let self = $(this);
    let file_display = $('.file_download_display');
    let file_modal = $("#file_download_modal");
    
    $.ajax({
        url: "/other_file_download",
        method: 'GET',
        dataType: 'json',
        success: function(response) {

            if(response.error) {
                alert('Error');
            }

            if(response.success) {
                file_modal.modal('show');

               let html = '<ul>';

                $.each(response.files_lists, function(index, value) {

                    let file_name = Object.keys(value)[0];
                    let file_time = value[file_name];

                    html += "<li class='p-0 m-0'>";
                    html += "<a href='/other-product/download/"+file_name+"' class='p-0 m-0'> Part "+ index +1 +"</a> ";
                    html += file_time;
                    html += "</li>";

                });

                html += '</ul>';

                file_display.html(html);
            }

        }
    });

    
});

let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
           
            ajax: "{{ url('other-product/amazon_com') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'hit' ,name: 'hit'},
                {data: 'asin', name: 'asin'},
                {data: 'sku', name: 'sku'},

                {data: 'hs_code_gst', name:'hs_code_gst', orderable :false, searchable: false},

                {data: 'update_time', name: 'update_time'},
                {data: 'availability', name: 'availability'},
                {data: 'price',name: 'price'},
                {data: 'list_price', name: 'list_price'},

                {data: 'price1_price_inr', name: 'price1_price_inr',orderable: false,searchable:false},
               
                {data: 'list_price_inr', name: 'list_price_inr'}, 
                {data: 'price_aed', name: 'price_aed'},
                {data: 'list_price_aed', name: 'list price_aed'},
                {data: 'shipping_weight', name: 'shipping_weight'},
                {data: 'image_t', name: 'image_t'},
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'image_p_image_d', name: 'image_p_image_d', orderable: false, searchable: false},
                {data: 'category', name: 'category'},
                {data: 'all_category', name: 'all_categorv'},
                {data: 'description', name: 'description'},
                {data: 'height_length_width', name: 'height_length_width', orderable: false,searchable :false},
                {data: 'weight', name: 'weight'},
                {data: 'flipkart_amazon', name: 'flipkart_amazon', orderable: false, searchable: false},
                {data: 'upc', name: 'upc'},
                {data: 'manufacturer', name: 'manufacturer'},
                {data: 'latency', name: 'latency'},
                {data: 'uae_latency_b2c_latency', name: 'uae_latency_b2c_latency',orderable:false,searchable:false},
                {data: 'ean', name: 'ean'},
                {data: 'color', name: 'color'},
                {data: 'model_mpn', name: 'model_mpn', orderable: false, searchable: false},
                {data: 'detail_page_url', name: 'detail_page_url'},
                {data: 'creation_time', name: 'creation_time'},
                {data: 'page', name: 'page'},
  ]
        });
     
</script>   
@stop