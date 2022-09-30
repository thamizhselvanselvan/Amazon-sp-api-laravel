@extends('adminlte::page')

@section('title', 'Exchange Rate')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row">
    <div class="col">
        <h1 class="m-0 text-dark">Catalog Exchange Rate</h1>
    </div>
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
    </div>
</div>

<div class="row">

    <div class="col-2"></div>

    <div class="col-8">
        <form action="{{ route('catalog.update.exchange.rate') }}" method="post">
            @csrf
            <div class="row">
                <div class="col">
                
                    <x-adminlte-select label="Choose Source-Destination" name="source_destination" class="source_destination">
                        <option value="NULL"> Select Source Destination </option>
                        <option value="ind_to_uae"> IND TO UAE </option>
                        <option value="ind_to_sg"> IND TO SG </option>
                        <option value="ind_to_sa"> IND TO SA </option>
                        <option value="usa_to_sg"> USA TO SG </option>
                        <option value="usa_to_uae"> USA TO UAE </option>
                        <option value="usa_to_ind_b2c"> USA TO IND B2C </option>
                        <option value="usa_to_ind_b2b"> USA TO IND B2B </option>
                    </x-adminlte-select>
                </div>

                <div class="col">
                    <x-adminlte-input label="Packaging" type="text" name="packaging" placeholder="Packaging" class="packaging" />  
                </div>

                <div class="col">
                    <x-adminlte-input label="Selling Price Commission" type="text" name="sp_commission" placeholder="Selling Price Commission" class="sp_commission" />
                </div>

            </div>

            <div class="row">

                <div class="col">
                    <x-adminlte-input label="Base Weight" type="text" name="base_weight" placeholder="Base Weight" class="base_weight" /> 
                </div>

                <div class="col">
                    <x-adminlte-input label="Seller Commission" type="text" name="seller_commission" placeholder="Seller Commission" class="seller_commission" />
                </div>

                <div class="col">
                    <x-adminlte-input label="Excerise Rate" type="text" name="excerise_rate" placeholder="Excerise Rate" class="excerise_rate" />
                </div>

            </div>

            <div class="row"> 

                <div class="col">
                    <x-adminlte-input label="Base Shipping Charge" type="text" name="base_shipping_charge" placeholder="Base Shipping Charge" class="base_shipping_charge" />
                </div>

                <div class="col">
                    <x-adminlte-input label="Duty Rate" type="text" name="duty_rate" placeholder="Duty Rate" class="duty_rate" />
                </div>

                <div class="col">
                    <x-adminlte-input label="Amazon Commission" type="text" name="amazon_commission" placeholder="Amazon Commission" class="amazon_commission" />
                </div>      
            </div>
            <p class="text-center"> <x-adminlte-button label="Update" type="submit" theme="success" icon="fa fa-refresh" class="btn-sm" /> </p>
        </form>
    </div>

    <div class="col-2"> </div>
    
</div>

@stop

@section('js')
<script type="text/javascript">

    $(document).ready(function(){

        $('.source_destination').change( function(event){
            event.preventDefault();
            let option = $(this).val();
            
            $.ajax({
                url: "/catalog/record/auto-load",
                method: "GET",
                data: {
                    "option": option,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    $.each(response, function(key, result){
                        
                        $('.base_weight').val(result['base_weight']);
                        $('.base_shipping_charge').val(result['base_shipping_charge']);
                        $('.packaging').val(result['packaging']);
                        $('.seller_commission').val(result['seller_commission']);
                        $('.duty_rate').val(result['duty_rate']);
                        $('.sp_commission').val(result['sp_commission']);
                        $('.excerise_rate').val(result['excerise_rate']);
                        $('.amazon_commission').val(result['amazon_commission']);
                    });
                },
            });

        });

    }); 

</script>
@stop

