@extends('adminlte::page')

@section('title', 'Create Shipment')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

<style>
    .autocomplete {
        /*the container must be positioned relative:*/
        position: relative;
        display: inline-block;
    }

    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }

    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    .autocomplete-items div:hover {
        /*when hovering an item:*/
        background-color: #e9e9e9;
    }

    .autocomplete-active {
        /*when navigating through the items using the arrow keys:*/
        background-color: DodgerBlue !important;
        color: #ffffff;
    }
</style>

@stop
@section('content_header')
<h1 class="m-0 text-dark">Outward Shipment</h1>
@stop
@section('content')
<!-- 
<div class="row">
    <div class="col">
        <a href="{{ route('outwardings.index') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left btn-sm"></i> Back
        </a>
    </div>
</div> -->
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
    </div>
</div>
<div class="row">
    <div class="col-2">
        <div class="form-group">
            <x-adminlte-select name="warehouse" label="Select warehouse:" id="warehouse">
                <option>Select warehouse</option>
                @foreach ($ware_lists as $ware_list)
                <option value="{{ $ware_list->id }}">{{$ware_list->name }}</option>
                @endforeach
            </x-adminlte-select>

        </div>
    </div>
    <div class="col-2">
        <div class="form-group">
            <x-adminlte-select name="destination" label="Select Destination:" id="destination">
                <option>Select Destination</option>
                @foreach ($destination_lists as $destination_list)
                <option value="{{ $destination_list->id }}">{{$destination_list->name }}</option>
                @endforeach

            </x-adminlte-select>

        </div>
    </div>
    <div class="col-2"  id="asin">
        <div class="form-group">
            <label>Enter ASIN:</label>
            <div class="autocomplete" style="width:200px;">
                <input id="upload_asin" type="text" autocomplete="off" name="upload_asin" placeholder="Enter Asin here..." class="form-control">
            </div>
        </div>
    </div>
    <!-- <div class="col-2">
        <div class="form-group">
            <x-adminlte-select name="country" label="Select Country:" id="country">
                <option>Select Country</option>
                @foreach ($destination_lists as $destination_list)
                <option value="{{ $destination_list->id }}">{{$destination_list->country }}</option>
                @endforeach
            </x-adminlte-select>

        </div>
    </div> -->
    <!-- <div class="col-2">
        <div class="form-group">
            <label>Enter Shipment ID:</label>
            <div class="Shipment" style="width:200px;">
                <input id="Shipment" type="text" name="Shipment" placeholder="enter Shipment ID" class="form-control">
            </div>
        </div>
    </div> -->

    <div class="col-1" id="currency">
        <x-adminlte-input label="Currency:" name="currency" type="text" placeholder="Currency" />

    </div>
    <div class="col text-right" id="create">
        <div style="margin-top: 1.8rem;">
            <!-- //<a href="/shipment/storeshipment"> -->
            <x-adminlte-button label="Create Shipment" theme="primary" onclick="getBack()" icon="fas fa-plus" class="btn-sm create_outshipmtn_btn" />
            <!-- </a> -->

        </div>
    </div>

</div>

<div class="row">

</div>
<br>
<table class="table table-bordered yajra-datatable table-striped" id="outward_table">
    <thead>
        <tr>
            <td>asin</td>
            <td>Item Name</td>
            <td>Quantity</td>
            <td>Price</td>
            <td>Action</td>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
@stop


