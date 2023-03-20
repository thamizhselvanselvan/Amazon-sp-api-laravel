@extends('adminlte::page')

@section('title', 'SMSA Packet Details')

@section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark col"><b>Packet Details</b></h1>
        <h2 class="mb-4 text-right col">

        </h2>
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
            </div>

            <div class="alert_display">
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="pl-2">
        <table class="table table-bordered yajra-datatable table-striped text-center">
            @if (isset($data1[0]))
                @foreach ($data1[0] as $key1 => $record)
                    <th class="bg-info">{{ $key1 }}</th>
                @endforeach
            @endif

            @if (isset($data2[0]))
                @foreach ($data2[0] as $key2 => $record)
                    <th class="bg-info">{{ $key2 }}</th>
                @endforeach
            @endif

            <tbody>
                @foreach ($data1 as $key3 => $records)
                    <tr>
                        @foreach ($records as $key4 => $data)
                            <td>{{ $records[$key4] ?? 'NA' }}</td>
                        @endforeach

                        @if (isset($data2[$key3]))
                            @foreach ($data2[$key3] as $data)
                                <td>{{ $data ?? 'NA' }}</td>
                            @endforeach
                        @endif

                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
@stop

@section('js')

@stop
