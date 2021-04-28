<link href="<?php echo base_url('assets/css/select2.min.css');?>" rel='stylesheet' type='text/css'>
<style type="text/css">
  .select2-container {
    border: 1px solid #dde6e9;
    outline: 0;
}
</style>
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">FOLLOW-UP  LIST</a></li>
          <li class="breadcrumb-item active"><a href="#">EDIT</a></li>          
        </ol>
  </div>
</div>
<section class="main-content">
    <div class="row">             
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                 <div class="col-md-12">
                  <?php echo form_open("followup_templates/edit/".$template_val->followup_id."");?>
                  <div class="row col-md-12">
                         <div class="row col-md-12">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="FollowUpTitle" class="col-form-label">Follow-up Title</label>
                                  <input type="text" name="name" id="FollowUpTitle"  class="form-control" value ="<?php echo $template_val->followup_name; ?>" required="required">
                                  <input type="hidden" name="fp_id" id="FollowUpTitle"  class="form-control" value ="<?php echo $template_val->followup_id; ?>" required="required">
                              </div>
                          </div>

                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="FollowUpTitle" class="col-form-label">Parameters</label>
                                 <select id="params" class="form-control" name="param[]" required="">
                                 
                                   <?php

                                   foreach ($parameters_list as $key => $value) { ?>
                                    <option value="<?php echo $value->parameter_id; ?>"><?php echo $value->parameter_name; ?></option>
                                 <?php  }
                                   ?>
                                 </select>
                              </div>
                          </div>
                         
                      </div> 
                      <div class="row col-md-12">
                         <div class="col-md-6">
                           <table id="prescription" width="100%" style="margin-left: 15px;" class="items">
                              <tr>
                                <th style="padding: 5px;">Clinical Parameters</th>
                                
                              </tr>

                              <?php $i=1; foreach ($c_params as $key => $value) { ?>
                                <tr>
                                 

                                  <td style="padding: 5px;"><?php echo $value->parameter_name; ?></td>
                                   <td><a href="javascript:;" id="<?php echo $value->parameter_id; ?>" class="btn btn-danger btn-xs btn-circle delete-parameter"><i class="fa fa-times"aria-hidden="true"></i></a></td>


                                </tr>
                              <?php } ?>
                            </table>

                            <table id="prescription" width="100%" style="margin-left: 15px;margin-top:10px" class="items">
                              <tr>
                                <th style="padding: 5px;">Lab Parameters</th>
                                
                              </tr>

                              <?php 
                              if(count($l_params)> 0){
                              $i=1; foreach ($l_params as $key => $value) { ?>
                                <tr>
                                 

                                  <td style="padding: 5px;"><?php echo $value->parameter_name; ?></td>
                                   <td><a href="javascript:;" id="<?php echo $value->parameter_id; ?>" class="btn btn-danger btn-xs btn-circle delete-parameter"><i class="fa fa-times"aria-hidden="true"></i></a></td>


                                </tr>
                              <?php }} else{
                                echo "<tr><td>No Parameters</td></tr>";
                              } ?>
                            </table>
                         </div>
                      </div>
              	   </div>
                   <div class="row col-md-12" id="submitBtn" style="margin-left: 40%;margin-top: 2rem;">
                      <input type="submit" class="btn btn-success" name="submit" value="Update Follow-up">
                    </div>
                </form>
                </div>
            </div>
          </div>
      </div>
  </div>
</section>

<!-- dropdown with search -->
        
<script src="<?php echo base_url('assets/js/select2.min.js');?>" type='text/javascript'></script>
<script type="text/javascript">
    $(document).ready(function(){
      $("#params").select2({
        multiple:true
      });
    });
    $(document).on("click",".delete-parameter",function(){
      var id = $(this).attr("id");
      if (confirm("Are you sure you want to delete parameter? ")) {
        alert(123);
    }
    })
</script>