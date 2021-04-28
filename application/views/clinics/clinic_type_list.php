   <?php $entity_id = $this->uri->segment(2); 
$user_id=$this->session->userdata('user_id');
?>
   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">CLINIC TYPE LIST</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('Clinic/clinic_type_add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i>ADD CLINIC TYPE</a>
        </div>
    </div>



<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="clinic_type_list" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>CLINIC TYPE</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($clinic_type as $value) { ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td><?php echo $value->clinic_type;?></td>
                                        <td>
										<?php if(accessprofile($entity_id,P_UPDATE)){ ?>
                                          <a href="<?php echo base_url('clinic/clinic_update/'.$value->clinic_id);?>">
											<i class="fa fa-edit"></i>
										</a>
										<?php } ?>
										<?php if(accessprofile($entity_id,P_DELETE)){ ?>
                                          <a href="<?php echo base_url('clinic/clinic_delete/'.$value->clinic_id);?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a>
										<?php } ?>
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

 <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Upload File</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
<div class="modal-body">
     <?php
$output = ''; 
$output .= form_open(base_url('clinic/save'), 'class="form-horizontal" enctype="multipart/form-data"');  
$output .= '<div class="row">';
$output .= '<div class="col-lg-12 col-sm-12"><div class="form-group">';
$output .= form_label('Choose file', 'file');
$data = array(
    'name' => 'userfile',
    'id' => 'userfile',
    'class' => 'form-control filestyle',
    'value' => '',
    'data-icon' => 'false'
);
$output .= form_upload($data);
$output .= '</div> <span style="color:red;">*Please choose an Excel file(.xls or .xlxs) as Input</span></div>';
$output .= '<div class="col-lg-12 col-sm-12"><div class="form-group text-right">';
$data = array(
    'name' => 'importfile',
    'id' => 'importfile-id',
    'class' => 'btn btn-primary',
    'value' => 'Import',
);
$output .= form_submit($data, 'Import Data');
$output .= '</div>
                        </div></div>';
$output .= form_close();
echo $output;
?>
</div>
     </div>
   </div>
 </div>

 <script>
  $(document).ready(function () {
      $('#clinic_type_list').dataTable();
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



