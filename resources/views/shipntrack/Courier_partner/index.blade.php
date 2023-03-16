@extends('adminlte::page')

@section('title', 'Courier Partners')

@section('content_header')
<div class="row">
    <div class="col-5">
        <h2 class="">
            <a href="{{ Route('courier.partners.create') }}">
                <x-adminlte-button label="Add Courier Partner" theme="primary" icon="fas fa-plus" />
            </a>
        </h2>
    </div>
    <h1 class="m-0 text-dark col">Courier Partner's</h1>
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
<!-- <div class="pl-2">
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
</div> -->

<div class="pl-2 pt-1">
    <table class="table table-bordered yajra-datatable table-striped table-sm ">
        <thead class="table-info">
            <tr>
                <th>ID</th>
                <th>courier Name</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Code</th>
                <th>Status</th>
                <th>type</th>
                <th>key1</th>
                <th>key2</th>
                <th>key3</th>
                <th>key4</th>
                <th>key5</th>
                <th>Action</th>
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
        $.extend($.fn.dataTable.defaults, {
            pageLength: 100,
        });

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            lengthChange: false,
            bFilter: false,
            ajax: {
                url: "{{route('snt.courier.index') }}",
                data: {},
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'source',
                    name: 'source',
                },
                {
                    data: 'destination',
                    name: 'destination',
                },
                {
                    data: 'courier_code',
                    name: 'courier_code',
                },
                {
                    data: 'active',
                    name: 'active',
                },
                {
                    data: 'type',
                    name: 'type',
                },
                {
                    data: 'key1',
                    name: 'key1',
                },
                {
                    data: 'key2',
                    name: 'key2',
                },
                {
                    data: 'key3',
                    name: 'key3',
                },
                {
                    data: 'key4',
                    name: 'key4',
                },
                {
                    data: 'key5',
                    name: 'key5',
                },
                {
                    data: 'action',
                    name: 'action',
                },
            ],
        });


        $(document).on('click', ".delete", function(e) {
            e.preventDefault();
            let bool = confirm('Are you sure you want to delete?');

            if (!bool) {
                return false;
            }
            let self = $(this);
            let id = self.attr('data-id');

            self.prop('disable', true);

            $.ajax({
                method: 'get',
                url: '/shipntrack/courier/remove/' + id,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "_method": 'DELETE'
                },
                response: 'json',
                success: function(response) {
                    alert('Delete success');
                    location.reload()
                },
                error: function(response) {

                    console.log(response)
                }
            });
        });
    });
</script>

@stop