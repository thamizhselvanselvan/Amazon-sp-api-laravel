@extends('adminlte::page')

@section('title', 'Backup Management')

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

@section('content_header')
<div class="row">
    <div class="col-10">
        <h1 class="m-0 text-dark">DataBase Backup Management</h1>
    </div>
    <div class="text-right col-2">
        <x-adminlte-button label="Save Changes" theme="primary" icon="fas fa-check-circle" id="db_backup" class="db_backup" />
    </div>
</div>
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
            @if($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
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
        </div>
        <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
            please Select tables to <strong>exclude </strong> from backup
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr class="table-info">
                    <!-- <th>ID</th> -->
                    <th>Connection Name</th>
                    <th>Table Name</th>
                    <th>exclude From Backup</th>
                </tr>
            </thead>
            @foreach ($table_data as $key1 => $value)

            @foreach($value as $key2 => $datas)

            @foreach($datas as $data)
            <tr>
                <td>{{$key2}}</td>
                <td>{{$data}}</td>
                @if(in_array($data,$selected_data))
                <td id='chk'><input type="checkbox" id="backup" name="backup" value="{{$key2}}|{{$data}}" checked></td>
                @else
                <td id='chk'><input type="checkbox" id="backup" name="backup" value="{{$key2}}|{{$data}}"></td>
                @endif
            </tr>
            @endforeach
            @endforeach
            @endforeach
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#db_backup").on('click', function(e) {
        var checkboxValues = [];
        var checkboxValues = [];
        $('input[type=checkbox]:checked').each(function() {
            checkboxValues.push($(this).val());
        });

        chk_values = checkboxValues.join(',');

        $.ajax({
            method: 'GET',
            url: '/admin/backup/save/',
            data: {
                'values': chk_values,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                window.location.href = '/admin/backup/management?success=Backup has been updated successfully'

            },
            error: function(response) {

                alert('Error');
                console.log(response);
            }
        });
    });
</script>
@stop