

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a></li>
            <li class="active">Citizens Tutorial Videos</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="page-title">Edit Tutorial Video</h4>
        <div class="row">
            <div class="col-12">
                <form action="<?=base_url('CitizenTutorials/edit/'.$info->umdaa_tutorial_id)?>" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-control-label">Tutorial Name</label>
                            <input type="text" class="form-control" name="tutorial_name" required value="<?=$info->tutorial_name?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-control-label">Video Description</label>
                            <input type="text" class="form-control" name="description"  value="<?=$info->tutorial_description?>">
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-4">
                            <label class="form-control-label">Video Thumbnail</label>
                            <input type="file" class="form-control" name="thumbnail">
                            <?php 
                            if($info->video_thumbnail != "")
                            {
                                ?>
                                <a href="<?=base_url('uploads/thumbnails/'.$info->video_thumbnail)?>" target="blank">
                                    <img src="<?=base_url('uploads/thumbnails/'.$info->video_thumbnail)?>" class="img-thumbnail img-100">
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-control-label">Video Link</label>
                            <input type="text" class="form-control" required name="tutorial_link" value="<?=$info->tutorial_link?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 ">
                            <button class="btn btn-app" type="submit" name="tutorialSubmit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.dataTable').DataTable();
    });
</script>