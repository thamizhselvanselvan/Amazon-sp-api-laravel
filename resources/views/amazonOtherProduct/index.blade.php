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

                </h2>
               
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