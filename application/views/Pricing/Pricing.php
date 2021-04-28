<style type="text/css">
  .pointer:hover
  {
    cursor: pointer;
  }
  .disabled
  {
  	cursor: none;
  }
</style>
<?php
$clinic_id = $this->session->userdata("clinic_id");
?>

<div class="page-bar">
  <div class="page-title-breadcrumb">
    <ol class="breadcrumb page-breadcrumb">
      <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("Dashboard"); ?>">UMDAA</a>&nbsp;<i class="fa fa-angle-right"></i>
      </li>
      <li class="active">PRICING</li>
    </ol>
  </div>
</div>

<!-- Modal Start -->
  <div id="clinicsModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title clinicModalTitle text-capitalize">UMDAA CLINICS</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body clinicBody">
          <h4 class="text-center clinicLoader">Getting Data <i class="fa fa-spinner fa-spin"></i></h4>
        </div>
      </div>

    </div>
  </div>
<!-- Modal Ends -->

<!-- Modal Start -->
  <div id="Modulemodal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title ModulemodalTitle text-capitalize">UMDAA CLINICS</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body ModuleBody">
          <div class="row">
            <div class="col-md-12">
              <table class="table table-bordered customTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Clinic Name</th>
                    <th>Role</th>
                    <th>Username</th>
                  </tr>
                </thead>
                <tbody class="ModuleTableBody">
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
<!-- Modal Ends -->

<!-- Modal Start -->
  <div id="SetPricingModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title text-capitalize">UMDAA CLINICS</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <form method="post" action="<?=base_url('Pricing/SetPricing')?>">
                <div class="form-group">
                  <h4 class="m-0 page-title p-0">Pricing Per Invoice</h4>
                </div>
                <div class="form-group">
                  <label class="trade_name">Consultation</label>
                  <input type="text" class="form-control" name="consultation" onkeypress="return decimal()" value="<?=$umdaaPricing->consultation?>">
                </div>
                <div class="form-group">
                  <label class="trade_name">Lab</label>
                  <input type="text" class="form-control" name="lab" onkeypress="return decimal()" value="<?=$umdaaPricing->lab?>">
                </div>
                <div class="form-group">
                  <label class="trade_name">Pharmacy</label>
                  <input type="text" class="form-control" name="pharmacy" onkeypress="return decimal()" value="<?=$umdaaPricing->pharmacy?>">
                </div>
                <div class="form-group">
                  <label class="trade_name">Procedure</label>
                  <input type="text" class="form-control" name="procedure" onkeypress="return decimal()" value="<?=$umdaaPricing->procedure?>">
                </div>
                <div class="form-group">
                  <label class="trade_name">Registration & Consultation</label>
                  <input type="text" class="form-control" name="registration" onkeypress="return decimal()" value="<?=$umdaaPricing->registration?>">
                </div>
                <div class="form-group">
                  <label class="trade_name">SMS</label>
                  <input type="text" class="form-control" name="sms" onkeypress="return decimal()" value="<?=$umdaaPricing->sms?>">
                </div>
                <div class="form-group text-center">
                  <button class="btn btn-app" name="submit">Submit</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
<!-- Modal Ends -->

<!-- Modal Start -->
  <div id="viewPricingModal" class="modal fade" role="dialog" id="doctorlist1">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title viewPricingModalTitle text-capitalize">UMDAA CLINICS</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
        	<div class="row">
        		<div class="col-md-12 ">
        			<button class="btn btn-dark pull-right" id="generateInvoice">Generate Invoice <i class="fa fa-spinner fa-spin generateLoader"></i></button>
        		</div>
        	</div>
          <table class="table table-bordered customTable pricingTable" >
            <thead>
              <tr>
                <th>#</th>
                <th>Month - Year</th>
                <th>Total Invoices</th>
                <th>Total Cost</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="pricingBody">
              
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
<!-- Modal Ends -->


