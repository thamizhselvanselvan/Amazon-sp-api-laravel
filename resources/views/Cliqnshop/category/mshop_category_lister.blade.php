@extends('adminlte::page')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .table td {
            padding: 0;
            padding-left: 5px;
        }

        .table th {
            padding: 2;
            padding-left: 5px;
        }
    </style>
@stop
@section('title', 'Users Keyword Search Logs')

@section('content_header')
    <div class="row">
        <h3>CliqnShop Categories - Prohibit/Allow</h3>
    </div>



@stop

@section('content')
    @if (session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{ session()->get('success') }}
        </div>
    @endif
    <div class="alert_display">
        @if (request('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ request('success') }}</strong>
            </div>
        @endif
    </div>

    <div class="alert_display">
        @if (request('error'))
            <div class="alert alert-warning alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ request('error') }}</strong>
            </div>
        @endif
    </div>

    @if (session()->has('error'))
        <div class="alert alert-warning" role="alert">
            {{ session()->get('error') }}
        </div>
    @endif

    <div class="card card-info collapsed-card">
        <div class="card-header">
            <h3 class="card-title">Filter :)</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="card-body" style="display: none;">
            <div class="row">
                <div class="col-12">
                    <div>
                        <form class="form-inline" id="form-log-delete" method="post"
                            action="{{ route('cliqnshop.keyword.log.delete') }}">
                            @csrf
                            <div class="form-group">
            
                                <select class="form-control form-control-sm" id="select-timeline" name="select_timeline">
                                    <option>Select the Country</option>
                                    <option value="">country 1</option>
                                    <option value="">country 2</option>
            
                                </select>
                            </div>
            
                            <button type="submit" id="clear_log" class="btn btn-warning mx-2 btn-sm">Apply</button>
                        </form>
                    </div> 

                </div>

            </div>

        </div>

        <div class="card-footer" style="display: none;">
            Heyy !~
        </div>
    </div>

    


    <table class="table table-bordered data-table">
        <thead>
            <tr class="table-info">
                <th>ID</th>
                <th>Category Code</th>
                <th>Category Label</th>
                <th>site_code</th>
                <th>created-Time </th>
                <th>Banned-Time </th>
                <th>Ban/Unban </th>

            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $.extend($.fn.dataTable.defaults, {
            pageLength: 50,
        });

        $(function myFunction() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('cliqnshop.category') }}",

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },

                    // {
                    //     data: 'id',
                    //     name: 'id'
                    // },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'label',
                        name: 'label'
                    },
                    {
                        data: 'siteid',
                        name: 'siteid'
                    },
                    {
                        data: 'ctime',
                        name: 'ctime'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }


                ]
            });


        });






        $(document).on('change', ".actionSwitch", function(e) {

            let selectedCheckBox = $(this) ;
            let operation = $(this).is(":checked") ? 'BAN' : 'UNBAN';            
            let label =  $(this).data("catlabel");
            let bool = confirm(`Are you sure you want to ${operation} - ${label} - Category ?`);
            

            if(!bool) {
                $(selectedCheckBox).prop('checked', $(selectedCheckBox).is(":checked") ? false : true);
            }
            else
            {
                var formData = {
                                _token : '<?php echo csrf_token() ?>',
                                siteid: $(this).data("siteid"),
                                catCode : $(this).val(),
                                operation : $(this).is(":checked") ? 'add' : 'remove',
                                };
                jQuery.ajax({  
                    url: 'category/storebancategory',  
                    type: 'POST',  
                    data: formData,
                    success: function(data) { 
                            console.log(data);
                                                
                    } ,
                    error: function (jqXHR, exception) {

                        $(selectedCheckBox).prop('checked', $(selectedCheckBox).is(":checked") ? false : true);
                        console.log(jqXHR);
                    }, 
                });
                
                
            }

          

          

            
        });
    </script>



@stop
