@extends('adminlte::page')

@section('title', 'Courier Partner')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Courier Partner</h1>
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
    <form action="{{route('snt.courier.index')}}" class="ml-4 mt-1 mr-4" method='post'>
        @csrf
        <div class="row">
            <div class="col">
                <x-adminlte-input label="Courier Name" name="c_name" type="text" placeholder="Courier Name" />
            </div>
            <div class="col">
                <x-adminlte-input label="Source" name="source" type="text" placeholder="Source" />
            </div>
            <div class="col">
                <x-adminlte-input label="Destinatioin" name="destination" type="text" placeholder="Destination" />
            </div>
            <div class="col">
                <x-adminlte-input label="Code" name="code" type="text" placeholder="Code" />
            </div>
        </div>
        <div class="row">
            <div class="col text-right">
                <x-adminlte-button label="Add New" class='btn-sm' icon='fas fa-plus' type="submit" theme='primary' />
            </div>
        </div>
    </form>
</div>

<div class="pl-2 pt-1">
    <table class="table table-bordered yajra-datatable table-striped table-sm ">
        <thead class="bg-info">
            <tr>
                <th>courier Name</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Code</th>
                <th>Active</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@stop

@section('js')
<script type="text/javascript">
    $(function() {


        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{route('snt.courier.index') }}",
                data: {},
            },
            columns: [{
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'source_destination',
                    name: 'source_destination',
                },
                {
                    data: 'courier_code',
                    name: 'courier_code',
                },
                {
                    data: 'courier_code',
                    name: 'courier_code',
                },
                {
                    data: 'active',
                    name: 'active',
                },
            ],
        });
    });
</script>
@stop