@extends('adminlte::page')

@section('title', 'Order Details')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<h1 class="m-0 text-dark">Search Order</h1>
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
    <div class="alert_display">
        @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
        @endif
    </div>
</div>
<form action="{{ route('orders.search') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-2">
            <x-adminlte-input label="Enter OrderID:" name="orderid" id="orderid" type="text" placeholder="OrderID...." />
        </div>
        <div class="col-1">
            <div style="margin-top: 2.0rem;">
                <x-adminlte-button label="Search" theme="primary" id="ord_search" icon=" fas fa-search" type="submit" />
            </div>
        </div>
        <div class="col-2">
            <div style="margin-top: 2.0rem;">
                <a href="/orders/csv/import">
                    <x-adminlte-button label="Order Import" theme="info" icon="fas fa-file-upload" type="button" />
                </a>
            </div>
        </div>
    </div>
</form>


<form action="{{ route('orders.searched.update') }}" method="post" id="update_form">
    @csrf
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-2">
                @if (isset($data[0]->amazon_order_identifier))
                <x-adminlte-input label="Amazon Order Identifier:" name="amazon_order_identifier" id="identifier" value="{{$data[0]->amazon_order_identifier}}" type="text" />
                @else
                <x-adminlte-input label="Amazon Order Identifier:" name="amazon_order_identifier" id="identifier" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->order_item_identifier))
                <x-adminlte-input label="Order Item Identifier:" name="order_item_identifier" value="{{$data[0]->order_item_identifier}}" type="text" />
                @else
                <x-adminlte-input label="Order Item Identifier:" name="order_item_identifier" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->store_name))
                <x-adminlte-input label="Store Name:" name="store_name" value="{{$data[0]->seller_id}}  [{{$data[0]->store_name}}]" type="text" />
                @else
                <x-adminlte-input label="Store Name:" name="store_name" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->asin))
                <x-adminlte-input label="Asin:" name="asin" value="{{$data[0]->asin}}" type="text" />
                @else
                <x-adminlte-input label="Asin:" name="asin" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->seller_sku))
                <x-adminlte-input label="SKU:" name="sku" value="{{$data[0]->seller_sku}}" type="text" />
                @else
                <x-adminlte-input label="SKU:" name="sku" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->title))
                <x-adminlte-input label="Product Name:" name="title" value="{{$data[0]->title}}" type="text" />
                @else
                <x-adminlte-input label="Product Name:" name="title" type="text" />
                @endif
            </div>

        </div>
        <div class="row justify-content-center">

            <div class="col-2">
                @if (isset($data[0]->marketplace_identifier))
                <x-adminlte-input label="Marketplace Identifier:" name="marketplace_identifier" value="{{$data[0]->marketplace_identifier}}" type="text" />
                @else
                <x-adminlte-input label="Marketplace Identifier:" name="marketplace_identifier" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->purchase_date))
                <x-adminlte-input label="Purchase Date:" name="purchase_date" value="{{$data[0]->purchase_date}}" type="text" />
                @else
                <x-adminlte-input label="Purchase Date:" name="purchase_date" type="text" />

                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->order_status))
                <x-adminlte-input label="Order Status:" name="order_status" value="{{$data[0]->order_status}}" type="text" />
                @else
                <x-adminlte-input label="Order Status:" name="order_status" type="text" />

                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->fulfillment_channel))
                <x-adminlte-input label="Fulfillment Channel:" name="fulfillment_channel" value="{{$data[0]->fulfillment_channel}}" type="text" />
                @else
                <x-adminlte-input label="Fulfillment Channel:" name="fulfillment_channel" type="text" />

                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->sales_channel))
                <x-adminlte-input label="Sales Channel:" name="sales_channel" value="{{$data[0]->sales_channel}}" type="text" />
                @else
                <x-adminlte-input label="Sales Channel:" name="sales_channel" type="text" />

                @endif
            </div>

            <div class="col-2">
                @if (isset($data[0]->ship_service_level))
                <x-adminlte-input label="Ship Service Level:" name="ship_service_level" value="{{$data[0]->ship_service_level}}" type="text" />
                @else
                <x-adminlte-input label="Ship Service Level:" name="ship_service_level" type="text" />
                @endif
            </div>



        </div>
        <div class="row justify-content-center">
            <div class="col-2">
                @if (isset($price_data->Amount))
                <x-adminlte-input label="Amount:" name="Amount" value="{{$price_data->Amount}}" type="text" />
                @else
                <x-adminlte-input label="Amount:" name="Amount" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($price_data->CurrencyCode))
                <x-adminlte-input label="Currency Code:" name="CurrencyCode" value="{{$price_data->CurrencyCode}}" type="text" />
                @else
                <x-adminlte-input label="Currency Code:" name="CurrencyCode" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->quantity_ordered))
                <x-adminlte-input label="Quantity Orderd:" name="qty" value="{{$data[0]->quantity_ordered}}" type="text" />
                @else
                <x-adminlte-input label="Quantity Orderd:" name="qty" type="text" />

                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->quantity_shipped))
                <x-adminlte-input label="Quantity Shipped:" name="quantity_shipped" value="{{$data[0]->quantity_shipped}}" type="text" />
                @else
                <x-adminlte-input label="Quantity Shipped:" name="quantity_shipped" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->unship))
                <x-adminlte-input label="UnShiped:" name="unship" value="{{$data[0]->unship}}" type="text" />
                @else
                <x-adminlte-input label=" UnShiped:" name="unship" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->shipment_service_level_category))
                <x-adminlte-input label="Shipment Service Level Category" name="shipment_service_level_category" value="{{$data[0]->shipment_service_level_category}}" type="text" />
                @else
                <x-adminlte-input label="Shipment Service Level Category:" name="shipment_service_level_category" type="text" />
                @endif
            </div>

        </div>
        <div class="row justify-content-center">
            <div class="col-2">
                @if (isset($data[0]->earky_ship))
                <x-adminlte-input label="Earliest Ship Date:" name="earky_ship" value="{{$data[0]->earky_ship}}" type="text" />
                @else
                <x-adminlte-input label="Earliest Ship Date:" name="earky_ship" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->latest_ship))
                <x-adminlte-input label="Latest Ship Date:" name="latest_ship" value="{{$data[0]->latest_ship}}" type="text" />
                @else
                <x-adminlte-input label="Latest Ship Date:" name="latest_ship" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->early_deli))
                <x-adminlte-input label="Earliest Delivery Date:" name="early_deli" value="{{$data[0]->early_deli}}" type="text" />
                @else
                <x-adminlte-input label="Earliest Delivery Date:" name="early_deli" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->latest_deli))
                <x-adminlte-input label="Latest Delivery Date:" name="latest_deli" value="{{$data[0]->latest_deli}}" type="text" />
                @else
                <x-adminlte-input label="Latest Delivery Date:" name="latest_deli" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->last_update_date))
                <x-adminlte-input label="Last Update Date:" name="last_update_date" value="{{$data[0]->last_update_date}}" type="text" />
                @else
                <x-adminlte-input label="Last Update Date:" name="last_update_date" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($data[0]->order_type))
                <x-adminlte-input label="Order Type" name="order_type" value="{{$data[0]->order_type}}" type="text" />
                @else
                <x-adminlte-input label="Order Type:" name="order_type" type="text" />
                @endif
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-2">
                @if (isset($details->Name))
                <x-adminlte-input label="Consignee Name:" name="name" value="{{$details->Name}}" type="text" />
                @else
                <x-adminlte-input label="Consignee Name:" name="name" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if(isset($details->AddressLine1))
                <x-adminlte-input label="Consignee Address 1:" name="address_1" value="{{$details->AddressLine1}}" type="text" />
                @else
                <x-adminlte-input label="Consignee Address 1:" name="address_1" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($details->AddressLine2))
                <x-adminlte-input label="Consignee Address 2:" name="address_2" value="{{$details->AddressLine2}}" type="text" />

                @else
                <x-adminlte-input label="Consignee Address 2:" name="address_2" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($details->City))
                <x-adminlte-input label="Consignee City:" name="city" value="{{$details->City}}" type="text" />
                @else
                <x-adminlte-input label="Consignee City:" name="city" type="text" />

                @endif
            </div>
            <div class="col-2">
                @if (isset($details->StateOrRegion))
                <x-adminlte-input label="Consignee County/State" name="county" value="{{$details->StateOrRegion}}" type="text" />
                @else
                <x-adminlte-input label="Consignee County/State:" name="county" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($details->CountryCode))
                <x-adminlte-input label="Consignee Country:" name="country" value="{{$details->CountryCode}}" type="text" />
                @else
                <x-adminlte-input label="Consignee Country:" name="country" type="text" />
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-2">
                @if (isset($email_used->BuyerEmail))
                <x-adminlte-input label="Buyer Email:" name="BuyerEmail" value="{{$email_used->BuyerEmail}}" type="text" />
                @else
                <x-adminlte-input label="Buyer Email:" name="BuyerEmail" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($details->Phone))
                <x-adminlte-input label="Phone No." name="phone" value="{{$details->Phone}}" type="text" />
                @else
                <x-adminlte-input label="Phone No." name="phone" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($item_tax->Amount))
                <x-adminlte-input label="Tax Amount:" name="tax_amount" value="{{$item_tax->Amount}}" type="text" />
                @else
                <x-adminlte-input label=" Tax Amount:" name="tax_amount" type="text" />
                @endif
            </div>
            <div class="col-2">
                @if (isset($item_tax->CurrencyCode))
                <x-adminlte-input label="Tax CurrencyCode:" name="rrencyCode" value="{{$item_tax->CurrencyCode}}" type="text" />
                @else
                <x-adminlte-input label=" Tax CurrencyCode:" name="rrencyCode" type="text" />
                @endif
            </div>
            <div class="col-2">
                <div style="margin-top: 2.0rem;">
                    <x-adminlte-button label="Update" theme="success" id="update" class="label.update" icon="fas fa-save" type="submit" />
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('js')
<script type="text/javascript">
    //  $("#update_form").hide();
    $("#ord_search").on('click', function(e) {

        let $ordervalue = $('#orderid').val();
        if ($ordervalue == "") {
            alert("Enter OrderID");
            return false;
        } else {
            // $("#update_form").show();
        }
    });
    $("#update").on('click', function(e) {

        let $orderid = $('#identifier').val();
        if ($orderid == "") {
            alert("Search The Order To Be Updated");
            return false;
        }

    });
</script>
@stop