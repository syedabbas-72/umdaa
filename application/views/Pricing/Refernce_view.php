   <style type="text/css">
     td
     {
      white-space: unset !important;
     }
   </style>
   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">ARTICLES</li>
          </ol>
        </div>
       <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('Articles/articles_add');?>" class="btn btn-success box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> Post An Article</a>
        </div>
    </div>
<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="testimonials_list" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No:</th>
                                        <th>Title</th>
                                        <?=($this->session->userdata('user_name')=="superadmin")?'<th>Posted By</th>':''?>
                                        <th>Status</th>
                                        <th>Review By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php 
                                  $i=1; 

                                  foreach ($articles_list as $value) { 
                                    $doctors = $this->db->query("select * from doctors where doctor_id='".$value->posted_by."'")->row();
                                  ?> 
                                    <tr>
                                      <td><?php echo $i++;?></td>
                                      <td><?php echo $value->article_title; ?></td>
                                      <?php
                                      if($this->session->userdata('user_name')=="superadmin")
                                        {
                                          if($value->posted_by=="214")
                                          {
                                            ?>
                                            <td>Super Admin</td>
                                            <?php
                                          }
                                          else
                                          {
                                            ?>
                                            <td>Dr. <?=$doctors->first_name." ".$doctors->last_name?></td>
                                            <?php
                                          }
                                          
                                        }
                                        ?>
                                      <td>Status</td>
                                      <td><?=$value->review_by?></td>
                                      <td>
                                        <a href="<?=base_url('../citizens/inDetail.php?q='.$value->article_id)?>" target="blank"><i class="fa fa-eye"></i></a>
                                        <a href="<?php echo base_url('Articles/articles_update/'.$value->article_id);?>"><i class="fa fa-edit"></i></a>
                                        <a href="<?php echo base_url('Articles/articles_delete/'.$value->article_id);?>" onClick="return doconfirm();"><i class="fa fa-gavel"></i></a>
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



 