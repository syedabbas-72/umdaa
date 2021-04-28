   <div class="page-bar">
  <div class="page-title-breadcrumb">
     <!--  <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">ORDERS</li>
      </ol>
  </div>
</div>


<div class="row">
  <form class="col-md-12" method="POST" action="<?php echo base_url('pharmacy_Billing');?>" enctype="multipart/form-data" role="form">
            <div class="row col-md-12">
            
                
                       
                 
                                 
                           
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" class="form-control datepicker" name="date_from" id="appointment_date_from" placeholder=" Date From" required="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" class="form-control datepicker" name="date_to" id="appointment_date_to" placeholder="Date To" required="">
                            </div>
                        </div>
                                
                                                <div class="col-md-1">
                            <div class="form-group">
                                <input type="submit"  id="reset-filter" class="btn btn-danger" value="Go">
                            </div>
                          </div>
                            <div class="col-md-1 pull-right">
                           <?php if(isset($from) && isset($to)) { ?>
                                <a target="_blank" href="<?php echo base_url('pharmacy_Billing/billing_report/'.$from.'/'.$to); ?>" target="_blank"><i style="font-size:20px;position: relative;top: 7px;" class="fa fa-print"></i></a>

                              <?php } else {  ?>

                                <a target="_blank" href="<?php echo base_url('pharmacy_Billing/billing_report/'.date("d-m-Y").'/'.date("d-m-Y")); ?>" target="_blank"><i style="font-size:20px;position: relative;top: 7px;" class="fa fa-print"></i></a>

                              <?php }  ?>
                           
                        </div>
                    
                  

              

              
            
                </div>
              
                  </form>
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <table id="doctorlist" class="table table-bordered dt-responsive nowrap">
                <thead>
    <tr>
        <th style="width:10%" class="text-center">S.No:</th>
        <th style="width:15%">Inv. Date</th>
        <th style="width:45%">Description</th>
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
    <td><?php echo date("d-m-Y",strtotime($value->billing_date_time));?></td>
      <td><?php echo $value->guest_name.'<br>'.$value->guest_mobile; ?><br><?php echo $value->mobile; ?></td> 
        <!--<td><?php echo $value->dname; ?></td> -->
        <td class="text-right"><?php echo round($value->bamount,2); ?></td> 
    <?php if($value->billing_type=='Pharmacy'){ ?>
    <td class="text-center"><a href="<?php echo base_url('new_order/view_order/'.$value->billing_id);?>"><i class="fa fa-eye"></i></a>
          <a data-target = "tool-tip"  href="<?php echo base_url('/uploads/billings/'.$value->invoice_pdf);?>"><i class="fa fa-download"></i></a></td>
    <?php }else{?>
        <td class="text-center"><a href="<?php echo base_url('new_order/view_order/'.$value->billing_id);?>"><i class="fa fa-eye"></i></a>
          <a data-target = "tool-tip"  href="<?php echo base_url('uploads/billing/'.$value->invoice_pdf);?>"><i class="fa fa-download"></i></a></td>
    <?php } ?>
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



