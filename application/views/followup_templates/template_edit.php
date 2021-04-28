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
                                  <input type="text" readonly="" name="name" id="FollowUpTitle"  class="form-control" value ="<?php echo $template_val->followup_name; ?>" required="required">
                                  <input type="hidden" name="fp_id" id="FollowUpTitle"  class="form-control" value ="<?php echo $template_val->followup_id; ?>" required="required">
                              </div>
                          </div>

                        <!--   <div class="col-md-6">
                            <span id="show_error" class="error" style="display: none;"> <a id="new_param" class="btn btn-xs btn-primary">New</a></span>
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
                          </div> -->
                         
                      </div> 
                      <div class="row col-md-12">
                         <div class="col-md-6">
                           <table id="prescription" class="table table-bordered items">
                              <tr id="cparams">
                                <th style="padding: 15px;">CLINICAL PARAMETERS</th><th style="padding: 15px;"><a href="javascript:;" id="add_clinical_param" class="btn btn-success btn-xs"><i class="fa fa-plus"aria-hidden="true"></i></a></th>
                                
                              </tr>

                              <?php $i=1; foreach ($c_params as $key => $value) { ?>
                                <tr class="clinical" id="<?php echo $value->parameter_id; ?>">
                                 

                                  <td style="padding: 15px;"><?php echo $value->parameter_name; ?></td>
                                   <td style="padding: 15px;"><a href="javascript:;" id="<?php echo $value->parameter_id; ?>" class="btn btn-danger btn-xs delete-parameter"><i class="fa fa-times"aria-hidden="true"></i></a><a style="margin-left:10px" href="javascript:;" id="<?php echo $value->parameter_id; ?>" class="btn btn-info btn-xs edit-parameter"><i class="fa fa-edit"aria-hidden="true"></i></a><a style="margin-left:10px;display: none" href="javascript:;" id="<?php echo $value->parameter_id; ?>" class="btn btn-success btn-xs update-parameter"><i class="fa fa-check"aria-hidden="true"></i></a></td>


                                </tr>
                              <?php } ?>
                            
                              <tr id="lparams">
                                <th style="padding: 15px;">LAB PARAMETERS</th><th style="padding: 15px;"><a href="javascript:;" id="add_lab_param" class="btn btn-success btn-xs"><i class="fa fa-plus"aria-hidden="true"></i></a></th>
                                
                              </tr>

                              <?php 
                              if(count($l_params)> 0){
                              $i=1; foreach ($l_params as $key => $value) { ?>
                                <tr  class="lab" id="<?php echo $value->parameter_id; ?>">
                                 

                                  <td style="padding: 15px;"><?php echo $value->parameter_name; ?></td>
                                   <td style="padding: 15px;"><a href="javascript:;" id="<?php echo $value->parameter_id; ?>" class="btn btn-danger btn-xs  delete-parameter"><i class="fa fa-times"aria-hidden="true"></i></a><a style="margin-left:10px" href="javascript:;" id="<?php echo $value->parameter_id; ?>" class="btn btn-info btn-xs edit-parameter"><i class="fa fa-edit"aria-hidden="true"></i></a><a style="margin-left:10px;display: none" href="javascript:;" id="<?php echo $value->parameter_id; ?>" class="btn btn-success btn-xs update-parameter"><i class="fa fa-check"aria-hidden="true"></i></a></td>


                                </tr>
                              <?php }} else{
                                echo "<tr><td>No Parameters</td></tr>";
                              } ?>
                            </table>
                         </div>

                         <div class="col-md-6" >
                          <div class="row" id="new_param_div" style="display: none;">
                           <div class="col-md-5">
                            <div class="form-group">
                              <label for="NewParameter" class="col-form-label">Parameter</label>
                                  <input type="text" name="parameter_name"   class="form-control" value ="" required="required">
                                  
                              </div>
                          </div>
                          <div class="col-md-5">
                            <div class="form-group">
                              <label for="NewParameter" class="col-form-label">Type</label>
                                  <select name="ptype" class="form-control">
                                  <option value="">--select--</option> 
                                  <option value="Clinical">Clinical</option>
                                  <option value="Lab">Lab</option>
                                </select>
                                  
                              </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                             <button style="margin-top:30px" class="btn btn-success btn-sm" id="create_parameter">Create </button>
                                  
                              </div>
                          </div>
                        </div>
                        <div class="row col-md-12" id="success_msg">
                         
                        </div>
                         </div>
                      </div>
              	   </div>
                  <!--  <div class="row col-md-12" id="submitBtn" style="margin-left: 40%;margin-top: 2rem;">
                      <input type="submit" class="btn btn-success" name="submit" value="Update Follow-up">
                    </div> -->
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
        multiple:true,
        minimumInputLength: 2,
      placeholder: "Select",
    allowClear: true,
         tags: true,
            createTag: function (params) {
                var term = $.trim(params.term);
                var count = 0
                var existsVar = false;
                //check if there is any option already
                if($('#params option').length > 0){
                    $('#params option').each(function(){
                        if ($(this).text().toUpperCase() == term.toUpperCase()) {
                            existsVar = true
                            $("#show_error").hide();
                            return false;
                        }else{
                            $("#show_error").show();

                            existsVar = false
                        }
                    });
                    if(existsVar){
                        return null;
                    }
                   
                }
                //since select has 0 options, add new without comparing
                else{
               
                    return {
                        id: params.term,
                        text: params.term,
                        newTag: true
                    }
                }
            }
      });
      $("#params").val(null).trigger("change");
    });
	
    $(document).on("click",".delete-parameter",function(){
    
       
      var id = $(this).attr("id");
      

      if (confirm("Are you sure you want to delete parameter? ")) {

       $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>followup_templates/delete_parameter',
    data:{ pid:id},
    success: function(result)
      {
        $("tr[id="+id+"]").remove();
        //location.href= '<?php echo base_url('calendar_view'); ?>';
      }       
             

     });
    }
    });
	
    function update_clinical_parameter(fid,val){
console.log(fid);
        $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>followup_templates/update_clinical_parameter',
    data:{ fid:fid,param:val},
    success: function(result)
      {
          $("#acr").remove();
        result = $.trim(result);
        var count = $("tr.clinical").length;

      if(count == 0){
        $("tr#cparams").after(result);
      }
      else{
        $("tr.clinical:last").after(result);
      }
      }       
             

     });
    }
	
     function update_lab_parameter(fid,val){

        $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>followup_templates/update_lab_parameter',
    data:{ fid:fid,param:val},
    success: function(result)
      {
          $("#acr").remove();
 
        result = $.trim(result);
         var count = $("tr.lab").length;

      if(count == 0){
        $("tr#lparams").after(result);
      }
      else{
        $("tr.lab:last").after(result);
      }
      }       
             

     });
    }
	
    $(document).on("click",".edit-parameter",function(){

        
         $(this).closest("tr").find('td:first').prop('contenteditable', true);
          $(this).closest("tr").find('td:first').focus();
        $(this).hide();
        $(this).closest("tr").find("td .update-parameter").show();
    });
    $(document).on("click",".update-parameter",function(){

        var name = $(this).closest("tr").find('td:first').html();
        var id = $(this).closest("tr").attr('id');
         $(this).closest("tr").find('td:first').prop('contenteditable', false);
        $(this).hide();
        $(this).closest("tr").find("td .edit-parameter").show();
          $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>followup_templates/update_parameter',
    data:{ pname:name, pid:id},
    success: function(result)
      {

 alert("Parameter Updated")

      }       
             

     });
         
    });
	
    $(document).on("click","#new_param",function(){
      $(this).hide();
      $("#new_param_div").show();

    });
    $(document).on("click","#add_clinical_param",function(){
      var fid = '<?php echo $followup_id; ?>';
      var count = $("tr.clinical").length;

      if(count == 0){
        $("tr#cparams").after('<tr class="clinical" id="acr"><td style="padding: 15px;"><input type="text" onkeyup ="clinicalParameterSearch('+fid+')" class="searchparam" id="new_clinical" value="" /></td><td style="padding: 15px;"><a id="add_clinical"  href="javascript:;" type="Clinical" class="btn btn-info btn-xs"><i class="fa fa-check" aria-hidden="true"></i></a></td></tr>');
      }
      else{
        $("tr.clinical:first").before('<tr class="clinical" id="acr"><td style="padding: 15px;"><input type="text" onkeyup ="clinicalParameterSearch('+fid+')" class="searchparam" id="new_clinical" value="" /></td><td style="padding: 15px;"><a id="add_clinical"  href="javascript:;" type="Clinical" class="btn btn-info btn-xs"><i class="fa fa-check" aria-hidden="true"></i></a></td></tr>');
      }
      
      return false;
    });
    $(document).on("click","#add_lab_param",function(){
      var fid = '<?php echo $followup_id; ?>';
       var count = $("tr.lab").length;
      if(count == 0){
        $("tr#lparams").after('<tr class="clinical" id="acr"><td style="padding: 15px;"><input type="text" onkeyup ="labParameterSearch('+fid+')" class="searchparam1" id="new_lab" value="" /></td><td style="padding: 15px;"><a id="add_lab"  href="javascript:;" type="Lab" class="btn btn-info btn-xs"><i class="fa fa-check" aria-hidden="true"></i></a></td></tr>');
      }
      else{
        $("tr.lab:first").before('<tr class="clinical" id="acr"><td style="padding: 15px;"><input type="text" onkeyup ="labParameterSearch('+fid+')" class="searchparam1" id="new_lab" value="" /></td><td style="padding: 15px;"><a id="add_lab"  href="javascript:;" type="Lab" class="btn btn-info btn-xs"><i class="fa fa-check" aria-hidden="true"></i></a></td></tr>');
      }
      return false;
    });

     $(document).on("click","#add_clinical",function(){
      var ptype = $(this).attr("type");
      var name = $("#new_clinical").val();
      if(name == ""){
        alert("please enter parameter");
        return false;
      }
      var fid = '<?php echo $followup_id; ?>';
      $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>followup_templates/create_clinical_parameter',
    data:{ pname:name, ptype:ptype, fid:fid},
    success: function(result)
      {
        $("#acr").remove();
       result = $.trim(result);
       if(result != "error"){
   
     var count = $("tr.clinical").length;

      if(count == 0){
        $("tr#cparams").after(result);
      }
      else{
        $("tr.clinical:last").after(result);
      }
   
       }
       else{
         // $("#success_msg").html(" Error Occured. ");
       }

      }       
             

     });
      return false;
    });
       $(document).on("click","#add_lab",function(){

      var ptype = $(this).attr("type");
      var name = $("#new_lab").val();

      if(name == ""){
        alert("please enter parameter");
        return false;
      }
      var fid = '<?php echo $followup_id; ?>';
      $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>followup_templates/create_lab_parameter',
    data:{ pname:name, ptype:ptype, fid:fid},
    success: function(result)
      {
        $("#acr").remove();
       result = $.trim(result);
       console.log(result);
       if(result != "error"){
   var count = $("tr.lab").length;

      if(count == 0){
        $("tr#lparams").after(result);
      }
      else{
        $("tr.lab:last").after(result);
      }
    
   
       }
       else{
         // $("#success_msg").html(" Error Occured. ");
       }

      }       
             

     });
      return false;
    });
        function clinicalParameterSearch(pid)
{

    var autoComplete = [];
    var json_url = '<?php echo base_url(); ?>uploads/clinical_parameters.json';
    $.getJSON(json_url, function(data) {
    for (var i = 0, len = data.length; i < len; i++) {
      autoComplete.push(data[i]);
    }   
  }); 
  $(".searchparam").autocomplete({   
    select: function(event, ui){
  
      var val= ui.item.value;
     var count = $("tr.clinical").length;

        update_clinical_parameter(pid,val);
      
 
     },
     response: function(event, ui) {
          
        },
    minLength:2,
    source: autoComplete
  }); 
}

 function labParameterSearch(fid)
{

    var autoComplete = [];
    var json_url = '<?php echo base_url(); ?>uploads/lab_parameters.json';
    $.getJSON(json_url, function(data) {
    for (var i = 0, len = data.length; i < len; i++) {
      autoComplete.push(data[i]);
    }   
  }); 
  $(".searchparam1").autocomplete({   
    select: function(event, ui){
       var val= ui.item.value;
       

 update_lab_parameter(fid,val);

     },
     response: function(event, ui) {
          
        },
    minLength:2,
    source: autoComplete
  }); 
}
</script>
