   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">SALT LIST</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('Salt/add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> ADD</a>
        </div>
    </div>



<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="doctorlist" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No:</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($salt_list as $value) { ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <!-- <td><?php echo $value->title;?></td> -->
                                        <td><?php echo $value->salt_name; ?></td>
                                        <?php if($values->status == 1) { ?>
                                        <td><?php echo $values->status;?>Inactive</td>
                                      <?php } else {?>
                                         <td><?php echo $values->status;?>Active</td>
                                      <?php } ?>

                                        <td>
                                          <a href="<?php echo base_url('salt/edit/'.$value->salt_id);?>"><i class="fa fa-edit"></i></a>
                                          <a href="<?php echo base_url('salt/delete/'.$value->salt_id);?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a></td>
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
      $('#doctorlist').dataTable();
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



