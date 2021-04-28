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
                    <form method="POST" action="<?php echo base_url('Package/package_update/'.$packages->package_id);?>" role="form">                                   
                        
                          <div class="row col-md-12">
                              <div class="col-md-4"><div class="form-group">
                                <label for="package_name" class="col-form-label">Package Name</label>
                                  <input id="package_name" name="package_name" type="text" placeholder="" class="form-control" value="<?php echo $packages->package_name;?>">
                                </div>
                              </div>
                             <div class="col-md-4"><div class="form-group">
                              <label for="status" class="col-form-label">Status</label>
                                <select id="status" name="status" type="text" placeholder="" class="form-control">
                                        <option>--select--</option>
                                        
                                        <option value="1" <?php if($packages->status==1) echo "selected"; ?>>Active</option>
                                        <option value="0" <?php if($packages->status==0) echo "selected"; ?>>InActive</option>
                                     
                                    </select>                                         
                              </div>
                            </div> 
                              
                          </div>
                          
                           <div class="col-md-6">
                              <div class="pull-right">
                                  <input type="submit" value="Save" name="submit" class="btn btn-success"> 
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
        