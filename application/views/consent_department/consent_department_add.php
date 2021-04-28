  <link href="<?php echo base_url('assets/css/select2.min.css');?>" rel="stylesheet">
  <style>
 /*.dropdown-menu{
  height:500px;
  overflow-y: auto;
 }*/
  .select2-container {
    border: 1px solid #dde6e9;
    outline: 0;
}
</style>

<div class="row page-header">
   <div class="col-lg-6 align-self-center">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">CONSENTFORM-DEPARTMENT</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                          <form method="POST" action="<?php echo base_url('Consentform_department/consent_department_add');?>" role="form">
                             


                            <div class="row col-md-12">
                                <div class="col-md-6"><div class="form-group">
                                    <label for="formulation" class="col-form-label">DEPARTMENT</label>    
                                    <select id="department" name="department" placeholder="" class="form-control" required="" onchange="getConsent(this.value)">
                                      <option>--select--</option>
                                       <?php foreach ($department_list as $value) {?>
                                        <option value="<?php echo $value->department_id;?>">
                                          <?php echo $value->department_name;?>
                                         </option>
                                      <?php } ?>
                                    </select>
                                </div></div>
        
                                <div class="col-md-6"><div class="form-group">
                                    <label for="salt" class="col-form-label">CONSENT FORM</label><br>
                                     <select id="consent" name="consent[]" multiple="" type="text" placeholder="" class="form-control consent">
                                        
                                    </select>
                                    <p class="loading_p">Loading Consent Forms <i class="fa fa-spinner fa-spin"></i></p>
                                </div>
                              </div>
                                   
                            </div>

                            <div class="row col-md-6" id="mapped_consent">

                            </div> 
                           
                
                                <div class="col-sm-6">
                                        <div class="pull-right">
                                            <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                        </div>
                                    </div>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>  

 <!-- <script src="<?php echo base_url('assets/js/select2.min.js');?>"></script> -->
<script type="text/javascript">
    $(document).ready(function(){
      $(".consent").select2();
      $('.loading_p').hide();
    });
</script>


<script>


  function getConsent(dept_id) { 
      var department_id = dept_id;
      //$('#consent').html('');
      //$('#consent').multiselect('rebuild');
      $('.loading_p').show();
      $.ajax({
        url:'<?php echo base_url();?>'+'Consentform_department/getConsentforms',
        type:'POST',
        data:{department_id:department_id},
        success:function(data){
          // console.log(data);
          $('#consent').html(data);
          $('.loading_p').hide();          
          //$('#consent').multiselect('rebuild');
          mapped_consentforms(department_id);
        }
      });

    }

    function mapped_consentforms(department_id){
    $.ajax({
        type:'POST',
        url:'<?php echo base_url();?>'+'Consentform_department/mapped_consentforms',
        data:{department_id:department_id},
        success:function(data){
         // console.log(data);
          $('#mapped_consent').html(data);
          //mapped_followups(dept);

        }
      })
}
$(document).on("click",".delete-consentform",function(){
    
       
      var id = $(this).attr("id");
      

      if (confirm("Are you sure you want to delete mapped consentform? ")) {

       $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>Consentform_department/delete_mapped_consentform',
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

