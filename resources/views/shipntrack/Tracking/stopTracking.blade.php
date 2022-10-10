@extends('adminlte::page')

@section('title', 'Stop & Ignore Tracking')

@section('content_header')
<div class="container-fluid">
    <h1 class="m-0 text-dark col">Stop And Ignore Tracking</h1>
</div>
@stop

@section('content')

<form class="ml-4 mt-1 mr-4" action="{{route('shipntrack.stop.update')}}" method="POST">
    @csrf
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
    <div class="container-fluid">
        <div class="row ">
            <div class="col-2">
                <x-adminlte-select name="forwarder" id='forwarder' label="Select Courier Partner">
                    <option value="select">--Select--</option>
                    @foreach ($courier_partner as $name )
                    <option value="{{$name}}">{{$name}}</option>
                    @endforeach
                </x-adminlte-select>
            </div>
            <div class="col">
                <div class="text-right m-3 ">
                    <x-adminlte-button label='Save' class="btn-sm" theme="primary" icon="fas fa-file-upload" type="submit" />
                </div>

            </div>
        </div>
        <div id="showTable" class="d-none">
            <table class='table table-bordered table-striped text-center'>
                <thead>
                    <tr class='text-bold bg-info'>
                        <th>Event</th>
                        <th>Show</th>
                        <th>Stop</th>
                    </tr>
                </thead>
                <tbody id='checkTable'>
                </tbody>
            </table>
        </div>
    </div>
</form>
@stop

@section('js')
<script type="text/javascript">
    $('#forwarder').change(function() {

        let forwarder = $(this).val();

        if (forwarder == 'select') {
            $('#tracking_status').empty();
            return false;
        }

        $.ajax({
            url: "{{route('shipntrack.stop')}}",
            method: "POST",
            data: {
                "source": forwarder,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                $('#showTable').removeClass('d-none');
                $('#checkTable').html(response.success);

            },
            error: function(result) {

            }
        });
    });
</script>
@stop