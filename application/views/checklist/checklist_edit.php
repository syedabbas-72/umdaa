<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <ol class="breadcrumb page-breadcrumb">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li><a class="parent-item" href="<?php echo base_url("Checklist"); ?>">Checklist</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">Checklist Edit</li>
                            </ol>
                        </div>
                        <div class="row pull-right">
                          <a href="<?=base_url('Checklist/checklist_add')?>" class="btn btn-app"><i class="fa fa-plus"></i>&nbsp;Add Checklist</a>
                        </div>
                    </div>
            <section class="main-content">
              <div class="row">             
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-body">
                      <form action="<?=base_url('Checklist/checklist_update/').$checklist_master->checklist_id?>" method="post">
                        <div class="row">
                          <div class="col-md-6">
                            <label class="col-form-label">Checklist Name</label>
                            <input type="text" class="form-control" name="description" required="" value="<?=$checklist_master->description?>">
                          </div>
                          <div class="col-md-6">
                            <label class="col-form-label">Checklist Type</label>
                            <select class="form-control" name="type" required="">
                              <option selected="" disabled="">Select Type</option>
                              <option value="Point" <?=($checklist_master->type=="Point")?'selected':''?> >Point</option>
                              <option value="Title" <?=($checklist_master->type=="Title")?'selected':''?> >Title</option>
                            </select>
                          </div>
                        </div>
                        <div class="row mt-2">
                          <div class="col-md-12 text-center">
                            <input type="submit" class="btn btn-success" name="submit" value="Save">
                          </div>
                        </div>
                      </form>
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
