<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">CLINIC-DOCTOR</a></li>
          <li class="breadcrumb-item active"><a href="#">EDIT</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                          <form method="POST" action="<?php echo base_url('clinic_doctor/clinic_doctor_update/'.$clinic_doctor_list->clinic_doctor_id);?>" role="form">
                            <div class="row col-md-12">
                                <div class="col-md-6"><div class="form-group">
                                    <label for="clinic_name" class="col-form-label">CLINIC NAME</label>
                                    <select name="clinic_name" id="clinic_name" class="form-control">
                                        <option>--select--</option>
                              <?php foreach($clinic_list as $val){?>
                              <option value="<?=$val->clinic_id; ?>" <?php if($val->clinic_id == $clinic_doctor_list->clinic_id){echo "selected";}?>><?=$val->clinic_name?></option>
                              <?php } ?>
                                        </select>
                                </div></div>
                                <div class="col-md-6"><div class="form-group">
                                    <label for="doctor_name" class="col-form-label">DOCTOR NAME</label>
                                        <select name="doctor_name" id="doctor_name" class="form-control">
                                          <option>--select--</option>
                                          <?php foreach($doctor_list as $val){?>
                              <option value="<?=$val->doctor_id; ?>" <?php if($val->doctor_id == $clinic_doctor_list->doctor_id){echo "selected";}?>><?=$val->first_name?></option>
                              <?php } ?>
                                        </select>
                                    </div>
                                </div>
                              </div>
                            </div> 

                                <div class="col-sm-6">
                                  <div class="pull-right">
                                      <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                  </div>
                                </div>

                                    <!-- <div class="col-sm-6">
                                        <div class="pull-left">
                                            <button type="submit" class="btn  btn-gray btn-rounded btn-border btn-sm">
                                                <i class=""></i> Cancel
                                            </button>
                                        </div>
                                    </div> -->
                                  
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>  