@section('js')

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });



    $(".create_outshipmtn_btn").on("click", function() {
        let self = $(this);
        let table = $("#outward_table tbody tr");
        //let data = {};
        let data = new FormData();

        table.each(function(index, elm) {

            let cnt = 0;
            let td = $(this).find('td');
            //  console.log(td);

            data.append('asin[]', td[0].innerText);
            data.append('name[]', td[1].innerText);
            data.append('quantity[]', td[2].children[0].value);
            data.append('price[]', td[3].children[0].value);

        });

        let destination = $('#destination').val();
        data.append('destination', destination);


        $.ajax({
            method: 'POST',
            url: '/shipment/storeoutshipment',
            data: data,
            processData: false,
            contentType: false,
            response: 'json',
            success: function(response) {

                console.log(response);
                //alert('success');
                // location.reload()

            },
            error: function(response) {
                console.log(response);
            }


        });

    });

    function getBack() {
        // window.location.assign('/inventory/shipments')
        window.location.href = '/inventory/outwardings'

    }

    $("#outward_table").hide();
    $("#create").hide();
    $("#asin").hide();
    $("#currency").hide();

    $("#destination").on('change', function(e) {
        $("#asin").show();
    });
    $("#asin").on('change', function(e) {
        $("#currency,#outward_table,#create").show();
    });



    autocomplete(document.getElementById("upload_asin"));

    function autocomplete(inp) {
        /*the autocomplete function takes two arguments,
        the text field element and an array of possible autocompleted values:*/
        var currentFocus;

        /*execute a function when someone writes in the text field:*/
        inp.addEventListener("input", function(e) {
            var a, b, i, val = this.value;
            /*close any already open lists of autocompleted values*/
            closeAllLists();

            if (!val && val.length > 2) {
                return false;
            }
            currentFocus = -1;
            /*create a DIV element that will contain the items (values):*/
            a = document.createElement("DIV");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            /*append the DIV element as a child of the autocomplete container:*/

            this.parentNode.appendChild(a);
            /*for each item in the array...*/

            if (val.length <= 1) {
                return false;
            }

            $.ajax({
                method: 'GET',
                url: '/shipment/autofinish',
                data: {
                    'asin': val
                },
                //response: 'json',
                success: function(arr) {

                    $.each(arr, function(index, val) {

                        let asin = val.asin;

                        /*check if the item starts with the same letters as the text field value:*/
                        if (asin.substr(0, asin.length).toUpperCase() == asin.toUpperCase()) {
                            /*create a DIV element for each matching element:*/
                            b = document.createElement("DIV");
                            /*make the matching letters bold:*/
                            b.innerHTML = "<strong>" + asin.substr(0, asin.length) + "</strong>";
                            b.innerHTML += asin.substr(asin.length);
                            /*insert a input field that will hold the current array item's value:*/
                            b.innerHTML += "<input type='hidden' value='" + asin + "'>";
                            /*execute a function when someone clicks on the item value (DIV element):*/
                            b.addEventListener("click", function(e) {
                                /*insert the value for the autocomplete text field:*/
                                //inp.value = this.getElementsByTagName("input")[0].value;
                                inp.value = '';

                                getData(this.getElementsByTagName("input")[0].value);

                                /*close the list of autocompleted values,
                                (or any other open lists of autocompleted values:*/
                                closeAllLists();
                            });
                            a.appendChild(b);
                        }
                    });
                },
                error: function(response) {}
            });
        });

        /*execute a function presses a key on the keyboard:*/
        inp.addEventListener("keydown", function(e) {
            var x = document.getElementById(this.id + "autocomplete-list");
            if (x) x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
                /*If the arrow DOWN key is pressed,
                increase the currentFocus variable:*/
                currentFocus++;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.keyCode == 38) { //up
                /*If the arrow UP key is pressed,
                decrease the currentFocus variable:*/
                currentFocus--;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.keyCode == 13) {
                /*If the ENTER key is pressed, prevent the form from being submitted,*/
                e.preventDefault();
                if (currentFocus > -1) {
                    /*and simulate a click on the "active" item:*/
                    if (x) x[currentFocus].click();
                }
            }
        });

        function addActive(x) {
            /*a function to classify an item as "active":*/
            if (!x) return false;
            /*start by removing the "active" class on all items:*/
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            /*add class "autocomplete-active":*/
            x[currentFocus].classList.add("autocomplete-active");
        }

        function removeActive(x) {
            /*a function to remove the "active" class from all autocomplete items:*/
            for (var i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }

        function closeAllLists(elmnt) {
            /*close all autocomplete lists in the document,
            except the one passed as an argument:*/
            var x = document.getElementsByClassName("autocomplete-items");
            for (var i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != inp) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        }
        /*execute a function when someone clicks in the document:*/
        document.addEventListener("click", function(e) {
            closeAllLists(e.target);
        });
    }

    function getData(asin) {

        $.ajax({
            method: 'GET',
            url: '/shipment/select/View',
            data: {
                'asin': asin
            },
            success: function(arr) {
                // console.log(arr);

                let html = "<tr class='table_row'>";
                html += "<td name='asin[]'>" + arr.asin + "</td>";
                html += "<td name='name[]'>" + arr.item_name + "</td>";
                html += '<td> <input type="text" value="1" name="quantity[]" id="quantity"> </td>'
                html += '<td> <input type="text" value="0" name="price[]" id="price"> </td>'
                html += '<td> <button type="button" id="remove" class="btn btn-danger remove1">Remove</button></td>'
                html += "</tr>";

                $("#outward_table").append(html);
            },
            error: function(response) {
                // console.log(response);
            }
        });
    }
    $('#outward_table').on('click', ".remove1", function() {

        $(this).closest("tr").remove();
    });
</script>
@stop