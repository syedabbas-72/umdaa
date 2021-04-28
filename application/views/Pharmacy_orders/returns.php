<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">Pharmacy Returns</li>
        </ol>
    </div>
</div>


<!-- Show Billing Modal -->
<div id="billModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body p-3">
        <h1 class="text-center text-success" style="font-size:65px !important"><i class="fas fa-check-circle"></i></h1>
        <h3 class="text-center text-primary">Return Successful</h3>
        <h4 class="text-center">You have to pay <br><span class="font-weight-bold text-primary returnAmt">Rs. <?=$this->session->flashdata('showBill')?>/-</span><br> to customer</h4>
        <p class="text-center">
            <!-- <a class="return_link btn btn-outline-warning">Print Invoice</a> -->
        </p>
      </div>
    </div>

  </div>
</div>
<!-- Show Billing modal ENds -->

<!-- Returns Modal -->
<div id="returnsModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Billing Info</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="billing_details">
        <p>Please Wait Loading Data... <i class="fas fa-spinner fa-spin"></i></p>
      </div>
    </div>

  </div>
</div>
<!-- Search Billing Modal -->
<div id="searchModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Search Bill</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row justify-content-around">
            <div class="col-md-8">
                <div class="form-group">
                    <label class="font-weight-bold">Billing Invoice No</label>
                    <input class="form-control" type="text" id="billInvoiceNo">
                </div>
                <div class="form-group text-center">
                    <button class="btn btn-app searchBill">Submit</button>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12" id="searchedData"></div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="page-title">Pharmacy Returns
            <button class="btn btn-app pull-right" data-toggle="modal" data-target="#searchModal">Returns</button>
        </h4>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-app btn-xs pull-right mt-3">
                    <div id="daterange" style="cursor: pointer; padding: 5px 10px; width: 100%;">
                        <i class="fa fa-calendar-alt"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i> <i class="fas fa-spinner fa-spin bill_loader"></i>
                        <input class="startDate" type="hidden"> 
                        <input class="endDate" type="hidden"> 
                    </div>
                </button>
                <table class="customTable returnsTable">
                    <thead>
                        <!-- <tr>
                            <th colspan="5" class="text-center">Pharmacy Returns - <?=date('F d Y')?></th>
                        </tr> -->
                        <tr>
                            <th>#</th>
                            <th>Invoice No & Return Date</th>
                            <th>Customer Info</th>
                            <th>Item Information</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="return_table">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
if($this->session->flashdata('showBill') != ''){
    ?>
    <script>
    $(document).ready(function(){
        $('#billModal').modal()
    })
    </script>
    <?php
}
?>
<script>
    $(document).ready(function(){
        // $('#billModal').modal()
        $('.returnsTable').DataTable();

        $('.searchBill').on("click", function(){
            var invoice_no = $('#billInvoiceNo').val()
            if(invoice_no != ""){
                $.post("<?=base_url('Pharmacy_orders/getBillInfo')?>", {invoice_no: invoice_no}, function(data){
                    console.log(data)
                    $('#searchedData').html(data)
                })
            }
        })

    })
</script>

<script>
$(document).on("click",".checkall",function(){
    if($('.checkall').prop("checked") == true){
        $('.checkall_sub').prop("checked", true)
        $('.checkall_sub').each(function(){
            var id = $(this).val()
            console.log(id)
            $('#txt_'+id).removeAttr("readonly")
        })
    }
    else{
        $('.checkall_sub').prop("checked", false)
        $('.checkall_sub').each(function(){
            var id = $(this).val()
            $('#txt_'+id).attr("readonly", true)
        })
    }
})
$(document).on("click",".checkall_sub",function(){
    $('.checkall').prop("checked", false)
    if($(this).prop("checked") == true){
        var id = $(this).val()
        $('#txt_'+id).removeAttr("readonly")
    }
    else{
        var id = $(this).val()
        $('#txt_'+id).attr("readonly", true)
    }
})
$(document).on("click",".getBillInfo", function(){
    var billing_id = $(this).attr('data-id')
    $('#searchModal').modal('hide')
    // alert(billing_id)
    $.post("<?=base_url('Pharmacy_orders/getBillDetails')?>", {billing_id:billing_id}, function(data){
        $('#billing_details').html(data);
    })
})
$(document).on('input','.qtyInput', function(){
    var max = $(this).attr('data-value')
    var value = $(this).val()
    if(parseInt(max) < parseInt(value)){
        alert('Quantity Should Be Lesser Than Sold Quantity')
        $(this).val(max)
    }
    else if(parseInt(value) == 0){
        alert('Minimum Return Quantity Should Be 1')
        $(this).val('1')
    }
})
$(document).on("click",".return",function(){
    $('.qtyInput').removeClass("d-none");
    $('.qtyText').addClass("d-none");
    $('.rsubmit').removeClass('d-none')
    $(this).addClass('d-none')
})
</script>

  <script>
  function doconfirm()
    {
        if(confirm("Are You Sure You Want To Delete?")){
            return true;
        }else{
            return false;  
        } 
   }
  </script>

<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.js"></script>

<script type="text/javascript">

    $('.bill_loader').hide();
    $(function() {

        var start = moment().subtract(0, 'days');
        var end = moment();

        function cb(start, end) {
            $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            
            var start_date = start.format('YYYY-MM-DD');
            var end_date = end.format('YYYY-MM-DD');
            
            $('#daterange .startDate').val(start_date);
            $('#daterange .endDate').val(end_date);   
            $('.bill_loader').show();   

            $.post("<?=base_url('Pharmacy_orders/ReturnsData')?>",
            {
                date_from:start_date,
                date_to:end_date
            },
            function(data){
                console.log(data)
                $('.bill_loader').hide();
                $('.returnsTable').DataTable().destroy();
                $('.returnsTable tbody.return_table').html(data);
                $('.returnsTable').DataTable();
            });
        }

        $('#daterange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

    });
</script>


