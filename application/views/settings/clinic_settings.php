<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url('dashboard'); ?>"><?php echo $clinic_name?></a>&nbsp;<i class="fa fa-angle-right"></i>
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
                                <form method="POST" action="<?php echo base_url('clinic/clinic_update/'.$clinic_list->clinic_id);?>" enctype="multipart/form-data" role="form" class="customForm">
                                    
                                    <!-- Sub header in the form -->
                                    <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Clinic Information
                                        </div>
                                    </div>

                                    <div class="row col-md-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="clinic_name">Clinic Name<span style="color:red;">*</span></label>
                                                <input type="text" class="form-control" id="clinic_name" placeholder="" name="clinic_name" value="<?php echo $clinic_list->clinic_name;?>" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="clinic_phone">Clinic Phone<span style="color:red;">*</span></label> 
                                                <input id="clinic_phone" name="clinic_phone" onkeypress="return numeric()" maxlength="10" value="<?php echo $clinic_list->clinic_phone;?>" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="clinic_email" class="">Clinic Email<span style="color:red;">*</span></label>
                                                <input id="clinic_email" name="email" value="<?php echo $clinic_list->email;?>" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row col-md-12">  

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="location" class="">Location</label>
                                                <input id="location" name="location" value="<?php echo $clinic_list->location;?>" type="text" placeholder="Location" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="district" class="">District</label>
                                                <select id="district" name="district_id" type="text" class="form-control">
                                                    <option value='0'>-- Select District --</option> 
                                                    <?php foreach ($district_list as $val) { ?>
                                                        <option value="<?php echo $val->district_id;?>"<?php if($val->district_id==$clinic_list->district_id){echo "selected";}?>><?php echo $val->district_name;?></option>
                                                    <?php } ?>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                                                              
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="state" class="">State<span style="color:red;">*</span></label>
                                                <select name="state_id" id="state" class="form-control" required="">
                                                    <option>--Select State--</option>
                                                    <?php foreach ($state_list as $val) { ?>
                                                        <option value="<?php echo $val->state_id;?>"<?php if($val->state_id==$clinic_list->state_id){echo "selected";}?>><?php echo $val->state_name;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="pincode" class="">Pincode</label>
                                                <input id="pincode" name="pincode" onkeypress="return numeric()" maxlength="6" value="<?php echo $clinic_list->pincode;?>" type="text" placeholder="Pincode" class="form-control">
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row col-md-12">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Clinic Logo</label>
                                                <div class="fileinput-new" data-provides="fileinput" style="padding-top:0px !important; padding-left: 0px !important">
                                                    <div class="fileinput-preview text-center" data-trigger="fileinput" style="padding-left: 0px !important; padding-top:0px !important;"><img style="width:100%; border:1px solid #ccc; background: #f3f3f3; margin-bottom: 5px" src="<?php echo base_url('uploads/clinic_logos/'.$clinic_list->clinic_logo);?>" /></div>
                                                    <input type="file" name="clinic_logo" value="<?= $clinic_list->clinic_logo; ?>" class="form-control" accept="image/x-png,image/jpeg" >
                                                </div>
                                            </div>
                                        </div>
                                        <?php /*
                                        <div class="col-md-3"><div class="form-group">
                                            <label>Clinic Emblem</label>
                                            <div class="fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('uploads/clinic_logos/'.$clinic_list->clinic_emblem);?>" /></div>

                                                <input type="file" name="clinic_emblem" value="<?= $clinic_list->clinic_emblem; ?>" class="form-control">
                                            </div>

                                        </div></div>
                                        */ ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address" class="">Address</label>                
                                                <textarea cols="5" rows="5" id="address" name="address" placeholder="" class="form-control"><?php echo $clinic_list->address;?></textarea>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Sub header in the form -->
                                    <!-- <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Business Information
                                        </div>
                                    </div> -->

                                    <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Clinic Finance Information
                                        </div>
                                    </div>


                                    <div class="row col-md-12">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="gstin_tb" class="">Enter GSTIN (If Applicable)</label>    
                                                <input id="gstin_tb" style="text-transform: uppercase;" value="<?php echo $clinic_list->gstin; ?>" name="gstin" type="text" placeholder="GST No." class="form-control">
                                            </div>
                                        </div> 
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="registration_fee" class="">Registration Fee<span style="color: red;">*</span></label>
                                                <input id="registration_fee" name="registration_fee" type="text" placeholder="" class="form-control" required="" value="<?php echo $clinic_list->registration_fee;?>" onkeypress="return numeric();" maxlength="3">
                                            </div>
                                        </div>
                                        <?php /*
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="pharmacy_discount" class="">Max Pharmacy Disc.(%)<span style="color: red;">*</span></label>
                                                <input id="pharmacy_discount" name="pharmacy_discount" type="text" placeholder="" class="form-control" required="" value="<?php echo $clinic_list->pharmacy_discount;?>" onkeypress="return numeric();" maxlength="2" max="99">
                                            </div>
                                        </div>
                                        */ ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="procedure_discount" class="">Advance.(%)<span style="color: red;">*</span></label>
                                                <input id="procedure_discount" name="minimum_advance" type="text" placeholder="" class="form-control" required="" value="<?php echo $clinic_list->minimum_advance;?>" onkeypress="return numeric();" maxlength="2" max="99">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="procedure_discount" class="">Max Procedure Disc.(%)<span style="color: red;">*</span></label>
                                                <input id="procedure_discount" name="procedure_discount" type="text" placeholder="" class="form-control" required="" value="<?php echo $clinic_list->procedure_discount;?>" onkeypress="return numeric();" maxlength="2" max="99">
                                            </div>
                                        </div>
                                    </div>
                          
                                    <?php /*
                                    <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Lab Information
                                        </div>
                                    </div>
                                    <div class="row col-md-12">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lab_discount" class="">Max Lab Disc.(%)<span style="color: red;">*</span></label>
                                                <input id="lab_discount" name="lab_discount" type="text" placeholder="" class="form-control" required="" value="<?php echo $clinic_list->lab_discount;?>" onkeypress="return numeric();" maxlength="2" max="99">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lab_discount" class="">Max Ref. Doc. Lab Disc.(%)<span style="color: red;">*</span></label>
                                                <input id="lab_discount" name="referral_doctor_lab_discount" type="text" placeholder="" class="form-control" required="" value="<?php echo $clinic_list->referral_doctor_lab_discount;?>" onkeypress="return numeric();" maxlength="2" max="99">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="minimum_advance_tb" class="">Min. Advance (%)<span style="color: red;">*</span></label>
                                                <input id="minimum_advance_tb" name="minimum_advance" type="text" placeholder="" class="form-control" required="" value="<?php echo $clinic_list->minimum_advance;?>" onkeypress="return numeric();" maxlength="2" max="99">
                                            </div>
                                        </div>
                                    </div> 
                                    */ ?>

                                    <!-- Sub header in the form -->
                                    <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Clinic Incharge Information
                                        </div>
                                    </div>

                                    <div class="row col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="incharge" class="col-form-label">Clinic Incharge Name<span style="color:red;">*</span></label>
                                                <input id="incharge" style="text-transform: capitalize;" name="incharge_name" value="<?php echo $clinic_list->incharge_name;?>" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="incharge_mobile" class="col-form-label">Incharge Mobile<span style="color:red;">*</span></label>
                                                <input id="incharge_mobile" onkeypress="return numeric()" maxlength="10" value="<?php echo $clinic_list->incharge_mobile;?>" name="incharge_mobile" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="incharge_email" class="col-form-label">Email<span style="color:red;">*</span></label>    
                                                <input id="incharge_email" style="text-transform: lowercase;" value="<?php echo $clinic_list->incharge_email;?>" name="incharge_email" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>
                                    </div> 
                                    
                                    <!-- Sub header in the form -->
                                    <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Available Facilities
                                        </div>
                                    </div>

                                    <div class="row col-md-12">

                                        <div class="form-group col-md-12 form-inline">
                                            <div class="checkbox checkbox-inline checkbox-success">
                                                <input type="hidden" value="0" name="pharmacy">
                                                <input id="pharmacy_cb" type="checkbox" name="pharmacy" <?php if($clinic_list->pharmacy == 1) { echo 'checked'; } ?> value="1">
                                                <label for="pharmacy_cb"> Pharmacy</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                                                <input type="hidden" value="0" name="lab">
                                                <input id="lab_cb" type="checkbox" name="lab" <?php if($clinic_list->lab == 1) { echo 'checked'; } ?> value="1">
                                                <label for="lab_cb"> Lab</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                                                <input type="hidden" value="0" name="lift">
                                                <input id="lift_cb" type="checkbox" name="lift" <?php if($clinic_list->lift == 1) { echo 'checked'; } ?>  value="1">
                                                <label for="lift_cb"> Lift</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                                                <input type="hidden" value="0" name="parking">
                                                <input id="parking_cb" type="checkbox" name="parking" <?php if($clinic_list->parking == 1) { echo 'checked'; } ?>  value="1">
                                                <label for="parking_cb"> Parking</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                                                <input type="hidden" value="0" name="valet_parking">
                                                <input id="valet_parking_cb" type="checkbox" name="valet_parking" <?php if($clinic_list->valet_parking == 1) { echo 'checked'; } ?>  value="1">
                                                <label for="valet_parking_cb"> Valet Parking</label>
                                            </div>
                                            <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                                                <input type="hidden" value="0" name="radiology">
                                                <input id="radiology_cb" type="checkbox" name="radiology" <?php if($clinic_list->radiology == 1) { echo 'checked'; } ?>  value="1">
                                                <label for="radiology_cb"> Radiology</label>
                                            </div>
                                        </div>

                                    </div>  

                                    <div class="row col-md-12">
                                        <div class="col-md-6">
                                            <div class="pull-left">
                                                <input type="submit" value="Save Changes" name="submit" class="customBtn">
                                                <!-- <input type="button" class="customBtn" value="Cancel" onclick="window.history.go(-1)">     -->
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            
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

<?php /*                                
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
*/?>



<script>
$(document).ready(function(){
    var url = "<?php echo base_url('Patients/getDistricts'); ?>";
    $('#state').on('change', function(){
        var id = $(this).val();
        // alert(id);
        if(id != 0){
            $.ajax({
                type:'POST',
                url:url,
                data:{id: id},
                success:function(html){
console.log(html);
                     $('#district').html(html);
                 }
                });
            
        }else{
            $('#district').html('<option value="">Select State first</option>');
            // $('#city').html('<option value="">Select state first</option>'); 
        }
    });
    
});
</script>
