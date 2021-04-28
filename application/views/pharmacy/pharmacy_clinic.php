<div class="row page-header">
   <div class="col-lg-6 align-self-center">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#"> PHARMACY - CLINIC </a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                  <form method="POST" action="<?php echo base_url('pharmacy/pharmacy_clinic');?>" role="form">
                            <div class="row col-md-12">
                          <div class="col-md-4">
                          <div class="form-group">
                              <label for="title" class="col-form-label"> CLINIC</label>
                                 <select id="clinic_id" name="clinic_id" type="text" placeholder="" class="form-control" >
                                        <option>--select--</option>
                                        <?php foreach ($clinics as $value) {?>
                                        <option value="<?php echo $value->clinic_id;?>">
                                          <?php echo $value->clinic_name;?>
                                         </option>
                                      <?php } ?>
                                    </select>
                                </div>

                              </div>
                                <div class="col-md-4">
                          <div class="form-group">
                              <label for="title" class="col-form-label"> PHARMACY</label>
                                 <select id="pharmacy_id" name="pharmacy_id" type="text" placeholder="" class="form-control" >
                                     <option value="<?php echo $pharmacy_master->pharmacy_id;?>">
                                          <?php echo $pharmacy_master->pharmacy_name;?>
                                         </option>
                                      
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
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>  