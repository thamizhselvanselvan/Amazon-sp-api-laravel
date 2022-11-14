@extends('adminlte::page')

@section('title', 'File Management')

@section('css')

@stop
@section('content_header')
    <div class="row">
        <div class="col">

            <h1 class="m-0 text-dark">File Management</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <table class="table table-striped table-bordered text-center table-sm">
            <thead class="table-info">
                <th>User Name</th>
                <th>Type</th>
                <th>Module</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
            </thead>
            <tbody>
                @foreach ($file_info as $file_value)
                    <tr>
                        <td>{{ $file_value['user_name'] }}</td>
                        <td>{{ $file_value['type'] }}</td>
                        <td>{{ $file_value['module'] }}</td>
                        <td>{{ $file_value['start_time'] }}</td>
                        <td>{{ $file_value['end_time'] }}</td>
                        <td>{{ $file_value['process'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop

@section('js')
@stop
