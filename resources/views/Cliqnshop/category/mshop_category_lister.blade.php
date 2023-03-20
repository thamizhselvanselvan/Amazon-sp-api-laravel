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

    {{-- collapse filter card start --}}
    <div class="card card-info collapsed-card" id="filter-card">
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

        <div class="card-body" >
            <div class="row">
                <div class="col-12">
                    <div>
                        <form class="form-inline" id="form-log-delete" method="get"
                            action="{{ route('cliqnshop.category') }}">
                            
                            <div class="form-group">

                                <x-adminlte-select name="site_id" id="filterSites" class="form-control form-control-sm">
                                    <option value='' selected>Select the Site to filter</option>
                                    @foreach ($sites as $site)
                                        @if ($site->code == 'in')
                                            {{ $site->code = 'India' }}
                                        @elseif ($site->code == 'uae')
                                            {{ $site->code = 'UAE' }}
                                        @endif
                                        <option value="{{ $site->siteid }}">{{ $site->code }}</option>
                                    @endforeach
                                </x-adminlte-select>                                

                                <x-adminlte-select name="banned_status" id="bannedStates" class="form-control form-control-sm ml-2">
                                    <option value='' selected>Select the Status to filter</option>                                    
                                    <option value="banned">Banned</option>
                                    <option value="allowed">Allowed</option>                                    
                                </x-adminlte-select>

                            </div>
                           
                            <button type="submit" id="clear_log" class="btn btn-warning mx-2 btn-sm">Apply</button>
                            <a class="btn btn-default  btn-sm" href="{{route('cliqnshop.category')}}">Reset</a>
                        </form>
                    </div> 

                </div>

            </div>

        </div>

        <div class="card-footer" >
            Heyy !~
        </div>
    </div>
    {{-- collapse filter card -end --}}

    


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

        const filter = {
            site_id : new URLSearchParams(window.location.search).get('site_id'),
            banned_status : new URLSearchParams(window.location.search).get('banned_status'),
        }

        let url = `category?`;        
        let hasFilter = false;

        if(!!filter.site_id)
        {
            hasFilter = true;
            url += `site_id=${filter.site_id}`  ;

            var filterSites = document.getElementById('filterSites'), filterSite, i;
            for (i = 0; i < filterSites.length; i++) {
                filterSite = filterSites[i];
                if (filterSite.value == filter.site_id)
                {
                    filterSite.setAttribute('selected', true);
                }
            }

        }
        if (!!filter.banned_status) 
        {
            hasFilter = true;
            url += `&banned_status=${filter.banned_status}`  ;

            var bannedStates = document.getElementById('bannedStates'), bannedState, i;
            for (i = 0; i < bannedStates.length; i++) {
                bannedState = bannedStates[i];
                if (bannedState.value == filter.banned_status)
                {
                    bannedState.setAttribute('selected', true);
                }
            }
        }

        if(hasFilter)
        {
            $('#filter-card').removeClass("collapsed-card");
        }
        

        $.extend($.fn.dataTable.defaults, {
            pageLength: 50,
        });

        $(function myFunction() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: `${url}`,
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },

                    // {
                    //     data: 'id',
                    //     name: 'id'
                    // },
                    {
                        data: 'code',
                        name: 'mshop_catalog.code',   
                     
                    },
                    {
                        data: 'label',
                        name: 'mshop_catalog.label',
                        
                    },
                    {
                        data: 'siteid',
                        name: 'siteid',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ctime',
                        name: 'ctime',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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
