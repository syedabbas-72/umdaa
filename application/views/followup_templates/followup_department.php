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
          <li class="breadcrumb-item"><a href="#">FOLLOW-UP LIST</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
  </div>
</div>
<section class="main-content">
    <div class="row">             
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                 <div class="col-md-12">
                  <?php echo form_open("Followup_templates/followup_department");?>
                  <div class="row col-md-12">
                         <div class="row col-md-12">
                          
                           <div class="col-md-6">
                            <div class="form-group">
                              <label for="FollowUpTitle" class="col-form-label">Department</label>
                                 <select class="form-control" name="dept" required="" id="department">
                                   <option value="">
                                     --Select Department--
                                   </option>
                                   <?php foreach ($dept_list as $key => $value) { ?>
                                    <option value="<?php echo $value->department_id; ?>"><?php echo $value->department_name; ?></option>
                                 <?php  } ?>
                                 </select>
                              </div>
                          </div>

                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="FollowUpTitle" class="col-form-label">Follow-up Name</label>
                                  <select class="form-control" name="followup_id[]" required="" id="FollowUpTitle">
                                  
                                 </select>
                              </div>
                          </div>
                         
                      </div> 
              	   </div>
                   <div class="row col-md-6" id="mapped_followup">


                   </div>



                   <div class="row col-md-12" id="submitBtn" style="margin-left: 40%;margin-top: 2rem;">
                      <input type="submit" class="btn btn-success" name="submit" value="Save">
                    </div>
                </form>
                </div>
            </div>
          </div>
      </div>
  </div>
</section>


<script src="<?php echo base_url('assets/js/select2.min.js');?>" type='text/javascript'></script>
<script type="text/javascript">
    $(document).ready(function(){
      $("#FollowUpTitle").select2({
        multiple:true
      });
    });
</script>

<script>
  $(document).ready(function(){
    $('#department').on('change', function(){
      var dept = $('#department').val();
      $.ajax({
        type:'POST',
        url:'<?php echo base_url();?>'+'Followup_templates/getFollowups',
        data:{department_id:dept},
        success:function(data){
         // console.log(data);
          $('#FollowUpTitle').html(data);
          mapped_followups(dept);

        }
      })
       
    })
  })
function mapped_followups(dept){
    $.ajax({
        type:'POST',
        url:'<?php echo base_url();?>'+'Followup_templates/mapped_followups',
        data:{department_id:dept},
        success:function(data){
         // console.log(data);
          $('#mapped_followup').html(data);
          //mapped_followups(dept);

        }
      })
}
 $(document).on("click",".delete-followup",function(){
    
       
      var id = $(this).attr("id");
      

      if (confirm("Are you sure you want to delete mapped followup? ")) {

       $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>followup_templates/delete_mapped_followup',
    data:{ pid:id},
    success: function(result)
      {
        $("tr[id="+id+"]").remove();
        //location.href= '<?php echo base_url('calendar_view'); ?>';
      }       
             

     });
    }
    });
</script>