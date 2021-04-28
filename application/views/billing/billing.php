   <div class="page-bar">
  <div class="page-title-breadcrumb">
     <!--  <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url('Dashboard')?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">BILLING</li>
      </ol>
  </div>
</div>


<div class="row">
<div class="col-md-12">
    <div class="card">
        <div class="card-body">     
            <div class="row">
              <div class="col-md-12 pull-right">
                <button class="btn btn-app btn-xs pull-right">
                    <div id="daterange" style="cursor: pointer; padding: 5px 10px; width: 100%;">
                        <i class="fa fa-calendar-alt"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i> <i class="fas fa-spinner fa-spin bill_loader"></i>
                        <input class="startDate" type="hidden"> 
                        <input class="endDate" type="hidden"> 
                    </div>
                </button>
                <button class="btn btn-success btn-icon printInventory pull-right" style="margin-right: 5px">
                  <i class="fas fa-print"></i> Print Billing <i class="fa fa-spinner fa-spin spinloader"></i>
                </button>&emsp;
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <table id="doctorlist" class="table table-bordered customTable mt-5">
                    <thead>
                        <tr>
                            <th style="width: 5%">S#</th>
                            <th style="width: 12%">Inv. No. & Date</th>
                            <th>Name</th>
                            <th style="width: 10%">Cost</th>
                            <th style="width: 12%">Disc Amount</th>
                            <!-- <th style="width: 10%">Tax</th> -->
                            <th style="width: 12%">Amount Paid</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
              </div>
            </div>

        </div>
    </div>
</div>
</div>



 <script>
  $(document).ready(function () {
      // var table = $('#doctorlist').dataTable({});
  });
  </script>


<script type="text/javascript">
  $(document).ready(function(){
    $('.spinloader').hide();
    $('.printInventory').on("click",function(){
      $('.spinloader').show();
      var start_date = $('.startDate').val();
      var end_date = $('.endDate').val();   

      $.post("<?=base_url('Pharmacy_Billing/generateBillingPdf')?>",
      {
          date_from:start_date,
          date_to:end_date
      },
      function(data){
        console.log(data)
          $('.spinloader').hide();
          window.open("<?=base_url('uploads/clinicBillings/')?>"+data.trim(),"_blank");
      });
    });
  });
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

            $.post("<?=base_url('Pharmacy_Billing/Pharmacy_Billings')?>",
            {
                date_from:start_date,
                date_to:end_date
            },
            function(data){
                $('.bill_loader').hide();
                $('#doctorlist').DataTable().destroy();
                $('#doctorlist tbody').html(data);
                $('#doctorlist').DataTable();
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



