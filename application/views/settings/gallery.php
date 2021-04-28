<!-- Lightbox-->
        <link href="<?php echo base_url(); ?>assets/lib/lightbox2/dist/css/lightbox.css" rel="stylesheet">
        <style type="text/css">
            .close{
    background: red;
    padding: 10px;
    position: absolute;
    color: white !important;
    right: 2px;
    top: 2px;
    z-index: 999;

 
            }

            .img {
    overflow:hidden;
    width:  100%;
    height: 150px;
    background-position: 50% 50%;
    background-repeat:   no-repeat;
    background-size:     cover;
}
    
        </style>
<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li><a href="<?=base_url("settings")?>">Settings</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active"><a href="<?=base_url("settings/gallery")?>">Gallery</a></li>
        </ol>
    </div>
</div>
<?php if($this->session->flashdata('msg')): ?>
    <p><?php echo $this->session->flashdata('msg'); ?></p>
<?php endif; ?>
 
   <div class="row">
        <div class="col-2 list-group ">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                 <?php $this->view("settings/settings_left_nav"); ?> 
                
            
            </div>
        </div>
        <div class="col-10 mb-1">
            <div class="card">
             
                <div class="card-body">
                    <div class="row col-md-12">
                        <div class="col-12">
                            <div class="tab-content">
                                <div class="tab-pane active" id="practice_details">
                                <span class="page-title">Clinic Gallery</span>
                                    <form method="POST" action="<?php echo base_url('settings/upload_gallery');?>" enctype="multipart/form-data">
                                        <div class=" row col-md-12">
                                   <div class="col-md-8">
                             
                                <div id="uploadme" class="fallback">
           
                                <!-- <label type="submit" for="upload-photo" style="padding-left:10px;padding-right:10px;">Choose Image Files Here</label><div id="file-upload-filename"></div> -->
                           <span class="font-weight-bold text-primary">Select Files To Upload</span>                     
                           <input name="file[]" type="file" class="form-control" required id="upload-photo" multiple  accept="image/*" />
                           <p class="small font-weight-bold font-italic p-0 text-danger">Only Images (.jpg, .png) Files are Supported.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary mt-4">Submit</button>
                            </div>
                            </div>

                                 </form>
                                 </div>
                                    

                                </div>
                                
    
                                
                                <!-- /.tab-pane -->
                            </div>
                        </div>
                        <!-- <hr> -->
     <!-- START -->
     <div class="card-body">
        <div class="row col-md-12 subHeader">
            <div class="">
                <span class="font-weight-bold text-uppercase">Uploaded Gallery</span>
            </div>
        </div>
        <div class="">
            <div class="row col-md-12">
                        
                      <?php 
                                if(count($gallery_images)>0){
                                    foreach ($gallery_images as  $image) {
                      ?>
                     <div class="col-md-3 mb-2">
                         <a id="<?php echo $image->clinic_gallery_id; ?>" style="position: relative;z-index: 0" >
                          <img style="vertical-align: top;" data-toggle="modal" data-target="#ImgModal" data-id="<?php echo base_url('uploads/clinic_gallery/'.$image->image); ?> " class="showImage img-fluid img-thumbnail" src="<?php echo base_url('uploads/clinic_gallery/'.$image->image); ?> " alt="">
                          <span id="remove_img" class="badge badge-danger position-absolute" style="margin-left:-10px !important">&times;</span>
                         </a>
                     </div>
                      <?php
                                    }
                                }
                      ?>
            </div>
        </div>
     </div>
     <!-- END -->
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>


        <div class="modal fade" id="ImgModal">
            <div class="modal-dialog modal-fluid">
                <div class="modal-content" style="background:transparent !important;box-shadow:none !important">
                    <div class="modal-body text-center">
                        <div class="">
                        </div>
                            <img id="modalImg">
                            <a href="" data-dismiss="modal">
                            <span class="badge badge-primary position-absolute pull-right" style="font-size:26px !important;height:auto !important;margin-left:-20px !important">&times;</span>
                            </a>
                    </div>
                </div>
            </div>
        </div>

                         
       <script>
        $(document).ready(function(){
            $('.showImage').on("click",function(){
                var src = $(this).attr("data-id");
                $('#modalImg').attr("src",src);
            });
        });
       </script>

        <script type="text/javascript">
           
             $(document).on("click","#remove_img",function(){
   
       
      var gallery_id = $(this).closest("a").attr("id");


      if (confirm("Are you sure you want to delete image? ")) {

       $.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>settings/delete_gallery_image',
    data:{ gid:gallery_id},
    success: function(result)
      {
       
        $(".lightboxGallery a[id="+gallery_id+"]").remove();
        window.location.href="";
        
      }       
             

     });
    }
    });


    var input = document.getElementById( 'upload-photo' );
var infoArea = document.getElementById( 'file-upload-filename' );

input.addEventListener( 'change', showFileName );

function showFileName( event ) {
  
  // the change event gives us the input it occurred in 
  var input = event.srcElement;
  
  // the input has an array of files in the `files` property, each one has a name that you can use. We're just using the name here.
  var fileName = input.files[0].name;
  
  // use fileName however fits your app best, i.e. add it into a div
  infoArea.textContent = 'File name: ' + fileName;
}

        </script>