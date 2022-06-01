@extends('adminlte::page')

@section('title', 'Add Shelves')

@section('css')
<link rel="stylesheet" href="/css/styles.css">





@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ route('shelves.index') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center">Add Rack Shelves</h1>
    </div>
</div>

@stop

@section('content')

<div class="loader d-none">
    <div class="sub-loader position-relative ">
        <div class="lds-hourglass"></div>
        <p>Loading...</p>
    </div>
</div>

<div class="row">
    <div class="col"></div>
    <div class="col-6">

        @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session()->get('success') }}
        </x-adminlte-alert>
        @endif

        @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session()->get('error') }}
        </x-adminlte-alert>
        @endif

        <form action="{{ route('shelves.store') }}" method="POST" id="admin_user">
            @csrf

            <div class="row justify-content-center">

                <div class="col-6">

                    <x-adminlte-select label="Select Warehouse" name="ware_id" id="warehouse">
                        <option>Select Warehouse</option>
                        @foreach ($ware_lists as $ware_list)

                        <option value="{{ $ware_list->id }}">{{ $ware_list->name  }}</option>

                        @endforeach

                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <x-adminlte-select label="Select Rack" name="rack_id" id="rack">
                        <option>Select Rack</option>
                    </x-adminlte-select>


                </div>
            </div>

            <div class="row ">
                <div class="col-6">
                    <x-adminlte-input label="Shelve Name" name="name" type="text" placeholder="Name" id="shelve_name" />
                </div>

                <div class="col-4" id="add">
                    <div style="margin-top: 2.3rem;">
                        <x-adminlte-button label="Add" theme="primary" icon="fas fa-plus" id="create" class="btn-sm " />
                    </div>
                </div>
            </div>
            <br>
            <table class="table table-bordered yajra-datatable table-striped" id="rack_table">
                <thead>
                    <tr>

                        <td>Shelve Name</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody id="data">
                </tbody>
            </table>
            <div class="text-center">
                <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-plus" type="submit" />
            </div>

        </form>
    </div>
    <div class="col"></div>
</div>

@stop



@section('js')

<script type="text/javascript">
    /*hide untill data is filled*/
    $("#rack_table").hide();
    $("#add").on('click', function(e) {
        $("#rack_table").show();

        let shelve_name;

        shelve_name = $('#shelve_name').val();

        let html = "<tr class='table_row'>";
        html += "<td> <input type='hidden'  name='name[]' value='" + shelve_name + "' /> " + shelve_name + "</td>";
        html += '<td> <button type="button" id="remove" class="btn btn-danger remove1">Remove</button></td>'

        $("#rack_table").append(html);


        $('#rack_table').on('click', ".remove1", function() {
            $(this).closest("tr").remove();
        });

        shelve_name = $('#shelve_name').val('');
    });

    $(document).ready(function(){

        $('#warehouse').change(function(){
            var id=$(this).val();
            $.ajax({
                url :'/rack/'+id,
                method : 'POST',
                data:{ 
                        'id':id,
                        "_token": "{{ csrf_token() }}",
                    },
                success:function(result){
                    // alert('success');
                    $('#rack').empty();
                    let rack_data ='<option> Select Rack </option>';
                    $.each(result, function(i, result){
                     rack_data += "<option value='"+result.rack_id+"'>"+result.rack_id+"/"+result.name+"</option>";
                    });
                    $('#rack').append(rack_data);
                },
                error:function(){
                    alert('ERROR');
                }  
            });
        });
    });
</script>
@stop