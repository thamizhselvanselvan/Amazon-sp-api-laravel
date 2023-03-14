@extends('adminlte::page')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;
    }

    .table th {
        padding: 2;
        padding-left: 5px;
    }
</style>
@stop
@section('title', 'Users Keyword Search Logs')

@section('content_header')
<div class="row">
    <h3>Users Keyword Search Logs</h3>
</div>



@stop

@section('content')
@if (session()->has('success'))
<div class="alert alert-success" role="alert">
    {{ session()->get('success') }}
</div>
@endif
<div class="alert_display">
    @if (request('success'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{request('success')}}</strong>
    </div>
    @endif
</div>

<div class="alert_display">
    @if (request('error'))
    <div class="alert alert-warning alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{request('error')}}</strong>
    </div>
    @endif
</div>

@if (session()->has('error'))
<div class="alert alert-warning" role="alert">
    {{ session()->get('error') }}
</div>
@endif


<div class="alert alert-info">
    <div >
        <p>Clear Search Log Data :) </p>
        <form class="form-inline" id="form-log-delete" method="post" action="{{ route('cliqnshop.keyword.log.delete') }}">
            @csrf
            <div class="form-group">
            
            <select class="form-control form-control-sm" id="select-timeline" name="select_timeline">
                <option>Select the Timeline</option>
                <option value="l-1-h">Last hour</option>
                <option value="l-24-h">Last 24 hour</option>
                <option value="l-7-d">Last 7 days</option>
                <option value="l-4-w">Last 4 weeks</option>
                <option value="all-time">All Time</option>
                </select>
            </div>           
            
            <button type="submit" id="clear_log" class="btn btn-warning mx-2 btn-sm">Clear Log</button>
        </form>
    </div>
</div>


<table class="table table-bordered data-table">
    <thead>
        <tr class="table-info">
            <th>ID</th>
            <th>Search Keyword</th>
            <th>Ip Address</th>
            <th>site_code</th>
            <th>Searched On </th>
          
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>
</div>
@stop

@section('js')
<script type="text/javascript">
    $.extend($.fn.dataTable.defaults, {
        pageLength: 50,
    });

    $(function() {

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('cliqnshop.keyword.log') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },

                // {
                //     data: 'id',
                //     name: 'id'
                // },
                {
                    data: 'search_term',
                    name: 'search_term'
                },
                {
                    data: 'ip_address',
                    name: 'ip_address'
                },
                {
                    data: 'site_code',
                    name: 'site_code'
                },
               
                
                {
                    data: 'created_at',
                    name: 'created_at'
                },
             
                
            ]
        });

    });


    $(document).on('click', "#clear_log", function(e) {
        e.preventDefault();

        let selected = $('#select-timeline').find(":selected").text();
        let bool = confirm(`Are you sure you want to Clear - ${selected} - Logs ?`);

        if(!bool) {
            return false;
        }

        let self = $(this);
        let form = $("#form-log-delete");

        form.submit();

    });

</script>



@stop