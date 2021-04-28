   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">FORMS LIST</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('FormBuilder/form_add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> ADD</a>
        </div>
    </div>



<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="clinic_list" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>FORM NAME</th>
										<th>FORM TYPE</th>
										<th>DEPARTMENT</th>										
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($forms_list as $value) {
												$CI=&get_instance();
												$res=$CI->db->query("SELECT department_name FROM department where department_id='".$value->department_type."'")->row()
								   ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td> 
										<td><?php echo $value->form_name;?></td>
										<td><?php echo $value->form_type;?></td>
										<td><?php echo $res->department_name?></td>	
                                        <td>
										<a href="<?php echo base_url('FormBuilder/form_section/'.$value->form_id);?>" title='Create Section' style="padding:5px;"><i class="fa fa-plus"></i></a>
										<a href="<?php echo base_url('FormBuilder/display_form/'.$value->form_id);?>" title='View Sections' style="padding:5px;">	<i class="fa fa-eye"></i></a>
                                        <a href="<?php echo base_url('FormBuilder/form_delete/'.$value->form_id);?>" onClick="return doconfirm();" style="padding:5px;"><i class="fa fa-trash"></i></a></td>
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
      $('#clinic_list').dataTable();
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



