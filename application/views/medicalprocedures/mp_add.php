<div class="row page-header">

   <div class="col-lg-6 align-self-center ">       

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="#">Home</a></li>

          <li class="breadcrumb-item"><a href="<?php echo base_url('Patients/');?>">Patients</a></li>

          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          

        </ol>

  </div>

</div>

              <!-- content start -->

<section class="main-content">

<div class="row">

  <div class="col-md-12">

    <!-- card start -->

    <div class="card">

      <!-- <div class="card-header card-default">Inline form</div> -->

       <div class="card-body">

          <div class="tabs">

            

              <div class="tab-content">

                  <div role="tabpanel" >

                  <form method="post" action="<?php echo base_url('Patients/patient_add');?>"  autocomplete="off" enctype="multipart/form-data">                                  

                        

                          <div class="row col-md-12">

                              

                               <div class="col-md-4"><div class="form-group">

                                <label for="title" class="col-form-label"> TITLE</label>

                                 
                                    <select id="title" name="title"  class="form-control">
                                      <option value="Mr"> Mr </option>
                                      <option value="Mrs"> Mrs </option>
                                    </select>
                                </div>

                              </div>

                              <div class="col-md-4"><div class="form-group">

                                <label for="first_name" class="col-form-label">FIRST NAME</label>

                                    <input id="first_name" name="first_name" type="text" class="form-control" required="">

                                </div>

                              </div>



                               <div class="col-md-4">

                                <div class="form-group">

                                <label for="last_name" class="col-form-label">LAST NAME</label>

                                    <input id="last_name" name="last_name" type="text" class="form-control">

                                </div>

                              </div>

                              </div>

                          </div>

                         

                             

                          <div class="row col-md-12">

                               <div class="col-md-4">

                                <div class="form-group">

                                <label for="  alias_name" class="col-form-label">ALIAS NAME</label>

                                    <input id=" alias_name" name="  alias_name" type="text" placeholder="" class="form-control">

                                </div>

                              </div>

                               <div class="col-md-4"><div class="form-group">

                                <label for="relation_type" class="col-form-label"> C / O TYPE</label>
                                 <select name="relation_type" class="form-control"> 
                                    <option value=""> --Select-- </option>
                                    <option value="B/o">B/o</option>
                                    <option value="W/o">W/o</option>
                                    <option value="S/o">S/o</option>
                                    <option value="D/o">D/o</option>
                                 </select>

                                </div>

                              </div>

                             <div class="col-md-4">

                                <div class="form-group">

                                <label for="person_name" class="col-form-label">PERSON NAME</label>

                                    <input id="person_name" name="person_name" type="text" placeholder="" class="form-control">

                                </div>

                              </div> 

                                                         

                          </div> 

                           <div class="row col-md-12">

                            <div class="col-md-4"><div class="form-group">

                              <label for="gender" class="col-form-label">GENDER</label>

                                <select id="gender" name="gender" type="text" placeholder="" class="form-control" onchange="genderStatus(this.value)">
                                    <option>--select--</option>
                                     <option value="Male">Male</option>
                                      <option value="Female">Female</option>
                                      <option value="Other">Other</option>
                                </select>     
                                <div id="fe_status" style="display: none;">
                                  <label for="gender" class="col-form-label">STATUS</label>
                                  <select  name="condition_type" type="text" placeholder="" class="form-control" required="">
                                   <option value="Normal">Normal</option>
                                    <option value="Pregnancy">Pregnancy</option>
                                    <option value="Lactate">Lactate</option>
                                    
                                  </select> 
                              </div>
                              </div>

                            </div>  

                             <div class="col-md-4">

                                <div class="form-group">

                                <label for="date_of_birth" class="col-form-label">DOB</label>

                                    <input id="date_of_birth" name="date_of_birth" type="text" class="form-control">

                                </div>

                              </div> 

                               <div class="col-md-4">

                                <div class="form-group">

                                <label for="age" class="col-form-label">AGE</label>

                                  <input id="age" name="age" type="text" placeholder="" class="form-control">

                                </div>

                              </div> 

                          </div>

                           <div class="row col-md-12">

                             

                               <div class="col-md-4">

                                <div class="form-group">

                                <label for="mobile" class="col-form-label">MOBILE</label>

                                    <input id="mobile" name="mobile" type="text" placeholder="" class="form-control">

                                </div>

                              </div> 

                               <div class="col-md-4">

                                <div class="form-group">

                                <label for="phone" class="col-form-label">PHONE</label>

                                    <input id="phone" name="phone" type="text" placeholder="" class="form-control">

                                </div>

                              </div>

                               <div class="col-md-4">

                                <div class="form-group">

                                <label for="email_id" class="col-form-label">EMAIL ID</label>

                                    <input id="email_id" name="email_id" type="email" placeholder="" class="form-control" required="">

                                </div>

                              </div>

                           </div>

                           <div class="row col-md-12">

                            <!--   <div class="col-md-4">

                                <div class="form-group">

                                <label for="organization_id" class="col-form-label">ORGANIZATION</label>

                                    <input id="organization_id" name="organization_id" type="text" placeholder="" class="form-control">

                                </div>

                              </div>  -->

                             
                               <div class="col-md-4">

                                <div class="form-group">

                                <label for="state_id" class="col-form-label">STATE</label>

                                   
                                     <select id="state_id" name="state_id" type="text" placeholder="" class="form-control" onchange="getDistricts(this.value)">
                                        <option>--select--</option>
                                        <?php foreach ($states as $value) {?>
                                        <option value="<?php echo $value->state_id;?>"><?php echo $value->state_name;?></option>
                                      <?php } ?>
                                    </select>

                                </div>

                              </div> 
                              
                              <div class="col-md-4">

                                <div class="form-group">

                                <label for="district_id" class="col-form-label">DISTRICT</label>
                                
                                    <select id="get_district_id" name="district_id" type="text" placeholder="" class="form-control">
                                        <option>--select--</option>
                                      </select>  
                                    
                                </div>

                              </div>
                               <div class="col-md-4">

                                <div class="form-group">

                                <label for="address" class="col-form-label">ADDRESS</label>

                                    <input id="address" name="address" type="text" placeholder="" class="form-control">

                                </div>

                              </div>
                           </div>

                           <div class="row col-md-12">
                             
                              

                               <div class="col-md-4">

                                <div class="form-group">

                                <label for="pincode" class="col-form-label">PINCODE</label>

                                    <input id="pincode" name="pincode" type="text" placeholder="" class="form-control">

                                </div>

                              </div>

                               <div class="col-md-4">

                                <div class="form-group">

                                <label for="referred_by" class="col-form-label">REFERRED BY</label>

                                    <input id="referred_by" name="referred_by" type="text" placeholder="" class="form-control">

                                </div>

                              </div>
							  <div class="col-md-4">

                                <div class="form-group">

                                <label for="clinic_id" class="col-form-label">CLINIC</label>

                                   
                                     <select id="clinic_id" name="clinic_id" type="text" placeholder="" class="form-control" >
                                        <option>--select--</option>
                                        <?php foreach ($clinics as $value) {?>
                                        <option value="<?php echo $value->clinic_id;?>"><?php echo $value->clinic_name;?></option>
                                      <?php } ?>
                                    </select>

                                </div>

                              </div> 

                           </div>

                           <div class="row col-md-12">

                              <div class="col-md-4">

                                <div class="form-group">

                                <label for="photo" class="col-form-label">PHOTO</label>

                                    <input id="photo" name="photo" type="file" placeholder="" class="form-control">

                                </div>

                               </div> 



                           <div class="col-md-9">

                              <div class="pull-right">

                                  <input type="submit" value="Save" name="submit" class="btn btn-success"> 

                              </div>

                          </div>
						            </div>
                      </form>
              </div>   <!--Tab end -->

        </div>

      </div>

    </div>

  </div>

</section>

 <script type="text/javascript">

  $( function() {

    $( "#date_of_birth" ).datepicker({ dateFormat: "yy-mm-dd",maxDate: new Date(),changeYear:true, yearRange: "-100:+0" });
$("#date_of_birth").on('change', function () {
      var dateString = Date.parse($(this).val());
  var today = new Date(),
            dob = new Date(dateString),
            age = new Date(today - dob).getFullYear() - 1970;

    //alert(age);
    $('#age').val(age);
  });
  } );
function getDistricts(id){
var url = "<?php echo  base_url('Patients/getDistricts'); ?>";
$.ajax({
type : 'POST',
url : url,
data: {
  id:id},
  beforeSend:function(data){ 
 //$('#fpo_status_'+id).html('<img src="<?php echo base_url('assets/images/load.gif');?>" />');
},
success:function (data) {
 $('#get_district_id').html(data); 
 }
});
}
function genderStatus(val){
  if(val=="Female"){
    $("#fe_status").show(1000);
  }else{
    $("#fe_status").hide(1000);
  }
}
</script>      