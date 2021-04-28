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
        <li class="parent-item"><a href="<?=base_url('Articles')?>">Articles</a> <i class="fa fa-angle-right"></i></li>
            
            <li class="active">Add Article</li>
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
                  <form method="post" enctype="multipart/form-data" action="<?php echo base_url('Articles/articles_add');?>" autocomplete="off">
                  		  <div class="row col-md-12">
                  		  </div>
                          <div class="row col-md-12">
                              <div class="col-md-6"><div class="form-group">
                                <label for="title" class="col-form-label">Title <span class="required">*</span></label>
                                <input  name="article_title" type="text" placeholder="Title For Article" class="form-control" required="" >
                                </div>
                              </div>
                  		  	<div class="col-md-6">
                                <div class="form-group">
                                  <label class="col-form-label">Departments</label>
                                  <select class="form-control article_department" name="departments[]" required="" multiple >
                                    <option disabled="">Select Department</option>
                                    <?php
                                    foreach ($departments as $value) 
                                    {
                                      ?>
                                      <option value="<?=$value->department_id?>"><?=$value->department_name?></option>
                                      <?php
                                    }
                                    ?>
                                  </select>
                                </div>
                  		  	</div>
                            </div>
                            <div class="row col-md-12 ">
                              <div class="col-md-6"><div class="form-group">
                                <label for="title" class="col-form-label">Article Type <span class="required">*</span></label>
                                <select class="form-control article_type" name="article_type" required="">
                                  <option selected="" disabled="">Select Article Type</option>
                                  <option value="PDF">PDF</option>
                                  <option value="image">Image</option>
                                  <option value="Video">Video</option>
                                </select>
                                </div>
                              </div>
                              <div class="col-md-6 video_url_div"><div class="form-group">
                                <label for="title" class="col-form-label">Video URL</label>
                                  <input  name="videoURL" type="text" placeholder="Paste The YouTube Link" class="form-control" >
                                </div>
                              </div>
                              <div class="col-md-6 pdf_div"><div class="form-group">
                                <label for="title" class="col-form-label">PDF</label>
                                  <input  name="pdf" type="file" placeholder="Upload File" class="form-control" accept="application/pdf" >
                                </div>
                              </div>
                              <div class="col-md-6 image_div"><div class="form-group">
                                <label for="title" class="col-form-label">Image</label>
                                  <input  name="article_image[]" type="file" multiple="" placeholder="Upload Image" class="form-control" >
                                </div>
                              </div>
                            </div>
                            <div class="row col-md-12">
                              <div class="col-md-6 ">
                              </div>
                            </div>
                            <div class="row col-md-12">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label class="col-form-label">Short Description <span class="required">*</span>(Max Length 700 Characters only)</label>
                                  <textarea maxlength="700" class="form-control" style="height: 75px" required="" name="short_description"></textarea>
                                </div>
                              </div>
                            </div>
                            <div class="row col-md-12">
                              <div class="col-md-6">
                              <div class="form-group">
                                <label for="title" class="col-form-label">Posted Department </label>
                                <select class="form-control" name="posted_dep">
                                    <option disabled="" selected>Select Department</option>
                                    <?php
                                    foreach ($departments as $value) 
                                    {
                                      ?>
                                      <option value="<?=$value->department_id?>"><?=$value->department_name?></option>
                                      <?php
                                    }
                                    ?>
                                </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                              <div class="form-group">
                                <label for="title" class="col-form-label">Tags</label>
                                <input  name="tags" type="text" class="form-control">
                                </div>
                              </div>
                            </div>
                            <div class="row col-md-12">
                              <div class="col-md-6">
                              <div class="form-group">
                                <label for="title" class="col-form-label">Read Article Link </label>
                                <input  name="read_link" type="text" placeholder="Enter Read More Link" class="form-control">
                                </div>
                              </div>
                              <div class="col-md-6">
                              <div class="form-group">
                                <label for="title" class="col-form-label">Article Author By</label>
                                <input  name="article_author" type="text" placeholder="Enter Article Author" class="form-control">
                                </div>
                              </div>
                            </div>
                          <div class="row col-md-12">
                            <div class="col-md-4 thumbnail_div">
                            	<div class="form-group">
                                <label for="title" class="col-form-label">Thumbnail</label><br>
                                  <input  name="thumbnail" type="file" placeholder="Upload Image" class="form-control" >
                                </div>
                            </div>
                            <!-- <div class="col-md-2">
                              <div class="form-group">
                                <?php
                                if($this->session->userdata('user_name') == "superadmin")
                                {
                                  ?>
                                  <label class="col-form-label">Article Status</label>
                                  <select class="form-control" name="articleStatus">
                                    <option selected="" disabled="">Select Article Status</option>
                                    <option value="">Done</option>
                                    <option value="0">Not Done</option>
                                  </select>
                                  <?php
                                }
                                else
                                {
                                  ?>
                                  <p>Sent For Review</p>
                                  <?php
                                }
                                ?>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <?php
                                if($this->session->userdata('user_name') == "superadmin")
                                {
                                  ?>
                                  <label class="col-form-label">Publish Status</label>
                                  <select class="form-control" name="publish_status">
                                    <option selected="" disabled="">Select Publish Status</option>
                                    <option value="1">Published</option>
                                    <option value="0">Not Published</option>
                                  </select>
                                  <?php
                                }
                                ?>
                              </div>
                            </div> -->
                            <div class="col-md-2">
                              <div class="form-group">
                                <label class="col-form-label">Patient Visibility</label><br>
                                <input type="radio" name="patient_visibility" value="1"> Yes
                                <input type="radio" name="patient_visibility" value="0"> No
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label class="col-form-label">Doctor Visibility</label><br>
                                <input type="radio" name="doctor_visibility" value="1"> Yes
                                <input type="radio" name="doctor_visibility" value="0"> No
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label class="col-form-label">Partner Visibility</label><br>
                                <input type="radio" name="partner_visibility" value="1"> Yes
                                <input type="radio" name="partner_visibility" value="0"> No
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

<script type="text/javascript">
 $(document).ready(function () {

      $('.video_url_div').hide();
      $('.image_div').hide();  
      $('.pdf_div').hide();  

      $('.article_type').on("change",function(){
        var value = $(this).val();
        if(value == "Video")
        {
          $('.image_div').hide(); 
          $('.pdf_div').hide();  
          $('.video_url_div').show("2000");
        }
        else if(value == "image")
        {
          $('.pdf_div').hide();  
          $('.video_url_div').hide();  
          $('.image_div').show("2000");
        }
        else if(value == "PDF")
        {
          $('.video_url_div').hide("2000");  
          $('.image_div').hide("2000");
          $('.pdf_div').show("2000");
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
       