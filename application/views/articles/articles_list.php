   <style type="text/css">
     td
     {
      white-space: unset !important;
     }
   </style>
<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a></li>
            <li class="active">Articles</li>
        </ol>
    </div>
    <div class="pull-right">
          <a href="<?php echo base_url('Articles/articles_add');?>" class="btn btn-app mt-3"><i class="fa fa-plus"></i> Post An Article</a>
    </div>
</div>

<!-- Article View Modal Start here -->
  
  <div class="modal fade" id="viewModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Article Details</h4>
          <button class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row">
              <div class="col-md-12 article_body">
                <p class="text-center">Loading Article Data <i class="fa fa-spinner fa-spin"></i></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Article View Modal Ends Here -->


<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="testimonials_list" class="table customTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Article Type</th>
                                        <th>Status</th>
                                        <th>Posted By</th>
                                        <th>Posted Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php 
                                  $i=1; 

                                  foreach ($articles_list as $value) { 
                                    if($value->article_status == "waiting")
                                    {
                                      $status = "Waiting For Review";
                                    }
                                    elseif($value->article_status == "approved")
                                    {
                                      $status = "Approved By Doctor. Ready To Publish.&emsp;<a href='".base_url('Articles/Publish/'.$value->article_id)."' class='btn btn-small btn-danger p-1'>Publish Now</a>";
                                    }
                                    elseif($value->article_status == "reviewed")
                                    {
                                      $status = "Review Done";
                                    }
                                    elseif($value->article_status == "re-review")
                                    {
                                      $status = "Review Again";
                                    }
                                    elseif($value->article_status == "published")
                                    {
                                      $status = "Published";
                                    }
                                    
                                    $doctors = $this->db->query("select * from doctors where doctor_id='".$value->posted_by."'")->row();
                                    $review = $this->db->query("select * from users where user_id='".$value->review_by."'")->row();
                                  ?> 
                                    <tr>
                                      <td><?php echo $i++;?></td>
                                      <td><?php echo $value->article_title; ?></td>
                                      <td><?=ucwords($value->article_type)?></td>
                                      <td><?=$status?></td>
                                      <td><?=(getDoctorname($value->posted_by)=="Dr. ")?'<span>UMDAA HEALTH CARE</span>':getDoctorname($value->posted_by)?></td>
                                      <td><?=date('D d M Y h:i A', strtotime($value->created_date_time))?></td>
                                      <td>
                                        <a class="articleView" data-id="<?=$value->article_id?>" data-toggle="modal" data-target="#viewModal"><i class="fa fa-eye"></i></a>
                                        <a href="<?php echo base_url('Articles/articles_update/'.$value->article_id);?>"><i class="fa fa-edit"></i></a>
                                        <a href="<?php echo base_url('Articles/articles_delete/'.$value->article_id);?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a>
                                      </td>
                                    </tr>
                                  <?php } ?>
                               
                                    
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

    </section>

 <script>
  $(document).ready(function () {
      $('#testimonials_list').dataTable();
  });
  </script>
  <script type="text/javascript">
    $(document).ready(function(){
      $('.articleView').on("click",function(){
        var article_id = $(this).attr("data-id");
        $.post("<?=base_url("Articles/getArticleData")?>",{article_id:article_id},function(data){
          $('.article_body').html(data);
        });
      });
    });
  </script>
  <script>
  function doconfirm()
    {
        if(confirm("Delete selected messages ?")){
            return true;
        }else{
            return false;  
        } 
   }
  </script>



 