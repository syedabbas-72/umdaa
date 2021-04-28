<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li><a class="parent-item" href="<?php echo base_url("Consentform"); ?>">Consent Forms</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                              
                                <li class="active">Consent Form Add</li>
                            </ol>
                        </div>
                    </div>
<section class="main-content">
        <div class="row">             
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                    	
                     <div class="col-md-12">
                      <?php echo form_open("Consentform/Consentform_insert");?>
                        <div class="row col-md-12">
                               <div class="row col-md-12">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="department_id" class="col-form-label">DEPARTMENT NAME</label>
                                        <select name="department_id" id="department_id"  class="form-control"  required="required">
                                          <option value="">--Select--</option>
                                          <?php foreach($departments_list as $dep_val){?>
                                            <option value="<?php echo $dep_val->department_id;?>"><?php echo $dep_val->department_name;?></option>
                                          <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="consent_form_title" class="col-form-label">CONSENT FORM TITLE</label>
                                        <input type="text" name="consent_form_title" id="consent_form_title"  class="form-control"  required="required">
                                    </div>
                                </div>
                            </div> 
                            <div class='row col-md-12'>
                              <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="brief" class="col-form-label">BRIEF</label>
                                        <input type="text" name="brief" id="brief"  class="form-control"  required="required">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="alternative" class="col-form-label"> ALTERNATIVE</label>
                                        <input type="text" name="alternative" id="alternative"  class="form-control"  required="required">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-group">
                                <label for="benefits" class="col-form-label"> BENEFITS</label>
                                <textarea name="benefits" id="summernote" style="height: 200px"></textarea>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-group">
                                 <label for="complications" class="col-form-label">COMPLICATIONS</label>
                                <textarea name="complications" id="summernote1" style="height: 200px"></textarea>
                              </div>
                            </div>
                            <div class='row col-md-12'>
                              <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="anesthesia" class="col-form-label">ANESTHESIA</label>
                                        <input type="text" name="anesthesia" id="anesthesia"  class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="status" class="col-form-label"> STATUS</label>
                                        <select name="status" id="status"  class="form-control"  required="required">
                                          <option value="">--Select--</option>
                                          <option value="1">Active</option>
                                          <option value="0">InActive</option>
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
                  <?php echo form_close();?>

                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
<script type="text/javascript">
 $(document).ready(function () {
      $('#summernote').summernote({});
});
</script>
<script type="text/javascript">
 $(document).ready(function () {
      $('#summernote1').summernote({});
});
</script>
