@extends('adminlte::page')
@section('title', 'Packet Details')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark"> Bombino Packet Activities</h1>
</div>
@stop

@section('css')
<style>
    .table td {
        padding: 0.1rem;
    }
</style>
@stop

@section('content')


<table class="table table-bordered yajra-datatable table-striped" style="font-size:12px;">

    <!-- <thead>
        <tr>
            <td><strong>Today</strong></td>
            <td><strong>Yesterday</strong></td>
            <td><strong>Last 7 days</strong></td>
            <td><strong>Last 30 days</strong></td>
        </tr>
    </thead> -->
    <tbody>
        @foreach ($pd_final_array as $values )
        <tr>
            @foreach ($values as  $value)
            <td>
                 {{$value}}
            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

@stop

@section('js')
<script type="text/javascript">
$(document).ready(function() {
    $('.yajra-datatable').DataTable( {
        "pagingType": "full_numbers"
    } );
} );
</script>

@stop