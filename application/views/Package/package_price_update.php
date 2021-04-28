<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('Package');?>">PACKAGE</a></li>
          <li class="breadcrumb-item active"><a href="#">Edit</a></li>          
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
                    <form method="POST" action="<?php echo base_url('Package/package_price_update/'.$packages->package_id);?>" role="form">                                   
                        
                          <div class="row col-md-12">
                              <div class="col-md-4"><div class="form-group">
                                <label for="package_name" class="col-form-label">Package Name</label>
								<input type="hidden" value="<?php echo $packages->package_id; ?>" name="package_id" />
								<input type="hidden" value="<?php echo $packages_price->package_price_id; ?>" name="package_price_id" />
                                  <input  name="package_name" type="text" placeholder="Package Name" class="form-control" value="<?php echo $packages->package_name; ?>" disabled>
                                </div>
                              </div>
                               <div class="col-md-4"><div class="form-group">
                              <label for="price" class="col-form-label">Price</label>
                                <input  name="price" type="text" placeholder="Price" class="form-control" required value="<?php echo $packages_price->price; ?>">                                       
                              </div>
                            </div>
                             
                              </div>
                          <div class="row col-md-12">
						  <div class="col-md-4"><div class="form-group">
                              <label for="from_date" class="col-form-label">From Date</label>
                                <input  name="from_date" type="text" placeholder="From Date" class="form-control" id="from_date" required value="<?php echo $packages_price->from_date; ?>">                                       
                              </div>
                            </div>
							<div class="col-md-4"><div class="form-group">
                              <label for="to_date" class="col-form-label">To Date</label>
                                <input  name="to_date" type="text" placeholder="To Date" class="form-control" id="to_date" required value="<?php echo $packages_price->to_date; ?>">                                       
                              </div>
                            </div>
						  </div>
                           <div class="row col-md-12">
                           <div class="col-md-6">
                              <div class="pull-right">
                                  <input type="submit" value="Save" name="submit" class="btn btn-success"> 
                              </div>
                          </div>
						   </div>
                      </form>
                  </div>
                  
          
              

          </div>   <!--Tab end -->
        </div>
      </div>
    </div>
  </div>
</section>
        