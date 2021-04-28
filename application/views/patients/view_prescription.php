<style type="text/css">
  .table td{
    padding-left:15px !important;
    padding-right: 15px !important; 
  }
  .price{
    font-weight: 600;
    font-size: 14px;
  }
  .formulation{
    background: #ebebeb;
    border-radius: 4;
  }
</style>
<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="#">Prescriptions List</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>        
         
      </ol>
  </div>
</div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-head card-default">
          
         <header> Name : <?php echo ucwords($patient_info->first_name." ".$patient_info->last_name); ?></header>

       
        
    </div>
      <div class="card-body"> 
          
        
					<table class="table table-bordered">
          <thead>
          <tr>
            <th>#</th>
            <th>Medicine</th>
            <th>Frequency</th>
            <th>Duration</th>
            <th>Qty</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>

  
        
        <?php 
            $i=1;
      
  
      
            foreach ($patient_prescription_drug as $key => $value) { 
      $M = 0;
      $dayM = "M";
      $dayA = "A";
      $dayN = "N";
                      $N = 0;
                      $A = 0;
                        $dose = 1;
                        $Mday = '';

                        if($value->preffered_intake == "AF"){
                          $intake = "After Food";
                        }
                        if($value->preffered_intake == "BF"){
                          $intake = "Before Food";
                        }
                      


                        if($value->day_schedule !=""){
                        $split_schedule = explode(",",$value->day_schedule);

                    if(in_array("M", $split_schedule)){
                      $M = "<span style='font-size:20px'>&#10004;</span>";
                      $dayM = "<span>M</span>";
                   
                    }
                    else{
                      $M = "<span style='font-size:20px'>&#215;</span>";
                      $dayM = "<span>M</span>";
                    }
                     if(in_array("A", $split_schedule)){
                      $A = "<span style='font-size:20px'>&#10004;</span>";
                      $dayA = "<span>A</span>";
                   
                    }
                    else{
                      $A = "<span style='font-size:20px'>&#215;</span>";
                      $dayA = "<span>A</span>";
                    }
                     if(in_array("N", $split_schedule)){
                      $N = "<span style='font-size:20px'>&#10004;</span>";
                      $dayN = "<span>N</span>";
                   
                    }
                    else{
                      $N = "<span style='font-size:20px'>&#215;</span>";
                      $dayN = "<span>N</span>";
                    }
      }

      
      ?>
          <tr>
            <td><?php echo $i++; ?></td>
            <td><span style="font-weight: bold"><?php if($value->trade_name != NULL || $value->trade_name != '') { echo strtoupper($value->formulation." ".$value->trade_name); }else{ echo strtoupper($value->formulation." ".$value->medicine_name); } ?></span><br>
              <span style="font-size: 13px;color:rgb(84,84,84);"><?php echo $value->composition; ?></span>
            
            </td>
            <td>
              <?php if($value->day_schedule==""||$value->day_schedule==NULL){ ?>
              <span><?php if($value->day_dosage =="stat" || $value->day_dosage =="sos" || $value->day_dosage =="HS"){ echo $value->day_dosage; } else { echo $value->day_dosage." times in a ".$value->dosage_frequency; } ?></span><br><span style="font-size: 13px;color:rgb(84,84,84);"><small><?php echo "(".$value->drug_dose." ".$value->dosage_unit." each )"; ?></small></span>
            <?php } else { ?>
              <span><?php echo $M.'   -   '.$A.'   -   '.$N; ?></span><br><span><?php echo $dayM.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayA.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayN; ?></span><br><span><small><?php echo $value->drug_dose." ".$value->dosage_unit." each"; ?></small></span>
            <?php } ?>

            </td>
            
            <!-- <td><?php if($value->dosage_frequency == "" || $value->dosage_frequency == NULL|| $value->dosage_frequency == "--") { echo "--"; } else { echo $value->dose_course." ".$value->dosage_frequency."(s)"; } ?><br> <?php   if($intake!="" || $intake!=NULL){ echo "(".$intake.")" ;} ?></span></td> -->
            
            <td><?php if($value->dose_course == 0 ){ echo "--"; } else { echo $value->dose_course." Days"; } ?><br> <?php   if($intake!="" || $intake!=NULL){ echo "(".$intake.")" ;} ?></td>
              <td><?php if($value->quantity == 0 ){ echo "--"; } else { echo $value->quantity; } ?></td>
            <td style="padding-top: 0"> <?php echo ucfirst($value->remarks); ?></td>
          </tr>
         
          <?php } ?>
          
        
        
  </tbody>

  </table>
		</div>
  </div>
</div>
</div>



<script type="text/javascript">
  function drop_invoice(bid)
{
  var base_url = '<?php echo base_url(); ?>';
  $.ajax({
          url : base_url+"/New_order/drop_pharmacy_invoice",
          method : "POST",
          data : {"bid":bid},
          success : function(rdata) { 
      alert('Invoice Dropped');
      window.location.href = "<?php echo base_url('Pharmacy_Billing'); ?>"; 
          }
    });
}
   $(document).ready(function(){
  
    // $("#payment_type").change(function(){
    //   var payment_type = $("#payment_type").val();
    //   if(payment_type == "cash" || payment_type == "" || payment_type == "card"){
    //      $("#div_cash").css("display","none");
    //   }else{
    //      $("#div_cash").css("display","block");
    //   }
    // });
  });
</script>