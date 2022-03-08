@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark"> Download Files</h1>
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
                <form class="container">
                    <li>
                        <ul>    
                            <a href="{{route('download.universalTextiles')}}">
                                Download Universal Textils
                            </a>
                        </ul>
                        <ul>
                            <a href="{{route('download.asinMaster')}}">
                                Download Asin Master
                            </a>
                        </ul>
                        <ul>
                            <a href="other-product/download/0">
                                Download Amazon other Product 
                            </a>
                            <!-- <select id='download_amazon_product' >
                                <option>--Select--</option>
                                <option value="0"> Download Amazon other Product 0</option>
                            </select> -->
                        </ul>
                    </li>
                </form>
        </div>
    </div>

@stop

@section('js')
<script type="text/javascript">
$(function(){
//     $(document).on('change', '#download_amazon_product', function(){
//         // alert($('#download_amazon_product').val());
//         $.ajax({
//             method: 'get',
//             url: 'other-product/download/0',
//             data:{
//                 "_token": "{{ csrf_token() }}",
//                 "_method": 'get',
//             },
//             success: function() {
                
//                 // yajra_table.ajax.reload();
                
//             }
//         })      
// // alert('change');
// });
});
</script>
   
@stop