   <div class="page-bar">
  <div class="page-title-breadcrumb">
     <!--  <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">BILLINGS</li>
      </ol>
  </div>
</div>


<div class="row">
  
<div class="col-md-12">
    <div class="card">
        <div class="card-body">          
            <div class="row pull-right">
              <button class="btn btn-app btn-xs pull-right">
                <div id="daterange" style="cursor: pointer; padding: 5px 10px; width: 100%;">
                    <i class="fa fa-calendar-alt"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i> <i class="fas fa-spinner fa-spin bill_loader"></i>
                    <input class="startDate" type="hidden"> 
                    <input class="endDate" type="hidden"> 
                </div>
            </button>
            </div>
            
            <table id="doctorlist" class="table table-bordered dt-responsive nowrap">
                <thead>
    <tr>
        <th>S.No</th>
        <th>Inv. Date</th>
        <th>Patient Name</th>
        <th>Billing Type</th>
        <th>Inv. Amount</th>
        <th>Action</th>
                               
    </tr>
</thead>
<tbody class="billings_body">
    <?php 
    $i=1; 
    foreach ($billing as $value) { 
        $discount = $value->discount;
        $discount_unit = $value->discount_unit;
        $total_amount = $value->total_amount;
        $disc = $discount."%";
    ?> 
    <tr>
        <td><?php echo $i++;?></td>
        <td><?php echo date("d-m-Y",strtotime($value->created_date_time));?></td>
        <td><?php echo ucwords($value->pname." ".$value->lname)." [".$value->umr_no."]".'<br>'.$value->mobile; ?></td> 
        <td><?php echo $value->billing_type; ?></td>
        <td><i class="fas fa-rupee-sign"></i> 
        <?php 
        if($discount_unit=="INR")
        {
            echo $total_amount-$discount;
        }
        else
        {
             echo $total_amount-(($disc/100)*$total_amount);
        }
        ?></td>          
        <td>
            <a href="<?php echo base_url('billing/view_order/'.$value->billing_id);?>"><i class="fa fa-eye"></i></a>&nbsp;
            <a href="<?php echo base_url('patients/print_invoice/'.$value->appointment_id.'/'.$value->billing_id);?>"><i class="fas fa-print"></i></a>
        </td>  
    </tr>
  <?php } ?>  
                </tbody>
            </table>

        </div>
    </div>
</div>
</div>






<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.js"></script>

 <script>
  $(document).ready(function () {
      $('#doctorlist').dataTable();
  });
  </script>
  <script>
  function doconfirm()
    {
        if(confirm("Delete selected messages ?")){
            return true;
        }else{
            return false;  
        } 
   }
  </script>

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

            $.post("<?=base_url('Billing/getBillings')?>",
            {
                startDate:start_date,
                endDate:end_date
            },
            function(data){
                console.log(data);
                $('.bill_loader').hide();
                $('.billings_body').html(data);
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

