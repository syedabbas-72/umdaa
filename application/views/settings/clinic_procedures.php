<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-left">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url('dashboard'); ?>"><?php echo $clinic_name?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>
          <li><a class="parent-item" href="<?php echo base_url('settings'); ?>">SETTINGS</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">PROCEDURES</li>
      </ol>
  </div>
</div>
<?php if($this->session->flashdata('msg')): ?>
    <p><?php echo $this->session->flashdata('msg'); ?></p>
<?php endif; ?>

          <div class="row">
        <div class="col-2 list-group ">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <?php $this->view("settings/settings_left_nav"); ?> 
            
            </div>
        </div>
        <div class="col-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                             
                                <div class="tab-pane active" id="staff">
                                  <form method="POST" action="<?php echo base_url('settings/save_procedure'); ?>" enctype="multipart/form-data" role="form">
                                        <div class = "row col-md-12">
                          
                              <div class="row col-md-12">       
                                <div class="col-md-6"><div class="form-group">
                                    <label for="clinic_name" class="col-form-label">Procedure Name<span style="color:red;">*</span></label>
                                    <input  onkeyup="investigationsearch()" id="search_investigation" name="procedure_name" value="" type="text" placeholder="" class="form-control" required="">
                                </div></div>
            
                                <div class="col-md-3"><div class="form-group">
                                    <label for="procedure_cost" id="procedure_cost" class="col-form-label">Procedure Cost(INR)<span style="color:red;">*</span></label>    
                                    <input id="procedure_cost"   name="procedure_cost" value="" type="text" placeholder="" class="form-control" required="">
                                </div>
                              </div>
                              <div class="col-md-3"><div class="form-group">
                                    <input style="margin-top: 33px" type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                </div>
                              </div>
                            </div>
                            
                          
                                        </div>
                                        </form>
                                        <hr>
                                        <div class="row col-md-12">

                              <table class = "table customTable">
                                <thead style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
                                  <tr>
                                <th>Procedure name</th>
                                <th>Procedure Unit Cost</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                                <tbody>
                                  <?php for($i=0;$i<count($procedures);$i++) { ?>
                                    <tr id="<?php echo $procedures[$i]->clinic_procedure_id; ?>">
                                      <td><?php echo $procedures[$i]->procedure_name;?></td>
                                      <td><?php echo $procedures[$i]->procedure_cost;?></td>
                                      <td style="padding: 15px;">
                                        <a href="javascript:;" id="<?php echo $procedures[$i]->clinic_procedure_id; ?>" class="delete-procedure"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        <a style="margin-left:10px" href="javascript:;" id="<?php echo $procedures[$i]->clinic_procedure_id; ?>" class="edit-procedure"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        <a style="margin-left:10px;display: none" href="javascript:;" id="<?php echo $procedures[$i]->clinic_procedure_id; ?>" class="update-procedure"><i class="fa fa-check" aria-hidden="true"></i></a>
                                      </td>
                                    </tr>
                                  <?php } ?>
                                  
                                </tbody>
                                
                              </table>

                               </div>

                                   

                    </div>
             </div>
                

            </div>
            

            
            <!-- /.tab-pane -->
        </div>
    </div>
</div>
<!-- /.tab-content -->
</div><!-- /.card-body -->
</div>
<!-- /.nav-tabs-custom -->
    
                     

<script>
$(function () {
        $("input[id*='procedure_cost']").keydown(function (event) {


            if (event.shiftKey == true) {
                event.preventDefault();
            }

            if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

            } else {
                event.preventDefault();
            }
            
            if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
                event.preventDefault();

        });
    });
  $(document).on("click",".edit-procedure",function(){

        
         $(this).closest("tr").find('td:first').prop('contenteditable', true);
         $(this).closest("tr").find('td:eq(1)').prop('contenteditable', true);
          $(this).closest("tr").find('td:first').focus();
        $(this).hide();
        $(this).closest("tr").find("td .update-procedure").show();
    });
  $(document).on("click",".delete-procedure",function(){
    
       
      var id = $(this).attr("id");
      

      if (confirm("Are you sure you want to delete procedure? ")) {

       $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>settings/delete_procedure',
    data:{ pid:id},
    success: function(result)
      {
        $("tr[id="+id+"]").remove();
        //location.href= '<?php echo base_url('calendar_view'); ?>';
      }       
             

     });
    }
    });
    $(document).on("click",".update-procedure",function(){

        var name = $(this).closest("tr").find('td:first').html();
        var price = $(this).closest("tr").find('td:eq(1)').html();
        var id = $(this).closest("tr").attr('id');
         $(this).closest("tr").find('td:first').prop('contenteditable', false);
        $(this).hide();
        $(this).closest("tr").find("td .edit-procedure").show();
          $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>settings/update_procedure',
    data:{ pname:name,price:price, pid:id},
    success: function(result)
      {

 alert("Procedure Updated")

      }       
             

     });
         
    });
    </script>