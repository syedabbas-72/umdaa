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
                    <span></span> <i class="fa fa-caret-down"></i>
                    <input class="startDate" type="hidden"> 
                    <input class="endDate" type="hidden"> 
                </div>
            </button>
            </div>
            <table id="doctorlist" class="table table-bordered dt-responsive nowrap">
                <thead>
    <tr>
        <th style="width:10%" class="text-center">S.No:</th>
        <th style="width:15%">Inv. Date</th>
        <th style="width:45%">Patient Name</th>
        <th style="width:45%">Billing Type</th>
        <th style="width:20%" class="text-right">Inv. Amount</th>
        <th style="width:20%" class="text-center">Action</th>
                               
    </tr>
</thead>
<tbody>
    <?php 
    $i=1; 
    foreach ($billing as $value) { 
    ?> 
    <tr>
        <td class="text-center"><?php echo $i++;?></td>
        <!--<td><?php //echo date_format($value->billing_date_time,"d-m-Y")?></td>-->
    <td><?php echo date("d-m-Y",strtotime($value->created_date_time));?></td>
      <td><?php echo $value->pname." ".$value->lname." [".$value->umr_no."]".'<br>'.$value->mobile; ?></td> 
        <!--<td><?php echo $value->dname; ?></td> -->
        <td class="text-left"><?php echo $value->billing_type; ?></td>
        <td class="text-right"><?php echo round($value->bamount,2); ?></td> 
         
        <td class="text-center"><a href="<?php echo base_url('billing/view_order/'.$value->billing_id);?>"><i class="fa fa-eye"></i></a>
        </td>
  
    </tr>
  <?php } ?>  
                </tbody>
            </table>

        </div>
    </div>
</div>
</div>



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



<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.js"></script>

<script type="text/javascript">
    $(function() {

        var start = moment().subtract(0, 'days');
        var end = moment();

        function cb(start, end) {
            $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            
            var start_date = start.format('YYYY-MM-DD');
            var end_date = end.format('YYYY-MM-DD');
            
            $('#daterange .startDate').val(start);
            $('#daterange .endDate').val(end);      

            alert(start_date+" "+end_date);
            $.post("<?=base_url('Billing/getBillings')?>",
            {
                startDate:start_date,
                endDate:end_date
            },
            function(data){
                var data = data.split("*");
                console.log(data);
                $('.consultationsAmount').html("<i class='fas fa-rupee-sign'></i> "+data[0]);
                $('.registrationsData').html(data[1]);
                $('.proceduresData').html(data[2]);
                $('.investigationData').html(data[3]);
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

