@extends('adminlte::page')

@section('title', 'Add Rack')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ route('racks.index') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center">Add Rack's</h1>
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

        <form action="{{ Route('racks.store') }}" method="POST" id="admin_user">
            @csrf
            <div class="row">

                <div class="col-4">

                    <x-adminlte-select name="warehouse_id" label="Select Warehouse">
                        <option>Select warehouse</option>
                        @foreach ($warehouse_lists as $warehouse_list)
                        <option value="{{ $warehouse_list->id }}">{{ $warehouse_list->name }}</option>
                        @endforeach

                    </x-adminlte-select>

                </div>

            </div>

            <div class="row justify-content-center">


                <div class="col-4">
                    <x-adminlte-input label="Rack ID" name='rack_id' type="text" placeholder="ID" value="{{ old('name') }}" />
                </div>
                <div class="col-4">
                    <x-adminlte-input label="Rack Name" name="name" type="text" placeholder="Name" value="{{ old('name') }}" />
                </div>
                <div class="col-4" id="add">
                    <div style="margin-top: 2.3rem;">
                        <x-adminlte-button label="Add" theme="primary" onclick="add()" icon="fas fa-plus" id="create" class="btn-sm " />
                    </div>
                </div>
            </div>
            <div class="row">

            </div>
            <br>
            <table class="table table-bordered yajra-datatable table-striped" id="rack_table">
                <thead>
                    <tr>
                        <td>Rack ID</td>
                        <td>Rack Name</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="text-center">
                <x-adminlte-button label="Submit" theme="primary" icon="fas fa-plus" type="submit" />
            </div>

        </form>
    </div>
    <div class="col"></div>
</div>

@stop

<!-- 
@section('js')

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#rack_table").hide();
    /*hide untill data is filled*/

    $("#add").on('click', function(e) {
        $("#rack_table").show();
    });

    $(".add").on("click", function() {
        let self = $(this);
        let table = $("#rack_table tbody tr");
        //let data = {};
        let data = new FormData();

        table.each(function(index, elm) {

            let cnt = 0;
            let td = $(this).find('td');
            //  console.log(td);

            data.append('rack_id[]', td[0].innerText);
            data.append('nack_ame[]', td[1].innerText);

        });
    });

    $(document).ready(function() {

        $("#add").submit(function(e) {

            //stop submitting the form to see the disabled button effect
            e.preventDefault();

            //disable the submit button
            $("#add").attr("disabled", true);

            //disable a normal button
            $("#add").attr("disabled", true);

            return true;

        });
    });


    /* Display Autocomplete data:*/
    function getData(rack_id, rack_name) {

        let html = "<tr class='table_row'>";
        html += "<td name='asin[]'>" + rack_id + "</td>";
        html += "<td name='name[]'>" + rack_name + "</td>";
        html += '<td> <button type="button" id="remove" class="btn btn-danger remove1">Remove</button></td>'
        html += "</tr>";

        $("#rack_table").append(html);

    }
</script>
@stop -->