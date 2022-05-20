@extends('adminlte::page')

@section('title', 'ASIN Master')

@section('content_header')
<h1 class="m-0 text-dark">Add ASIN</h1>
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

                </h2>
                
                <form class='container'>
    
                </form>
               
                
        </div>
    </div>
@stop

@section('js')
<script type="text/javascript">

let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('master/asin') }}",
            
        });
     
</script>   
@stop