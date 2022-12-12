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
                <option Value="">Select warehouse</option>
                @foreach ($ware_lists as $ware_list)
                <option value="{{ $ware_list->warehouses->id }}">{{$ware_list->warehouses->name }}</option>
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
    <div class="col-2" id="asin">
        <div class="form-group">
            <label>Enter ASIN:</label>
            <div class="autocomplete" style="width:200px;">
                <input id="upload_asin" type="text" autocomplete="off" name="upload_asin" placeholder="Enter Asin here..." class="form-control">
            </div>
        </div>
    </div>


    <div class="col-2" id="currency">

        <x-adminlte-select name="currency" id="currency_output" label="Currency:">
            <option value="">Select Currency </option>
            @foreach ($currency_lists as $currency_list)
            <option value="{{ $currency_list->id }}">{{$currency_list->code }}</option>
            @endforeach
        </x-adminlte-select>
    </div>
    <div class="col text-right" id="create">
        <div style="margin-top: 1.8rem;">
            <x-adminlte-button label="Create Shipment" theme="primary" icon="fas fa-plus" class="btn-sm create_outshipmtn_btn" />
        </div>
    </div>
</div>

<div class="row">
</div>
<br>
<table class="table table-bordered yajra-datatable table-striped" id="outward_table">
    <thead>
        <tr>
            <th>asin</th>
            <th>Item Name</th>
            <th>Inwarding Price</th>
            <th>Outwarding Price</th>
            <th>Quantity Left</th>
            <th>Quantity</th>
            <th> Tag</th>
            <th>Action</th>
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
        $(this).prop('disabled', true);

        let ware_valid = $('#warehouse').val();
        let currency_valid = $('#currency_output').val();
        let validation = true;
        if (ware_valid == 0) {
            alert('warehouse field is required');
            $('.create_outshipmtn_btn').prop('disabled', false);
            validation = false;
            return false;
        } else if (currency_valid == 0) {
            $('.create_outshipmtn_btn').prop('disabled', false);
            alert('currency field is required');
            validation = false;
            return false;
        } else {

            let self = $(this);
            let table = $("#outward_table tbody tr");
            //let data = {};
            let data = new FormData();

            table.each(function(index, elm) {

                let cnt = 0;
                let td = $(this).find('td');

                let tag = $(td[6]).find('select').val();
                if (tag == 0) {
                    alert('please select the Tag for all ASIN');
                    $('.create_outshipmtn_btn').prop('disabled', false);
                    validation = false;
                    return false;
                }

                data.append('id[]', $(td[0]).attr("data-id"));
                data.append('asin[]', td[0].innerText);
                data.append('name[]', td[1].innerText);
                data.append('price[]', td[3].innerText);
                data.append('quantity[]', td[5].children[0].value);
                data.append('tag[]', $(td[6]).find('select').val());


            });

            let warehouse = $('#warehouse').val();
            data.append('warehouse', warehouse);


            let currency = $('#currency_output').val();
            data.append('currency', currency);

            let destination = $('#destination').val();
            data.append('destination', destination);

            if (validation) {
                $.ajax({
                    method: 'POST',
                    url: "{{route('inventory.out.save')}}",
                    data: data,
                    processData: false,
                    contentType: false,
                    response: 'json',
                    success: function(response) {
                        if (response.success) {
                            getBack();
                        }
                    },
                    error: function(response) {
                        alert('Something went Wrong')
                    }


                });
            }

        }

    });

    function getBack() {
        window.location.href = '/inventory/outwardings?success=shipment has created Successfully'

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

            let warehouse_id = $("#warehouse").val();

            $.ajax({
                method: 'POST',
                url: '/inventory/shipment/warehouseg/' + warehouse_id,
                data: {
                    'asin': val,
                    "_token": "{{ csrf_token() }}",

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

    function getData(asin, id) {
        let warehouse_id = $("#warehouse").val();
        $.ajax({
            method: 'GET',
            url: "{{route('inventory.shipment.select.View')}}",
            data: {
                'asin': asin,
                warehouse_id,
                'id': id
            },
            success: function(arr) {
                //   console.log(arr);

                let html = "<tr class='table_row'>";
                html += "<td name='asin[]' data-id='" + arr.id + "'>" + arr.asin + "</td>";
                html += "<td name='name[]'>" + arr.item_name + "</td>";
                html += "<td>" + arr.price + "</td>";
                html += "<td name='priceo[]'>" + arr.price + "</td>";
                html += "<td name='quantityl[]'>" + arr.balance_quantity + "</td>";
                html += '<td> <input type="text" value="1" name="quantity[]" id="quantity"> </td>'
                html += `<td>
                     <x-adminlte-select name="tag[]" id="tag">>
                      <option value=" ">Select Tag</option>
                       @foreach ($tags as $tag)
                       <option value="{{ $tag->id }}">{{$tag->name }}</option>
                      @endforeach
                    </x-adminlte-select>
                     </td>`
                html += '<td> <button type="button" id="remove" class="btn btn-danger remove1">Remove</button></td>'
                html += "</tr>";

                $("#outward_table").append(html);

                // $("#quantity").on("change", function() {
                //         let out_qty = $('#quantity').val();

                //         let exist_qty = arr.balance_quantity;

                //         if (out_qty > exist_qty) {
                //             alert('Product quantity Exceeds');
                //             return false;
                //         } else if  (out_qty < exist_qty) {
                //             alert('item is not present In the warehouse');
                //             return false;
                //         }

                // });
            },
            error: function(response) {
                alert('Something went Wrong')
            }
        });
    }
    $('#outward_table').on('click', ".remove1", function() {

        $(this).closest("tr").remove();
    });
</script>
@stop