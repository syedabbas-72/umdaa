<style type="text/css">
  .vital_row{
    width: 50%;
  }
  .vital_txt{
    width: 20%;
  }
</style>
<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                              
                                <li class="active">Add Vitals</li>
                            </ol>
                        </div>
                    </div>
              <!-- content start -->

<div class="row">
 
  <div class="col-md-12">
     <div class="card"> 
    
     
    

<div class="row">
                                    <div class="col-lg-4 text-center">
                                        
                                            <div class="panel-body"><b>PATIENT:</b> <?php echo ucwords($patient_info->first_name." ".$patient_info->last_name); ?></div>
                                        
                                    </div>
                                    <div class="col-lg-4 text-center">
                                       
                                            <div class="panel-body"><b>UMR NO:</b> <?php echo $patient_info->umr_no; ?></div>
                                      
                                    </div>
                                    <div class="col-lg-4 text-center">
                                       
                                            <div class="panel-body"><b>MOBILE:</b> <?php echo $patient_info->mobile; ?></div>
                                    
                                    </div>
                                </div>      
     
    <hr style="margin: 0">
    <div class="card-body">
     <div class="row col-md-12"> 
         
          <div class="col-md-3" id="view_casesheet">
 <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                
             <div class="col-md-12">
    <div class="form-group ulgroup" ><ul>
  <?php

  foreach($profile_pages as $keys=>$values){ ?>
      <a class='nav-link' id='<?php echo $values->user_entity_alias; ?>' href="<?php echo base_url($values->entity_url.'/'.$patient_id.'/'.$appointment_id); ?>"><?php echo $values->user_entity_name; ?></a>
     
 <?php }
      
      
     echo '</ul></div></div>';
?>
            
            </div></div>