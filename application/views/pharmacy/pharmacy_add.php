<div class="row page-header">
   <div class="col-lg-6 align-self-center">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">PHARMACY</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                          <form method="POST" action="<?php echo base_url('pharmacy/pharmacy_add');?>" role="form">
                            <div class="row col-md-12">
                                <div class="col-md-3"><div class="form-group">
                                    <label for="pharmacy_name" class="col-form-label">PHARMACY<span style="color: red;">*</span></label>
                                    <input id="pharmacy_name" name="pharmacy_name" type="text" placeholder="" class="form-control-demo" required="">
                                </div></div>
                                <div class="col-md-3"><div class="form-group">
                                    <label for="pharmacy_code" class="col-form-label">PHARMACY CODE<span style="color: red;">*</span></label>
                                        <input type="text" name="pharmacy_code" class="form-control-demo" required="">
                                    </div>
                                </div>
                                <div class="col-md-3"><div class="form-group">
                                    <label for="location" class="col-form-label">LOCATION<span style="color: red;">*</span></label>    
                                    <input id="location" name="location" type="text" placeholder="" class="form-control-demo" required="">
                                </div>
                              </div>
                            </div> 


                            <div class="row col-md-12">
                                <div class="col-md-3"><div class="form-group">
                                    <label for="address" class="col-form-label">ADDRESS<span style="color: red;">*</span></label>    
                                    <input id="address" name="address" type="text" placeholder="" class="form-control-demo" required="">
                                </div></div>
                                <div class="col-md-3"><div class="form-group">
                                    <label for="phone" class="col-form-label">PHONE<span style="color: red;">*</span></label>
                                   <input id="phone" name="phone" type="text" placeholder="" class="form-control-demo" required="">
                                  </div>
                                </div>
                                 <div class="col-md-3"><div class="form-group">
                                    <label for="contact_person" class="col-form-label">CONTACT PERSON<span style="color: red;">*</span></label>
                                      <input name="contact_person" id="contact_person" class="form-control" required="">
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