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
<link href="<?php echo base_url('assets/css/select2.min.css');?>" rel="stylesheet">
<div class="row page-header">
   <div class="col-lg-6 align-self-center">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">PROCEDURE-DEPARTMENT</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                          <form method="POST" action="<?php echo base_url('Procedure_department/procedure_department_add');?>" role="form">
                             


                            <div class="row col-md-12">
                                <div class="col-md-6"><div class="form-group">
                                    <label for="formulation" class="col-form-label">DEPARTMENT</label>    
                                    <select id="department" name="department" placeholder="" class="form-control" required="" onchange="getProcedure(this.value)">
                                      <option>--select--</option>
                                       <?php foreach ($department_list as $value) {?>
                                        <option value="<?php echo $value->department_id;?>">
                                          <?php echo $value->department_name;?>
                                         </option>
                                      <?php } ?>
                                    </select>
                                </div></div>
        
                                <div class="col-md-6"><div class="form-group">
                                    <label for="salt" class="col-form-label">Medical Procedure</label><br>
                                     <select id="procedure" name="procedure[]" type="text" placeholder="" class="form-control procedure">
                                        
                                    </select>
                                </div></div>
                                
                                
                            </div> 
                             <div class="row col-md-6" id="mapped_procedure">

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

 <script src="<?php echo base_url('assets/js/select2.min.js');?>"></script>
<script type="text/javascript">
    $(document).ready(function(){
      $(".procedure").select2({
        multiple:true
      });
    });
</script>

<script>
  function getProcedure(dept_id) { 
      var department_id = dept_id;
      //$('#consent').html('');
      //$('#consent').multiselect('rebuild');
      $.ajax({
        url:'<?php echo base_url();?>'+'Procedure_department/getprocedure',
        type:'POST',
        data:{department_id:department_id},
        success:function(data){
          //console.log(data);
          $('#procedure').html(data);
          //$('#consent').multiselect('rebuild');
          mapped_procedures(department_id);
        }
      })

    }
    function mapped_procedures(department_id){
    $.ajax({
        type:'POST',
        url:'<?php echo base_url();?>'+'Procedure_department/mapped_procedures',
        data:{department_id:department_id},
        success:function(data){
         // console.log(data);
          $('#mapped_procedure').html(data);
          //mapped_followups(dept);

        }
      })
}
$(document).on("click",".delete-procedure",function(){
    
       
      var id = $(this).attr("id");
      

      if (confirm("Are you sure you want to delete mapped procedure? ")) {

       $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>Procedure_department/delete_mapped_procedure',
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

