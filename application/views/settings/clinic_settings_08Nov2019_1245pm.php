<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url('dashboard'); ?>">HOME</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">SETTINGS</li>
      </ol>
  </div>
</div>

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
          <form method="POST" action="<?php echo base_url('clinic/clinic_update/'.$clinic_list->clinic_id);?>" enctype="multipart/form-data" role="form">
            <div class="row col-md-12">                          
              <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
                <div class="card-header" style="font-size: 15px;padding: 10px 20px">CLINIC INFORMATION</div>
              </div>
            </div>
            <div class="row col-md-12">
              <div class="col-md-6"><div class="form-group">
                  <label for="clinic_name">CLINIC NAME<span style="color:red;">*</span></label>
                  <input type="text" class="form-control" id="clinic_name" placeholder="" name="clinic_name" value="<?php echo $clinic_list->clinic_name;?>" required="">
              </div></div>
              <div class="col-md-6"><div class="form-group">
                  <label for="clinic_phone">CLINIC PHONE<span style="color:red;">*</span></label> 
                  <input id="clinic_phone" name="clinic_phone" value="<?php echo $clinic_list->clinic_phone;?>" type="text" placeholder="" class="form-control" required="">
              </div></div>
                  
            <div class="row col-md-12">
               <div class="col-md-3"><div class="form-group">
                   <label for="clinic_email" class="">CLINIC E-MAIL<span style="color:red;">*</span></label>
                      <input id="clinic_email" name="clinic_email" value="<?php echo $clinic_list->email;?>" type="text" placeholder="" class="form-control" required="">
                  </div>
              </div>
              <div class="col-md-3"><div class="form-group">
                    <label for="state" class="">STATE<span style="color:red;">*</span></label>
                      <select name="state" id="state" class="form-control" required="">
                        <option>--select state--</option>
                        <?php foreach ($state_list as $val) { ?>
                        <option value="<?php echo $val->state_id;?>"<?php if($val->state_id==$clinic_list->state_id){echo "selected";}?>><?php echo $val->state_name;?></option>
                        <?php } ?>
                      </select>
                  </div>
                </div>
              
              <div class="col-md-3"><div class="form-group">
                    <label for="district" class="">DISTRICT</label>
                      <select id="district" name="district" type="text" placeholder="" class="form-control">
                                        <option value='0'>-- Select --</option>
                                    </select>
                    </div>
                </div>

                
                 <div class="col-md-3"><div class="form-group">
                     <label for="pincode" class="">PIN CODE</label>
                      <input id="pincode" name="pincode" value="<?php echo $clinic_list->pincode;?>" type="text" placeholder="" class="form-control">
                    </div>
                </div>

            </div> 
                 
            <div class="row col-md-12">
              <div class="col-md-4"><div class="form-group">
                  <label>CLINIC LOGO</label>
                  <div class="fileinput-new" data-provides="fileinput">
                    <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('uploads/clinic_logos/'.$clinic_list->clinic_logo);?>" /></div>
                   
                     <input type="file" name="clinic_logo" value="<?= $clinic_list->clinic_logo; ?>" class="form-control">
                  </div>
                  
              </div></div>
              <div class="col-md-3"><div class="form-group">
                  <label>CLINIC EMBLEM</label>
                  <div class="fileinput-new" data-provides="fileinput">
                    <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('uploads/clinic_logos/'.$clinic_list->clinic_emblem);?>" /></div>
                   
                     <input type="file" name="clinic_emblem" value="<?= $clinic_list->clinic_emblem; ?>" class="form-control">
                  </div>
                  
              </div></div>
      

              <div class="col-md-5"><div class="form-group">
                    <label for="address" class="">ADDRESS</label>                
                    <textarea style="height:150px" id="address" name="address" placeholder="" class="form-control"><?php echo $clinic_list->address;?></textarea>
                  </div>
              </div>
      
            </div>

            <div class="row col-md-12">

            <div class="col-md-4"><div class="form-group">
              <label for="registration_fee" class="">REGISTRATION FEE<span style="color: red;">*</span></label>
                  <input id="registration_fee" name="registration_fee" type="text" placeholder="" class="form-control" required="" value="<?php echo $clinic_list->registration_fee;?>">
              </div>
            </div>
            <div class="col-md-4"><div class="form-group">
              <label for="pharmacy_discount" class="">MAX PHARMACY DISCOUNT(%)<span style="color: red;">*</span></label>
                  <input id="pharmacy_discount" name="pharmacy_discount" type="text" placeholder="" class="form-control" required="" value="<?php echo $clinic_list->pharmacy_discount;?>">
              </div>
            </div>
                              
            <div class="col-md-4"><div class="form-group">
              <label for="lab_discount" class="">MAX LAB DISCOUNT(%)<span style="color: red;">*</span></label>
                <input id="lab_discount" name="lab_discount" type="text" placeholder="" class="form-control" required="" value="<?php echo $clinic_list->lab_discount;?>">
              </div>
            </div>
          </div> 
          <div class="row col-md-12">
            <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
              <div class="card-header" style="font-size: 15px;padding: 10px 20px">CLINIC INCHARGE INFORMATION</div>
            </div>
          </div>
          <div class="row col-md-12">
              <div class="col-md-4"><div class="form-group">
                <label for="clinic_head" class="col-form-label">CLINIC INCHARGE<span style="color:red;">*</span></label>          
                <input id="clinic_head" style="text-transform: capitalize;" name="clinic_incharge" value="<?php echo $clinic_list->incharge_name;?>" type="text" placeholder="" class="form-control" required="">
              </div>
              </div>
              <div class="col-md-4"><div class="form-group">
                   <label for="incharge_mobile" class="col-form-label">INCHARGE MOBILE<span style="color:red;">*</span></label>
                    <input id="incharge_mobile" value="<?php echo $clinic_list->incharge_mobile;?>" name="incharge_mobile" type="text" placeholder="" class="form-control" required="">
                  </div>
              </div>
               <div class="col-md-4"><div class="form-group">
                  <label for="incharge_email" class="col-form-label">E-MAIL<span style="color:red;">*</span></label>    
                   <input id="incharge_email" style="text-transform: lowercase;" value="<?php echo $clinic_list->incharge_email;?>" name="incharge_email" type="text" placeholder="" class="form-control" required="">
                  </div>
              </div>
          </div> 
          <div class="row col-md-12">                          
            <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
              <div class="card-header" style="font-size: 15px;padding: 10px 20px">FACILITIES PROVIDED</div>
            </div>
          </div>
                <?php $facilities = explode(",",$clinic_list->facilities);?>
        <div class="row col-md-12">
          <div class="form-group col-md-12">
       
           <div class="form-inline">
                  <?php if(in_array('pharmacy',$facilities)) { ?>
                                 <div class="checkbox checkbox-inline checkbox-success">
                <input id="checkbox15" type="checkbox" name="facilities[]" checked="" value="pharmacy">
                <label for="checkbox15"> Pharmacy </label>
              </div>
               <?php }else { ?>
                 <div class="checkbox checkbox-inline checkbox-success">
                <input id="checkbox15" type="checkbox" name="facilities[]"  value="pharmacy">
                <label for="checkbox15"> Pharmacy </label>
              </div>
               <?php } if(in_array('lab',$facilities)) { ?>

                                  <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                <input id="checkbox16" name="facilities[]" type="checkbox" checked="" value="lab">
                <label for="checkbox16"> Lab </label>
              </div>

            <?php } else {?>
                 <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                <input id="checkbox16" name="facilities[]" type="checkbox"  value="lab">
                <label for="checkbox16"> Lab </label>
              </div>
            <?php } if(in_array('radiology',$facilities)) { ?>
                                  <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                <input id="checkbox17" name="facilities[]" type="checkbox" checked="" value="radiology">
                <label for="checkbox17"> Radialogy </label>
              </div>
            <?php } else {?>
              <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                <input id="checkbox17" name="facilities[]" type="checkbox"  value="radiology">
                <label for="checkbox17"> Radialogy </label>
              </div>
            <?php } if(in_array('parking',$facilities)) { ?>
               <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                <input id="checkbox18" name="facilities[]" type="checkbox" checked="" value="parking">
                <label for="checkbox18"> Parking </label>
              </div>
            <?php }else { ?>
                 <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                <input id="checkbox18" name="facilities[]" type="checkbox"  value="parking">
                <label for="checkbox18"> Parking </label>
              </div>
            <?php } if(in_array('valetparking',$facilities)) { ?>
               <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                <input id="checkbox19" name="facilities[]" type="checkbox" checked="" value="valetparking">
                <label for="checkbox19"> Valet Parking </label>
              </div>
            <?php } else { ?>
                 <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                <input id="checkbox19" name="facilities[]" type="checkbox" value="valetparking">
                <label for="checkbox19"> Valet Parking </label>
              </div>

            <?php } if(in_array('lift',$facilities)) { ?>
               <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                <input id="checkbox20" name="facilities[]" type="checkbox" checked = "" value="lift">
                <label for="checkbox20"> Lift if not on ground floor </label>
              </div>
            <?php } else {?>
              <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                <input id="checkbox20" name="facilities[]" type="checkbox"  value="lift">
                <label for="checkbox20"> Lift if not on ground floor </label>
              </div>
            <?php } ?>
            </div>
          </div>
        </div>
        <div class="row col-md-12">
            <?php   if(in_array('pharmacy',$facilities)) { ?>
               <div class="col-sm-4" id="gstn_div">
               Enter GSTIN (If Applicable)
               <input id="gstnhid" name="gstn" type="hidden"  value=""> 
               <input id="gstn" name="gstn" type="text"  value="<?php echo $clinic_list->gstin; ?>">
               </div>
              
            <?php } else {  ?>

              <div class="col-sm-4" id="gstn_div" style="display: none;">
               Enter GSTIN (If Applicable)
               <input id="gstnhid" name="gstn" type="hidden"  value=""> 
               <input id="gstn" name="gstn" type="text" value="<?php echo $clinic_list->gstin; ?>">
               </div>

            <?php } ?>
        </div>
        <div class="row col-md-12">
            <div class="col-md-6"><div class="pull-right">
                <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
            </div></div>
            <div class="col-md-6">
                <input type="button" class="btn btn-default" value="Cancel" onclick="window.history.go(-1)">
            </div>
        </div>           
                  
      </form>
    </div>
  </div>
</div> 
</div>
</div>
</div>
</div>
</div>                                 
   

                     


<script type="text/javascript">
      var selected_state = '<?php echo $clinic_list->state_id; ?>';
  

    getDistricts(selected_state);
  $(document).on("click","#checkbox15",function(){
    if($(this).is(':checked')){
      $("#gstn_div").show();
    }
    else{
      $("#gstn_div").hide();
    }
  });
     function getDistricts(id) {
        var url = "<?php echo base_url('Patients/getDistricts'); ?>";
          var selected_district = '<?php echo $clinic_list->district_id; ?>';
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                id: id},
            beforeSend: function (data) {
                //$('#fpo_status_'+id).html('<img src="<?php echo base_url('assets/images/load.gif'); ?>" />');
            },
            success: function (data) {
                $('#district').html(data);
                 if(selected_district!=0|| selected_district!= "NULL"|| selected_district!=""){
       
        $("#district").val(selected_district);
    }
            }
        });
    }
</script>