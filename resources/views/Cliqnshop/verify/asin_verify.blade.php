@extends('adminlte::page')

@section('title', 'Product Approval')

@section('content_header')

@stop


@section('content')
    

    <div class="row pt-4">
        <div class="col ">
            <div style="margin: 0.1rem 0; text-align: center" >
                <h3>Product Approve/Reject</h3>
            </div>
        </div>
    </div>
        
<div class="loader d-none">
    <div class="sub-loader position-relative ">
        <div class="lds-hourglass"></div>
        <p>Loading...</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if(session()->has('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session()->get('success') }}
        </x-adminlte-alert>
        @endif

        @if(session()->has('warning'))
        <x-adminlte-alert theme="warning" title="warning" dismissable>
            {{ session()->get('warning') }}
        </x-adminlte-alert>
        @endif

        @if(session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session()->get('error') }}
        </x-adminlte-alert>
        @endif
    </div>
</div>



{{-- collapse filter card start --}}
<div class="card card-info " id="filter-card">
    <div class="card-body" >
        <div class="row">
            <div class="col-12">
                <div>
                    <form class="form-inline" id="form-log-delete" method="get"
                        action="{{ route('cliqnshop.verify.asin') }}">
                        
                        <div class="form-group">

                            <x-adminlte-select name="site_id" id="filterSites"  class="form-control form-control-sm">
                                <option value='' selected>Select the Site to Apply filter</option>
                                @foreach ($sites as $site)
                                    @if ($site->code == 'in')
                                        {{ $site->code = 'India' }}
                                    @elseif ($site->code == 'uae')
                                        {{ $site->code = 'UAE' }}
                                    @endif
                                    <option value="{{ $site->siteid }}">{{ $site->code }}</option>
                                @endforeach
                            </x-adminlte-select>                                


                        </div>
                       
                        <button type="submit" id="clear_log" class="btn btn-warning mx-2 btn-sm">Apply</button>
                        <a class="btn btn-default  btn-sm" href="{{route('cliqnshop.verify.asin')}}">Reset</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- collapse filter card -end --}}

<table class="table table-bordered yajra-datatable table-striped" id='orderspending'>
    <thead>
        <tr class='text-bold bg-info'>
            <th> S.N </th>
            <th>ASIN</th>
            <th>SKU </th>
            <th>Product Name </th>
            <th>Site</th>
            <th>Status</th>
            {{-- <th>created-time</th>
            <th>created-by</th> --}}
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="data_display_pending">

    </tbody>
</table>
@stop
@section('js')
<script type="text/javascript">

    const filter = {
            site_id : new URLSearchParams(window.location.search).get('site_id'),
        }

        let url = `{{ route('cliqnshop.verify.asin') }}?`;  
        if(!!filter.site_id)
        {            
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
    
    $(function() {

        $.extend($.fn.dataTable.defaults, {
            pageLength: 50,

        });

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'asin',
                    name: 'asin'
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'label',
                    name: 'label'
                },
                {
                    data: 'site',
                    name: 'site'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                // {
                //     data: 'ctime',
                //     name: 'ctime'
                // },
                // {
                //     data: 'editor',
                //     name: 'editor'
                // },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },



            ]
        });

    });

    
    $(document).on('click', ".deleteAsin", function(e) {
        
        label = $(this).data('label');
        shortenlabel = label.substring(0, 20).concat('...');
        
        return confirm(`Are you sure? You want to ban/delete this  ( ${shortenlabel} ) product! `);
    });

    $(document).on('click', ".approveAsin", function(e) {
        
        label = $(this).data('label');
        shortenlabel = label.substring(0, 20).concat('...');
        
        return confirm(`Are you sure? You want to Approve this  ( ${shortenlabel} ) product! `);
    });

    
</script>
@stop