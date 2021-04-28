<style type="text/css">
  .note-editor.note-frame .note-editing-area .note-editable
  {
    /*height: 250px !important;*/
  }
  .select2-selection
  {
    border-radius: 4px !important;
    border-color: #7faac2 !important;
    
  }
</style>
<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i></li>
            <li class="parent-item"><a href="<?=base_url('Articles')?>">Articles</a> <i class="fas fa-angle-right"></i></li>
            <li class="active">Article Edit</li>
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
                  <?php
                  if($articles_list->doctor_comments != "" && $articles_list->article_status == "re-review")
                  {
                    ?>
                    <div class="row col-md-12">
                      <div class="col-md-12">
                        <!-- <h5 class="mb-0">Comments By Doctor To Re-Edit</h5> -->
                        <p class="font-italic text-center bg-danger rounded-top rounded-bottom p-2 mb-0"><?=$articles_list->doctor_comments?> - <?=getDoctorName($articles_list->posted_by)?></p>
                      </div>
                    </div>
                    <?php
                  }
                  ?>
                  <form method="post" enctype="multipart/form-data" action="<?php echo base_url('Articles/articles_update/'.$articles_list->article_id);?>" autocomplete="off">
                          <div class="row col-md-12">
                              <div class="col-md-6">
                               <div class="form-group">
                                <label for="title" class="col-form-label">Title <span class="required">*</span></label>
                                <input  name="article_title" type="text" placeholder="Title For Article" class="form-control" required="" value="<?=$articles_list->article_title?>" >
                                </div>
                              </div>
                              <!-- <div class="col-md-6">
                                <div class="form-group">
                                  <label class="col-form-label">Article Department</label>
                                  <select class="form-control article_department" name="article_department[]" multiple="" required="">
                                    <option  disabled="">Select Article Department</option>
                                    <option value="0">All Departments</option>
                                    <?php
                                    $i=0;
                                    foreach ($departments as $value) {
                                      ?>
                                      <option value="<?=$value->department_id?>" <?=($dept_ids[$i]->department_id==$value->department_id)?'selected':''?> ><?=$value->department_name?></option>
                                      <?php
                                      $i++;
                                    }
                                    ?>
                                  </select>
                                </div>
                              </div> -->
                          <div class="col-md-6">
                                <div class="form-group">
                                <?php
                                if($this->session->userdata('user_id') == $articles_list->posted_by)
                                {
                                  ?>
                                    <label class="col-form-label">Departments <span class="required">*</span></label>
                                    <select class="form-control article_department" name="departments[]" multiple="" required="">
                                      <option disabled="">Select Doctor</option>
                                      <?php
                                      foreach ($departments as $value) {
                                        ?>
                                        <option value="<?=$value->department_id?>" <?=(in_array($value->department_id,$dept))?'selected':''?> ><?=$value->department_name?></option>
                                        <?php
                                      }
                                      ?>
                                    </select>
                                  <?php
                                }
                                else
                                {
                                  ?>
                                  <label class="col-form-label">Selected Departments</label>
                                  <?php
                                  if($dept[0] == 0)
                                  {
                                    $deptname = "All Departments, ";
                                  }
                                  else
                                  {
                                    foreach($dept as $value)
                                    {
                                      $deptInfo = $this->db->query("select department_name from department where department_id='".$value."'")->row();
                                      $deptname .= $deptInfo->department_name.", ";
                                    }
                                  }
                                  echo "<p class='p-0'>".substr($deptname,0,-2)."</p>";
                                }
                                ?>
                                </div>
                          </div>
                            </div>
                            <div class="row col-md-12 ">
                              <div class="col-md-6"><div class="form-group">
                                <label for="title" class="col-form-label">Article Type <span class="required">*</span></label>
                                <select class="form-control article_type" name="article_type" required="">
                                  <option selected="" disabled="">Select Article Type</option>
                                  <option value="pdf" <?=(strtolower($articles_list->article_type)=="pdf")?'selected':''?> >PDF</option>
                                  <option value="image" <?=(strtolower($articles_list->article_type)=="image")?'selected':''?> >Image</option>
                                  <option value="video" <?=(strtolower($articles_list->article_type)=="video")?'selected':''?> >Video</option>
                                </select>
                                </div>
                              </div>
                              <div class="col-md-6 video_url_div"><div class="form-group">
                                <label for="title" class="col-form-label">Video URL</label>
                                  <input  name="videoURL" type="text" placeholder="Paste The YouTube Link" class="form-control" value="<?=$articles_list->video_url?>" >
                                </div>
                              </div>
                              <div class="col-md-4 image_div"><div class="form-group">
                                <label for="title" class="col-form-label">Image</label>
                                  <input  name="article_image" type="file" placeholder="Upload Image" class="" >
                                </div>
                              </div>
                              <div class="col-md-4 pdf_div"><div class="form-group">
                                  <label for="title" class="col-form-label">PDF</label>
                                  <br>
                                  <a href="<?=base_url('uploads/article_videos/'.$articles_list->posted_url)?>" target="_blank" class="btn btn-app btn-xs">Show File</a>
                                  <?php
                                  if($articles_list->created_by == $this->session->userdata('user_id'))
                                  {
                                    ?>
                                    <input type="file" name="article_pdf" placeholder="Upload File">
                                    <?php
                                  }
                                  ?>
                                </div>
                              </div>
                            </div>
                            <div class="row col-md-12">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label class="col-form-label">Short Description <span class="required">*</span></label>
                                  <textarea class="form-control" style="height: 75px" required="" name="short_description"><?=$articles_list->short_description?></textarea>
                                </div>
                              </div>
                            </div>

                            <div class="row col-md-12">
                              <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="Read Link" class="col-form-label">Read Article Link</label>
                                    <input  name="read_link" type="text" placeholder="Link For Article" class="form-control" value="<?=$articles_list->read_article_link?>" >
                                    </div>
                                  </div>
                                <!-- </div> -->
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="Read Link" class="col-form-label">Article Author</label>
                                    <input  name="article_author" type="text" placeholder="Enter Article Author" class="form-control" value="<?=$articles_list->article_author?>" >
                                    </div>
                                  </div>
                                </div>
                            </div>
                          <div class="row col-md-12">
                            <?php
                            if($articles_list->article_type == "video")
                            {
                              ?>
                              <div class="col-md-4 thumbnail_div">
                                <div class="form-group">
                                  <label for="title" class="col-form-label">Thumbnail</label><br>
                                  <input  name="thumbnail" type="file" placeholder="Upload Image" class="form-control"  accept="image/x-png,image/gif,image/jpeg">
                                </div>
                                <?php 
                                if($articles_list->video_image!="")
                                {
                                  ?>
                                  <a href="<?=$articles_list->video_image?>" class="pl-2" target="_blank">
                                  <img src="<?=$articles_list->video_image?>" class="img-thumbnail w-25" >
                                  </a>
                                  <?php
                                }
                                ?>
                              </div>
                              <?php
                            }
                            ?>
                            <div class="col-md-3">
                              <div class="form-group">
                                <?php
                                if($this->session->userdata('user_name') == "superadmin")
                                {
                                  if($articles_list->article_status == "")
                                  {
                                    ?>
                                    <label class="col-form-label">Article Status</label>
                                    
                                    <select class="form-control" name="publish_status" disabled>
                                      <option selected="" disabled="">Select Status</option>
                                      <option value="reviewed" <?=($articles_list->article_status=="waiting" || $articles_list->article_status=="re-review")?'selected':''?> >Review Done</option>
                                      <option value="published" <?=($articles_list->article_status=="approved")?'selected':''?> >Publish</option>
                                    </select>  
                                    <?php
                                  }
                                }
                                ?>
                              </div>
                            </div>
                            <?php
                            if($this->session->userdata('user_id') == $articles_list->posted_by)
                            {
                              ?>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <label class="col-form-label">Patient Visibility</label><br>
                                  <input type="radio" name="patient_visibility" value="1" <?=($articles_list->citizens=="1")?'checked':''?> > Yes
                                  <input type="radio" name="patient_visibility" value="0" <?=($articles_list->citizens=="0")?'checked':''?> > No
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <label class="col-form-label">Doctor Visibility</label><br>
                                  <input type="radio" name="doctor_visibility" value="1" <?=($articles_list->doctors=="1")?'checked':''?> > Yes
                                  <input type="radio" name="doctor_visibility" value="0" <?=($articles_list->doctors=="0")?'checked':''?> > No
                                </div>
                              </div>
                              <?php
                            }
                            ?>
                          </div>
                          <?php
                          if($articles_list->article_type == "image")
                          {
                            $images = explode(",", $articles_list->posted_url);
                            ?>
                            <div class="row col-md-12">
                              <div class="col-md-12">
                                <span class="page-title noBdr m-0">Images</span>
                              </div>
                              <?php
                              foreach($images as $value)
                              {
                                ?>
                                <div class="col-md-3">
                                  <img src="<?=base_url('uploads/article_images/'.$value)?>" class="img-thumbnail">
                                </div>
                                <?php
                              }
                              ?>
                            </div>
                            <?php
                          }
                          elseif($articles_list->article_type == "pdf")
                          {
                            ?>
                            <div class="row col-md-12 my-2">
                              <div class="col-md-12">
                                <a href="<?=base_url('uploads/article_pdf/'.$articles_list->posted_url)?>" target="_blank" class="btn btn-app btn-xs">Show PDF File</a>
                              </div>
                            </div> 
                            <?php
                          }
                          elseif($articles_list->article_type == "video")
                          {
                            ?>
                            <div class="row col-md-12 my-2">
                              <div class="col-md-12">
                                <a href="<?=base_url('uploads/article_videos/'.$articles_list->posted_url)?>" target="_blank" class="btn btn-app btn-xs">Show Video File</a>
                              </div>
                            </div> 
                            <?php
                          }
                          ?>
                          
                           <div class="col-md-6">
                              <div class="pull-right">
                                  <input type="hidden" value="<?=$articles_list->article_status?>" name="articleStatus">
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

<script type="text/javascript">
 $(document).ready(function () {

      <?php 
        if(strtolower($articles_list->article_type) == "video")
        {
          ?>
            $('.video_url_div').show();
          <?php
        }
        else
        {
          ?>
          $('.video_url_div').hide();
          <?php
        }
        ?>

      
      $('.image_div').hide();  
      $('.pdf_div').hide();  

      $('.article_type').on("change",function(){
        var value = $(this).val();
        if(value == "video")
        {
          $('.image_div').hide();  
          $('.video_url_div').show("2000");
          $('.thumbnail_div').show("2000");
        }
        else if(value == "image")
        {
          $('.video_url_div').hide();  
          $('.thumbnail_div').hide();
          $('.image_div').show("2000");
        }
        else if(value == "pdf")
        {
          $('.video_url_div').hide("2000");  
          $('.thumbnail_div').hide();
          $('.image_div').hide("2000");
        }
      });

      $('#description').summernote({});
});
</script>
<script type="text/javascript">
  $(document).ready(function() {
      $('.article_department').select2();
  });
</script>
       