@extends('adminlte::page')
@section('title', 'System Setting')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class='row'>

    <div class="col text-center">
        <h1 class="mb-1 text-dark font-weight-bold"> System Setting </h1>

    </div>
    <!-- <div class="col-5 text-right">
        <a href="test">
            <x-adminlte-button label="Bin" type="submit" theme="primary" icon="far fa-trash-alt text-danger" class="btn btn-sm  ml-2" />
        </a>
    </div> -->
</div>

<form action="{{ isset($records)? route('update.system.setting', $records[0]->id) : route('add.system.setting') }}" method="POST" >
    @csrf
    <div class="card ">
        <div class="card-body">
            <div class="row">
                @if(isset($records))
                    @foreach ($records as $record)
                        
                        <div class="col">
                            <x-adminlte-input label="Key" type="text" name="key" placeholder="key" value="{{$record->key}}" />
                        </div>
                        <div class="col">
                            <x-adminlte-input label="value" type="text" name="value" placeholder="value" value="{{$record->value}}"/>
                        </div>

                    @endforeach
                @else
                    <div class="col">
                        <x-adminlte-input label="Key" type="text" name="key" placeholder="key"  />
                    </div>
                    <div class="col">
                        <x-adminlte-input label="value" type="text" name="value" placeholder="value" />
                    </div>
                @endif
            </div>

            <a href="{{ route('system.setting.home') }}" class="{{ (isset($records)) ? 'btn btn-primary btn-sm' : 'd-none' }}" >
                <i class="fas fa-arrow-left"></i> back
            </a>
            
            <a href="{{ route('recycle.system.setting') }}" class="btn btn-primary btn-sm float-right ml-2">
                <i class="far fa-trash-alt text-danger"></i> Bin
            </a>
            <x-adminlte-button label="{{ (isset($records)) ? 'Update System Setting ' : 'Add System Setting' }}" type="submit" theme="{{ (isset($records)) ? 'success' : 'primary' }}" icon=" {{ (isset($records)) ? 'fas fa-edit' : 'fa fa-plus' }}" class="btn-sm float-right"  />

        </div>
    </div>
</form>

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
            @elseif($message = Session::get('danger'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif
        </div>

        <table class="table table-bordered yajra-datatable table-striped text-center table-sm">
            <thead>
                <tr class="length">
                    <th>S/N</th>
                    <th>Key</th>
                    <th>Key Value</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

    </div>
</div>

@stop

@section('js')
<script>
    
    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        pageLength: 100,
        ajax: "{{ url('admin/system-setting') }}",
        // ajax: currentLocation,
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 'key',
                name: 'key',
            },
            {
                data: 'value',
                name: 'value',
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });

    $(document).on('click', '.remove', function(){
        let bool = confirm('Are you sure you want to delete this ?');
        if (!bool) {
            return false;
        }
    });

</script>
@stop