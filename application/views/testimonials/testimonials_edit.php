<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('Testimonials');?>">Testimonials</a></li>
          <li class="breadcrumb-item active"><a href="#">UPDATE</a></li>          
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
                    <form method="POST" action="<?php echo base_url('Testimonials/testimonials_update/'.$testimonials_list->testimonial_id);?>" role="form">                                   
                        
                          <div class="row col-md-12">
                              <div class="col-md-8"><div class="form-group">
                                <label for="title" class="col-form-label">Title</label>
                                  <input id="title" name="title" type="text" placeholder="Title" class="form-control" value="<?php echo $testimonials_list->title;?>">
                                </div>
                              </div>                              
                          </div>
                          <div class="row col-md-12">
                               <div class="col-md-4"><div class="form-group">
                                <label for="password" class="col-form-label">Testimonial Given By</label>
                                  <input  name="testimonial_given_by" type="text" placeholder="Testimonial Given By" class="form-control"  value="<?php echo $testimonials_list->testimonial_given_by;?>">
                                </div>
                              </div>
                             
                              <div class="col-md-4"><div class="form-group">
                                <label for="email_id" class="col-form-label">Status</label>
                                <select id="status" name="status" type="text" placeholder="" class="form-control">
                                        <option>--select--</option>
                                        
                                        <option value="1" <?=($testimonials_list->status=="1")?'selected':''?>>Active</option>
                                        <option value="0"<?=($testimonials_list->status=="0")?'selected':''?>>InActive</option>
                                     
                                    </select> 
                                </div>
                              </div>
                            </div>
                            <div class="row col-md-12">
                              <div class="col-md-8"><div class="form-group">
                                <label for="reports_to" class="col-form-label">Description</label>
                                  <textarea class="form-control" name="description" placeholder="Description" style="height: 100px;"><?=$testimonials_list->description?></textarea>                        
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
        