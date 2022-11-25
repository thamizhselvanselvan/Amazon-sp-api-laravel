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
@section('title', 'Cliqnshop KYC Details')

@section('content_header')
<div class="row">
    <h3>Cliqnshop KYC Details</h3>
</div>
<div class="modal fade" id="kyc_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="customerCrudModal">Validate KYC</h4>
            </div>
            <div class="modal-body">

          <input type="hidden" name="cust_id" id="cust_id">

                <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-content">
                       
                          <img src="" class="showPicfront"> 
                    </div>
                        </div>
                    </div>
                </div>


                <div class="modal pic_back" tabindex="-1" role="dialog" aria-labelledby="pic_back" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                           <img src="" class="showPicback"> 
                        </div>
                    </div>
                </div>
                <div>
                    <h5> <b> Customer Name : </b><a name="cname" id="cname"></a> </h5>
                    <h5> <b> Document Type : </b><a name="dtype" id="dtype"></a> </h5>
                    <h4> <b> <a name="front" id="front"></a> Front : </b> <button type="button" class="btn btn-primary btn-sm m-2" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fa fa-eye"></i> View</button>
                        <h4> <b> <a name="front" id="front"></a> Back : </b> &nbsp;<button type="button" class="btn btn-primary btn-sm m-2" data-toggle="modal" data-target=".pic_back"><i class="fa fa-eye"></i> View</button>
                        </h4>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="kyc">KYC Status</label>
                        <select id="kyc_status" name="kyc_status" class="form-select ml-4" aria-label="Default select example">
                            <option value='0'>Select Status :</option>
                            <option value="1">Accept</option>
                            <option value="2">Reject</option>
                        </select>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group rea">
                            <strong>Reason For Rejecting:</strong>
                            <textarea name="reason" id="reason" class="form-control" placeholder="Please Enter The Reason For Rejecting The KYC"></textarea>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary" id="save_changes"><i class="fas fa-save"></i> Save changes</button>
                    <button type="button" class="btn btn-danger" id="close" data-dismiss="modal"><i class="fa fa-close" aria-hidden="true"></i> Close</button>
                </form>
            </div>
        </div>
    </div>
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
                <strong>{{request('success')}}</strong>
            </div>
            @endif
     </div>

     <div class="alert_display">
            @if (request('error'))
            <div class="alert alert-warning alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{request('error')}}</strong>
            </div>
            @endif
     </div>

@if (session()->has('error'))
<div class="alert alert-warning" role="alert">
    {{ session()->get('error') }}
</div>
@endif
<table class="table table-bordered data-table">
    <thead>
        <tr class="table-info">
            <th>ID</th>
            <th>Customer ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile No.</th>
            <th>KYC Status</th>
            <th>KYC Updted Date</th>
            <th>Rejection Reason</th>
            <th>Action</th>
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

    $(function() {

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('cliqnshop.kyc') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'telephone',
                    name: 'telephone'
                },
                {
                    data: 'kyc_status',
                    name: 'kyc_status'
                },
                {
                    data: 'kyc_aproved_date',
                    name: 'kyc_aproved_date'
                },
                {
                    data: 'rejection_reason',
                    name: 'rejection_reason'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

    });
    $('.rea').hide();
    $(document).on('click', '#kyc_aprove', function() {
        
        let selected_date = $('#search_date').val();
        let customer_id = $(this).attr('value');
        if (customer_id == '') {
            alert('No KYC File Found');
            return false;
        } else {
            $.ajax({
                method: 'get',
                url: "{{ route('cliqnshop.kyc.view') }}",
                data: {
                    'id': customer_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if(response == 'no kyc found' )
                    {
               window.location.href = '/cliqnshop/kyc?error= No KYC Found'
                    } else {

                        $('#kyc_modal').modal('show');
                     
                        $(".modal-body #cname").text(response['kyc'][0]['name']);
                        $(".modal-body #cust_id").val(response['kyc'][0]['customer_id']);
                        $(".modal-body #dtype").text(response['kyc'][0]['document_type']);
                        $(".modal-body #front").text(response['kyc'][0]['document_type']);
                        $('.showPicfront').attr('src', response['back_path_url']);
                        $('.showPicback').attr('src', response['back_path_url']);
                    }
                },
                error: function(response) {
    
                    alert('something went wrong');
                }
            });
        }
    });
    $('#close').click(function() {
        $('#kyc_modal').modal('hide');
    });



$('#kyc_status').on('change',function() {
   let  status = $('#kyc_status').val();
 if (status == '2') {
            $('.rea').show();
} else if(status == '1'|| status == 0)
{
     $('.rea').hide();
}
});



    $('#save_changes').click(function() {
      let  rea = $('#reason').val();
      let  status = $('#kyc_status').val();
      let  id = $('#cust_id').val();
      
        if (status == '0') {
            alert('Please Select The KYC Status');
            return false;
        } else if(rea == '')
        {
            alert('Please fill The reason For Rejecting The KYC');
            return false;
        }

        $.ajax({
                method: 'get',
                url: "{{ route('cliqnshop.kyc.update') }}",
                data: {
                    'id': id,
                    'rea' :rea,
                    'status' : status,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
              
                window.location.href = '/cliqnshop/kyc?success=KYC Status has  updated successfully'
                },
                error: function(response) {
                  
                    alert('something went wrong');
                }
            });
    });
</script>

@stop