<section class="main-content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="row">
          <div class="col-md-12 pull-right">
            <button class="btn btn-app pull-right" data-toggle="modal" data-target="#SetPricingModal">Set Pricing for invoices</button>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <table class="table table-bordered customTable " id="doctorlist">
              <thead>
                <tr>
                  <th>
                    <div class="row">
                      <div class="col-md-12">#</div>
                    </div>
                  </th>
                  <th  style="width: 30%">
                    <div class="row">
                      <div class="col-md-12">Doctor</div>
                    </div>
                  </th>
                  <th class="text-center">Invoices
                    <div class="row" style="border-top: 1px solid #ddd">
                      <div class="col-md-4">
                        APPROVED
                      </div>
                      <div class="col-md-4">
                        CANCELLED
                      </div>
                      <div class="col-md-4">
                        TOTAL
                      </div>
                    </div>
                  </th>
                  <th style="width: 15%">
                    <div class="row">
                      <div class="col-md-12">Module Info</div>
                    </div>
                  </th>
                  <th style="width: 10%">
                    <div class="row">
                      <div class="col-md-12">Status</div>
                    </div>
                  </th>
                  <th style="width: 10%">
                    <div class="row">
                      <div class="col-md-12">Actions</div>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php
                $i = 1;
                $today = date_create(date("Y-m-d"));
                $date_t = date("Y-m-d");
                foreach ($doctors_list as $value) 
                {
                  $approved = $this->db->query("select count(*) as count from billing where status='1' and doctor_id='".$value->doctor_id."'")->row();
                  $dropped = $this->db->query("select count(*) as count from billing where status='2' and doctor_id='".$value->doctor_id."'")->row();
                  $docInfo = $value->doctor_id.":".$value->first_name." ".$value->last_name;
                  $invoiceInfo = $this->db->query("select * from invoice_pricing where doctor_id='".$value->doctor_id."' and payment_status='0'")->row();
                  $appointmentInfo = $this->db->query("select count(*) as count, max(appointment_date) as appointment_date from appointments where doctor_id='".$value->doctor_id."' and appointment_date<='".$date_t."' order by appointment_id DESC LIMIT 1")->row();
                  $apDate = date_create($appointmentInfo->appointment_date);
                  $diff = date_diff($apDate,$today);
                  ?>
                  <tr>
                    <td><?=$i?></td>
                    <td><span class="trade_name">Dr. <?=ucwords(strtolower($value->first_name." ".$value->last_name))?></span><br><span class="formulation m-0"><?=$value->qualification." - ".$value->department_name?></span>
                       <label class="pull-right pointer viewDocCLinics" data-toggle="modal" data-target="#clinicsModal" data-value="<?=$docInfo?>"><i class="fa fa-info-circle"></i></label>
                       <br>
                       <?php
                       // echo "select count(*) as count, appointment_date from appointments where doctor_id='".$value->doctor_id."' order by appointment_id DESC LIMIT 1";
                       if($appointmentInfo->count<=0)
                       {
                          echo '<label class="text-muted font-weight-bold"><i class="fas fa-dot-circle"></i> Not Used Yet</label>';
                       }
                       elseif($appointmentInfo->count>0)
                       {
                          if($diff->format("%a")<=0)
                          {
                            echo '<label class="text-success font-weight-bold"><i class="fas fa-dot-circle"></i> Last Appointment Today</label>';
                          }
                          elseif($diff->format("%a")<=1 && $diff->format("%a")>0)
                          {
                            echo '<label class="text-success font-weight-bold"><i class="fas fa-dot-circle"></i> Last Used '.date('d M Y',strtotime($appointmentInfo->appointment_date)).'</label>';
                          }
                          elseif($diff->format("%a")>1 && $diff->format("%a")<=4)
                          {
                            echo '<label class="text-warning font-weight-bold"><i class="fas fa-dot-circle"></i> Last Used '.$diff->format("%a").' Days Ago.</label>';
                          }
                          elseif($diff->format("%a")>4 && $diff->format("%a")<=7)
                          {
                            echo '<label class="text-danger font-weight-bold"><i class="fas fa-dot-circle"></i> Last Used '.$diff->format("%a").' Days Ago.</label>';
                          }
                          elseif($diff->format("%a")>7)
                          {
                            echo '<label class="text-danger font-weight-bold"><i class="fas fa-dot-circle"></i> Used Few Days Ago.</label>';
                          }
                       }
                       ?>
                       <span class="font-weight-bold"><?=($appointmentInfo->appointment_date!="")?date("M d Y",strtotime($appointmentInfo->appointment_date)):''?></span>
                       <br>
                       <span>Date Of Joining : <?=($value->docDate!="" || $value->docDate!=NULL)?date("M d Y",strtotime($value->docDate)):'-'?>&emsp;MOB : <?=$value->mobile?></span>
                    </td>
                    <td>
                      <div class="row">
                        <div class="col-md-4 text-center"><?=$approved->count?> 
                          <label class="pull-right pointer getApprovedInvoices" data-toggle="modal" data-target="#clinicsModal" data-value="<?=$docInfo?>"><i class="fa fa-info-circle"></i></label></div>
                        <div class="col-md-4 text-center"><?=$dropped->count?> 
                          <label class="pull-right pointer getCancelledInvoices" data-toggle="modal" data-target="#clinicsModal" data-value="<?=$docInfo?>"><i class="fa fa-info-circle"></i></label></div>
                        <div class="col-md-4 text-center"><?=$approved->count+$dropped->count?> 
                          <label class="pull-right pointer getTotalInvoices" data-toggle="modal" data-target="#clinicsModal" data-value="<?=$docInfo?>"><i class="fa fa-info-circle"></i></label></div>
                      </div>
                    </td>
                    <td><button class="btn btn-app checkModuleStatus" id="<?=$docInfo?>" data-toggle="modal" data-target="#Modulemodal">Check Info</button></td>
                    <td class="text-center"><span class="formulation <?=($invoiceInfo->status=='Charge')?'bg-danger':''?>"><?=($invoiceInfo->status=="")?'NA':$invoiceInfo->status?></span></td>
                    <td>
                      <button class="btn btn-app checkPaymentStatus" id="<?=$docInfo?>" data-toggle="modal" data-target="#viewPricingModal">View More</button>
                    </td>
                  </tr>
                  <?php
                  $i++;
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>              
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
	$('.generateLoader').hide();
  //Get Clinics 
  $(document).on("click",".viewDocCLinics",function(){
    var datavalue = $(this).attr("data-value").split(":");
    $(".clinicModalTitle").html("Dr. "+datavalue[1]);
    $('.clinicBody').empty();
    $('.clinicBody').html('<h4 class="text-center clinicLoader">Getting Data <i class="fa fa-spinner fa-spin"></i></h4>');
    $.post("<?=base_url('Pricing/getClinicsList')?>",{doctor_id:datavalue[0]},function(data){
      $('#clinicsModal .modal-dialog').addClass("modal-lg");
      $('.clinicBody').html(data);
    });
  });
  // Get Approved Invoices
  $(document).on("click",".getApprovedInvoices",function(){
    var datavalue = $(this).attr("data-value").split(":");
    $(".clinicModalTitle").html("Dr. "+datavalue[1]);
    $('.clinicBody').empty();
    $('.clinicBody').html('<h4 class="text-center clinicLoader">Getting Data <i class="fa fa-spinner fa-spin"></i></h4>');
    $.post("<?=base_url('Pricing/getInvoicesCount')?>",{doctor_id:datavalue[0],type:'1'},function(data){
      $('#clinicsModal .modal-dialog').removeClass("modal-lg");
      $('.clinicBody').html(data);
    });
  });
  // Get Cancelled Invoices
  $(document).on("click",".getCancelledInvoices",function(){
    var datavalue = $(this).attr("data-value").split(":");
    $(".clinicModalTitle").html("Dr. "+datavalue[1]);
    $('.clinicBody').empty();
    $('.clinicBody').html('<h4 class="text-center clinicLoader">Getting Data <i class="fa fa-spinner fa-spin"></i></h4>');
    $.post("<?=base_url('Pricing/getInvoicesCount')?>",{doctor_id:datavalue[0],type:'2'},function(data){
      $('#clinicsModal .modal-dialog').removeClass("modal-lg");
      $('.clinicBody').html(data);
    });
  });
  // Get All Invoices
  $(document).on("click",".getTotalInvoices",function(){
    var datavalue = $(this).attr("data-value").split(":");
    $(".clinicModalTitle").html("Dr. "+datavalue[1]);
    $('.clinicBody').empty();
    $('.clinicBody').html('<h4 class="text-center clinicLoader">Getting Data <i class="fa fa-spinner fa-spin"></i></h4>');
    $.post("<?=base_url('Pricing/getInvoicesCount')?>",{doctor_id:datavalue[0],type:'All'},function(data){
      $('#clinicsModal .modal-dialog').removeClass("modal-lg");
      $('.clinicBody').html(data);
    });
  });
  // Get All Paid Invoices to Umdaa 
  $(document).on("click",".checkPaymentStatus",function(){
    var datavalue = $(this).attr("id").split(":");
    $(".viewPricingModalTitle").html("Dr. "+datavalue[1]);
    $('#generateInvoice').attr("data-id",datavalue[0]);
    $('.pricingBody').empty();
    $.post("<?=base_url('Pricing/getPaidInvoices')?>",{doctor_id:datavalue[0]},function(data){
      $('.pricingBody').html(data);
    });
  });
  // Generate Invoice For Last Month
  $(document).on("click","#generateInvoice",function(){
    var datavalue = $(this).attr("data-id");
    $('#generateInvoice').html('Generating Invoice. Please Wait <i class="fa fa-spinner fa-spin"></i>');
    $('#generateInvoice').addClass('disabled');
    $.post("<?=base_url('Pricing/generateInvoices')?>",{doctor_id:datavalue},function(data){
      // console.log(data);
      if(data==0)
      {
        alert("Already Generated");
        $('#generateInvoice').html('Generate Invoice for Last Month');
        $('#generateInvoice').removeClass('disabled');
      }
      else
      {
        alert("Generated");
        $('#generateInvoice').html('Generate Invoice for Last Month');  
        $('#generateInvoice').removeClass('disabled');
        $.post("<?=base_url('Pricing/getPaidInvoices')?>",{doctor_id:datavalue},function(data){
          $('.pricingBody').html(data);
        });   
      }
      
    });
  });
  // Check Module Info
  $(document).on("click",".checkModuleStatus",function(){
    var datavalue = $(this).attr("id").split(":");
    $(".ModulemodalTitle").html("Dr. "+datavalue[1]);
    $('.ModuleTableBody').empty();
    $.post("<?=base_url('Pricing/getModuleInfo')?>",{doctor_id:datavalue[0]},function(data){
      $('.ModuleTableBody').html(data);
    });
  });


  // Check Billing For Upto Now
  // $(document).on("click","#checkThisMonth",function(){
  //   var datavalue = $(this).attr("data-id");
  //   var start = "<?=date('Y-m')?>-01";
  //   var end = "<?=date('Y-m')?>-31";
  //   $('#checkThisMonth').html('Generating Invoice. Please Wait <i class="fa fa-spinner fa-spin"></i>');
  //   $('#generateInvoice').addClass('disabled');
  //   $.post("<?=base_url('Pricing/generateInvoices')?>",{doctor_id:datavalue,start:start,end:end},function(data){
  //     if(data==0)
  //     {
  //       alert("Already Generated");
  //     $('#generateInvoice').html('Generate Invoice for Last Month');
  //     $('#generateInvoice').removeClass('disabled');
  //     }
  //     else
  //     {
  //       alert("Generated");
  //     $('#generateInvoice').html('Generate Invoice for Last Month');
  //       $('#generateInvoice').removeClass('disabled');
  //       $.post("<?=base_url('Pricing/getPaidInvoices')?>",{doctor_id:datavalue},function(data){
  //       $('.pricingBody').html(data);
  //     });   
  //     }
      
  //   });
  // });


</script>
<script type="text/javascript">
  $(document).ready(function(){
      $('#doctorlist').DataTable();
      $('#doctorlist1').DataTable();
  });
</script>

