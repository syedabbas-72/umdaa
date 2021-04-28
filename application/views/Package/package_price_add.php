<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('Package');?>">PACKAGE</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
  </div>
</div>
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
                  <form method="post" action="<?php echo base_url('Package/package_price_add');?>" autocomplete="off">
                          <div class="row col-md-12">
                              <div class="col-md-4"><div class="form-group">
                                <label for="package_name" class="col-form-label">Package Name</label>
								<input type="hidden" value="<?php echo $packages->package_id; ?>" name="package_id" />
                                  <input  name="package_name" type="text" placeholder="Package Name" class="form-control" value="<?php echo $packages->package_name; ?>" disabled>
                                </div>
                              </div>
                               <div class="col-md-4"><div class="form-group">
                              <label for="price" class="col-form-label">Price</label>
                                <input  name="price" type="text" placeholder="Price" class="form-control" required>                                       
                              </div>
                            </div>
                             
                              </div>
                          <div class="row col-md-12">
						  <div class="col-md-4"><div class="form-group">
                              <label for="from_date" class="col-form-label">From Date</label>
                                <input  name="from_date" type="text" placeholder="From Date" class="form-control" id="from_date" required>                                       
                              </div>
                            </div>
							<div class="col-md-4"><div class="form-group">
                              <label for="to_date" class="col-form-label">To Date</label>
                                <input  name="to_date" type="text" placeholder="To Date" class="form-control" id="to_date" required>                                       
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
                 
          </div>   <!--Tab end -->
        </div>
      </div>
    </div>
  </div>
  </div>
</section>
<script>
$(function () {
        
        $("#from_date,#to_date").datepicker({dateFormat: "yy-mm-dd", changeYear: true, yearRange: "-100:+0"});
        
    });
</script>