<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('Package');?>">PACKAGE</a></li>
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
                  <form method="post" action="<?php echo base_url('Package/package_add');?>" autocomplete="off">
                          <div class="row col-md-12">
                              <div class="col-md-4"><div class="form-group">
                                <label for="package_name" class="col-form-label">Package Name</label>
                                  <input  name="package_name" type="text" placeholder="Package Name" class="form-control" >
                                </div>
                              </div>
                               <div class="col-md-4"><div class="form-group">
                              <label for="status" class="col-form-label">Status</label>
                                <select id="status" name="status" type="text" placeholder="" class="form-control">
                                        <option>--select--</option>
                                        
                                        <option value="1" selected>Active</option>
                                        <option value="0">InActive</option>
                                     
                                    </select>                                         
                              </div>
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
</section>
       