@extends('adminlte::page')

@section('title', 'Add Bin')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ route('bins.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h3 class="m-0 text-dark text-center">Add Bin</h3>
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

        <form action="{{ route('bins.store') }}" method="POST" id="admin_user">

            @csrf

            <div class="row justify-content-center">
                <div class="col-6">

                    <x-adminlte-select label="Select Warehouse" name="ware_id" id="warehouse">
                        <option value="">Select Warehouse</option>
                        @foreach ($ware_lists as $ware_list)

                        <option value="{{ $ware_list->id }}">{{ $ware_list->name  }}</option>

                        @endforeach


                    </x-adminlte-select>
                </div>
                <div class="col-6">

                    <x-adminlte-select label="Select Rack" name="rack_id" id='rack_id'>
                        <option value="">Select Rack</option>
                       
                    </x-adminlte-select>
                </div>
            </div>
            <div class="row">
                <div class="col-6">

                    <x-adminlte-select name="shelve_id" id='shelve_id' label="Select Shelve">
                        <option value=""> Select Shelve </option>
                        
                    </x-adminlte-select>

                </div>



                <div class="col-6">
                    <x-adminlte-input label="Name" name="name" id="name" type="text" placeholder="Name "  />
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-3">
                    <x-adminlte-input label="Width" name="width" id="width" type="text" placeholder="Width"  />
                </div>
                <div class="col-3">
                    <x-adminlte-input label="Height" name="height" id="height" type="text" placeholder="Height"  />
                </div>
                <div class="col-3">
                    <x-adminlte-input label="Depth" name="depth" id="depth" type="text" placeholder="Depth "  />
                </div>
                <div class="col-3" id="add">
                    <div style="margin-top: 2.3rem;">
                        <x-adminlte-button label="Add" theme="primary" icon="fas fa-plus" id="create" class="btn-sm " />
                    </div>
                </div>
            </div>
            <br>
            <table class="table table-bordered yajra-datatable table-striped" id="bin_table">
                <thead>
                    <tr>

                        <td>Bin Name</td>
                        <td>Width</td>
                        <td>Height</td>
                        <td>Depth</td>
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
<script>
    
    /*hide untill data is filled*/
    $("#bin_table").hide();
    $("#add").on('click', function(e) {
        $("#bin_table").show();

        let bin_name;
        let width;
        let height;
        let depth;

        bin_name = $('#name').val();
        width=$('#width').val();
        height=$('#height').val();
        depth=$('#depth').val();

        let html = "<tr class='table_row'>";
        html += "<td> <input type='hidden'  name='name[]' value='" + bin_name + "' /> " + bin_name + "</td>";
        html += "<td> <input type='hidden'  name='width[]' value='" + width + "' /> " + width + "</td>";
        html += "<td> <input type='hidden'  name='height[]' value='" + height + "' /> " + height + "</td>";
        html += "<td> <input type='hidden'  name='depth[]' value='" + depth + "' /> " + depth + "</td>";
        html += '<td> <button type="button" id="remove" class="btn btn-danger remove1">Remove</button></td>'

        $("#bin_table").append(html);


        $('#bin_table').on('click', ".remove1", function() {
            $(this).closest("tr").remove();
        });

        bin_name = $('#name').val('');
        width=$('#width').val('');
        height=$('#height').val('');
        depth=$('#depth').val('');
        
    });

    $(document).ready(function(){

        $('#warehouse').change(function(){
            var id=$(this).val();
            $.ajax({
                url :'/Binrack/'+id,
                method : 'POST',
                data:{ 
                        'id':id,
                        "_token": "{{ csrf_token() }}",
                    },
                success:function(result){
                    // alert('success');
                    $('#rack_id').empty();
                    let rack_data ='<option> Select Rack </option>';
                    $.each(result, function(i, result){
                    rack_data += "<option value='"+result.rack_id+"'>"+result.rack_id+"/"+result.name+"</option>";
                    });
                    $('#rack_id').append(rack_data);
                },
                error:function(){
                    alert('ERROR');
                }  
            });
        });

        $('#rack_id').change(function(){
            var id=$(this).val();
            
            $.ajax({
                url :'/BinShelves/'+id,
                method : 'POST',
                data:{ 
                        'id':id,
                        "_token": "{{ csrf_token() }}",
                    },
                success:function(result){
                    // alert('success');
                    $('#shelve_id').empty();
                    let shelve_data ='<option> Select Shelve </option>';
                    $.each(result, function(i, result){
                        shelve_data += "<option value='"+result.rack_id+"'>"+result.rack_id+"/"+result.name+"</option>";
                    });
                    $('#shelve_id').append(shelve_data);
                },
                error:function(){
                    alert('ERROR');
                }  
            });
        });
    });
</script>

@stop