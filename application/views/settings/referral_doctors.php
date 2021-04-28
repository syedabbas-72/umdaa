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
          <li class="active">REFERRAL DOCTORS</li>
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
                                  <form method="POST" action="<?php echo base_url('settings/save_referral_doctor'); ?>" enctype="multipart/form-data" role="form">
                                        <div class = "row col-md-12">
                          
                                     
                                <div class="col-md-5"><div class="form-group">
                                    <label for="doctor_name" class="col-form-label">Doctor Name<span style="color:red;">*</span></label>
                                    <input style="text-transform: capitalize;" id="doctor_name" name="doctor_name" value="" type="text" placeholder="" class="form-control" required="" onkeyup="return alpha()" maxlength="35">
                                </div></div>
            
                                <div class="col-md-3"><div class="form-group">
                                    <label for="mobile" id="qualification" class="col-form-label">Qualification<span style="color:red;">*</span></label>    
            
                                    <input style="text-transform: capitalize;" id="qualification" name="qualification" value="" type="text" placeholder="" class="form-control" required="" onkeyup="return alpha()" maxlength="35">
                                    <!-- <select class="form-control qualification" name="qualification[]" required="" multiple="">
                                      <?php
                                      // $qualification = explode(", ", $doctor_list->qualification);
                                      ?>
                                      <option disabled="">Select</option>
                                      <option value="MBBS">MBBS</option>
                                      <option value="MD">MD</option>
                                      <option value="DM">DM</option>
                                      <option value="M ch">M ch</option>
                                      <option value="DA">DA</option>
                                      <option value="DO">DO</option>
                                      <option value="DL">DL</option>
                                      <option value="DD">DD</option>
                                      <option value="DGO">DGO</option>
                                      <option value="MS">MS</option>
                                      <option value="BSC">BSC</option>
                                      <option value="PHD">PHD</option>
                                    </select> -->
                                </div>
                              </div>

                               <div class="col-md-3"><div class="form-group">
                                    <label for="mobile" id="mobile" class="col-form-label">Mobile<span style="color:red;">*</span></label>    
                                    <input style="text-transform: capitalize;" id="mobile" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10" minlength="10"  name="mobile" value="" type="text" placeholder="" class="form-control" required="">
                                </div>
                              </div>
                              


                            
                          
                                        </div>
                                        <div class="row col-md-12">
                                   <div class="col-md-5"><div class="form-group">
                                    <label for="doctor_name" class="col-form-label">Email</label>
                                    <input style="text-transform: lowercase;" id="email" name="email" value="" type="email" placeholder="" class="form-control">
                                </div></div>
            
                                <div class="col-md-3"><div class="form-group">
                                    <label for="mobile" id="procedure_cost" class="col-form-label">Area<span style="color:red;">*</span></label>    
                                    <input style="text-transform: capitalize;" id="location"   name="location" value="" type="text" placeholder="" class="form-control" required="">
                                </div>
                              </div>
                              <div class="col-md-3"><div class="form-group">
                                    <input style="margin-top: 33px" type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                </div>
                              </div>
                                        </div>
                                        </form>
                                        <hr>
                                        <div class="row">
                              <div class="col-md-12">
                              <table id="referral_doctors_list" class = "table table-striped dt-responsive nowrap">
                                <thead style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
                                  <tr>
                                <th>Doctor name</th>
                                <th>Mobile</th>
                                <th style="width: 70px">Qualif. </th>
                                <th>Email</th>
                                <th>Location</th>
                                <th style="width:100px">Action</th>
                              </tr>
                            </thead>
                                <tbody>
                                  <?php for($i=0;$i<count($referral_doctors);$i++) { ?>
                                    <tr id="<?php echo $referral_doctors[$i]->rfd_id; ?>">
                                      <td><?php echo ucwords($referral_doctors[$i]->doctor_name);?></td>
                                      <td><?php echo $referral_doctors[$i]->mobile;?></td>
                                      <td><?php echo  ucwords($referral_doctors[$i]->qualification);?></td>
                                      <td><?php echo $referral_doctors[$i]->email;?></td>
                                      <td><?php echo ucwords($referral_doctors[$i]->location);?></td>
                                      <td style="padding: 15px;"><a href="javascript:;" id="<?php echo $referral_doctors[$i]->rfd_id; ?>" class="btn btn-danger btn-xs delete-doctor"><i class="fa fa-times" aria-hidden="true"></i></a><a style="margin-left:10px" href="javascript:;" id="<?php echo $referral_doctors[$i]->rfd_id; ?>" class="btn btn-info btn-xs edit-doctor"><i class="fa fa-edit" aria-hidden="true"></i></a><a style="margin-left:10px;display: none" href="javascript:;" id="<?php echo $referral_doctors[$i]->rfd_id; ?>" class="btn btn-success btn-xs update-doctor"><i class="fa fa-check" aria-hidden="true"></i></a></td>
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
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
  
                     


 <script>
  $(document).ready(function () {
      $('#referral_doctors_list').dataTable();
      $('.qualification').select2();
  });
  </script>
    

<script>

  $(document).on("click",".edit-doctor",function(){

        
         $(this).closest("tr").find('td:first').prop('contenteditable', true);
         $(this).closest("tr").find('td:eq(1)').prop('contenteditable', true);
         $(this).closest("tr").find('td:eq(2)').prop('contenteditable', true);
         $(this).closest("tr").find('td:eq(3)').prop('contenteditable', true);
         $(this).closest("tr").find('td:eq(4)').prop('contenteditable', true);
          $(this).closest("tr").find('td:first').focus();
        $(this).hide();
        $(this).closest("tr").find("td .update-doctor").show();
    });
  $(document).on("click",".delete-doctor",function(){
    
       
      var id = $(this).attr("id");
      

      if (confirm("Are you sure you want to delete doctor? ")) {

       $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>settings/delete_doctor',
    data:{ did:id},
    success: function(result)
      {
        $("tr[id="+id+"]").remove();
        //location.href= '<?php echo base_url('calendar_view'); ?>';
      }       
             

     });
    }
    });
    $(document).on("click",".update-doctor",function(){

        var name = $(this).closest("tr").find('td:first').html();
        var mobile = $(this).closest("tr").find('td:eq(1)').html();
        var qualification = $(this).closest("tr").find('td:eq(2)').html();
        var email = $(this).closest("tr").find('td:eq(3)').html();
        var location = $(this).closest("tr").find('td:eq(4)').html();
        var id = $(this).closest("tr").attr('id');
      
         $(this).closest("tr").find('td:first').prop('contenteditable', false);
        $(this).hide();
        $(this).closest("tr").find("td .edit-doctor").show();
          $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>settings/update_doctor',
    data:{ pname:name,mobile:mobile, did:id, email:email, qualification:qualification, location:location},
    success: function(result)
      {

 alert("Changes Updated")

      }       
             

     });
         
    });
    </script>