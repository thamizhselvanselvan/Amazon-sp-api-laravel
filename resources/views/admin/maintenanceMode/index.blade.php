@extends('adminlte::page')

@section('title', 'Maintenance Mode')

@section('css')

@stop
@section('content_header')
    <div class="row">
        <div class="col">

            <h1 class="m-0 text-dark text-center"><b>System Maintenance Mode</b></h1>
        </div>
    </div>
@stop

@section('content')

    <div class="custom-control custom-switch text-center">
        @if ($maintenance_mode == 1)
            <input type="checkbox" class="custom-control-input btn-lg" id="maintenance_mode" name="mode" value="off"
                checked>
            <label class="custom-control-label" for="maintenance_mode">Maintenance Mode On/Off</label>
        @else
            <input type="checkbox" class="custom-control-input btn-lg" id="maintenance_mode" name="mode" value="on">
            <label class="custom-control-label" for="maintenance_mode">Maintenance Mode On/Off</label>
        @endif

    </div>

@stop

@section('js')
    <script text="javascript">
        $('#maintenance_mode').click(function() {

            mode = $(this).val();
            // alert(mode);
            if (mode == 'on') {
                $(this).val('off');
            } else {
                $(this).val('on');
            }
            // alert(mode);
            let bool = confirm("Are you sure you want to " + mode.toUpperCase() + " maintenance mode?");
            if (!bool) {
                return false;
            }

            $.ajax({
                method: 'POST',
                url: "{{ route('maintenence.mode.on.off') }}",
                data: {
                    'mode': mode,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    console.log(response);
                }
            });
        });
    </script>
@stop
