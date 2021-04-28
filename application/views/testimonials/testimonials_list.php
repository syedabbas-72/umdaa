   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">TESTIMONIALS</li>
          </ol>
        </div>
       <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('Testimonials/testimonials_add');?>" class="btn btn-success box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> Add</a>
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
                                        <th>Description</th>
                                        <th>Testimonial Given By</th>   
                                        <th>Status</th>                                  
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php 
                                  $i=1; 
                                  foreach ($testimonials_list as $value) { 
                                  ?> 
                                    <tr>
                                      <td><?php echo $i++;?></td>
                                      <td><?php echo $value->title; ?></td>
                                      <td><?php echo $value->description; ?></td>
                                      <td><?php echo $value->testimonial_given_by; ?></td>
                                      <td><?=($value->status=="1")?'Active':'InActive'?></td>
                                      <td>
                                        <a href=""><i class="fa fa-eye"></i></a>
                                        <a href="<?php echo base_url('testimonials/testimonials_update/'.$value->testimonial_id);?>"><i class="fa fa-edit"></i></a>
                                        <a href="<?php echo base_url('testimonials/testimonials_delete/'.$value->testimonial_id);?>" onClick="return doconfirm();"><i class="fa fa-gavel"></i></a>
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



 