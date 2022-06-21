@extends('adminlte::page')
@section('title', 'Search Invoice')

@section('css')
<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
<h1 class="m-0 text-dark">Invoice Filter</h1>
@stop
@section('content')

<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col">
            <form action="">
                @csrf
                <div class="form-group">
                    <label>Invoice Date:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control float-right datepicker" name='invoice_date' autocomplete="off" id="invoice_date">
                        <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="search" class="btn-sm ml-2" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('js')
<script type="text/javascript">
    $(document).ready(function(){
        $(".datepicker").daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
            },
        });
        $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        
        $('#search').click(function(){
            var invoice_date = $('#invoice_date').val();
            alert(invoice_date);

            $.ajax({
                method: 'POST',
                url: "{{ url('/invoice/select-invoice')}}",
                data:{ 
                "invoice_date": invoice_date,
                "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    console.log(response);
                alert('Export pdf successfully');
                }
            });
        });
    });
</script>
@stop