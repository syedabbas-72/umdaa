<div class="page-bar">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb page-breadcrumb pull-left">
          <li><a href="#">Home</a><i class="fa fa-angle-right"></i></li>
          <li><a href="<?php echo base_url('User_Entities');?>">User Entities</a><i class="fa fa-angle-right"></i></li>
          <li><a href="#">ADD</a></li>          
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
                <h3 class="page-header"><b><i class="fa fa-plus-circle"></i> Add User Entity</b></h3>
                  <div role="tabpanel" >
                  <form method="post" action="<?php echo base_url('User_Entities/user_entities_add');?>" autocomplete="off">
                          <div class="row">
                            <div class="col-md-4">
                              <label class="col-form-label">Entity Name *</label>
                              <input type="text" name="user_entity_name" class="form-control" required="">
                            </div>
                            <div class="col-md-4">
                              <label class="col-form-label">Entity Alias Name</label>
                              <input type="text" name="user_entity_alias" class="form-control" >
                            </div>
                            <div class="col-md-4">
                              <label class="col-form-label">Category</label>
                              <select class="form-control" name="category">
                                <option selected="" disabled="">Select Category</option>
                                <option value="Administration" <?=($category=="Administration")?'selected':''?> >Administration</option>
                                <option value="Main" <?=($category=="Main")?'selected':''?> >Main</option>
                                <option value="Masters" <?=($category=="Masters")?'selected':''?> >Masters</option>
                                <option value="Ionic" <?=($category=="Ionic")?'selected':''?> >Ionic</option>
                                <option value="Angular" <?=($category=="Angular")?'selected':''?> >Angular</option>
                              </select>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-4">
                              <label class="col-form-label">Method </label>
                              <input type="text" name="method_name" class="form-control">
                            </div>
                            <div class="col-md-4">
                              <label class="col-form-label">Position</label>
                              <input type="text" name="position" min="0" minlength="1" class="form-control" >
                            </div>
                            <div class="col-md-4">
                              <label class="col-form-label">Parent Entities</label>
                              <select class="form-control" name="parent_id">
                                <option selected="" value="0">Select Category</option>
                                <?php
                                foreach ($parentEntities as $value) {
                                  ?>
                                    <option value="<?=$value->user_entity_id?>"><?=$value->user_entity_name?></option>
                                  <?php
                                }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-4">
                              <label class="col-form-label">Level</label>
                              <select class="form-control" name="level">
                                <option selected="" disabled="">Select Level</option>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                              </select>
                            </div>
                            <div class="col-md-4">
                              <label class="col-form-label">Level Alias</label>
                              <select class="form-control" name="level_alias">
                                <option selected="" disabled="">Select Level Alias</option>
                                <option value="nav">Nav</option>
                                <option value="page">Page</option>
                              </select>
                            </div>
                            <div class="col-md-4">
                              <label class="col-form-label">Is Mobile Module ?</label>
                              <div class="row" id="check_ptype">
                                <div class="radio radio-success">
                                  <input type="radio" class="radio-ma" id="radio14" name='is_mobile_module' value="1" required="required">
                                  <label for="radio14" > Yes </label>
                                </div>
                                <div class="radio radio-success">
                                  <input type="radio" class="radio-ma"  id="radio15" name='is_mobile_module' value="0">
                                  <label for="radio15" > No </label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-4">
                              <label class="col-form-label">Entity URL </label>
                              <input type="text" name="entity_url" class="form-control">
                            </div>
                            <div class="col-md-4">
                              <label class="col-form-label">Entity Icon</label>
                              <input type="text" name="entity_icon" class="form-control" placeholder="Ex: <i class='fa fa-dashboard'></i>">
                            </div>
                          </div>
                           <div class="row" style="margin-top: 20px;">
                             <div class="col-md-12">
                                  <div class="text-center">
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
       