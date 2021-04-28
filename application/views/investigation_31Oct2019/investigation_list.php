  <div class="page-bar">
   <div class="page-title-breadcrumb">
      <div class=" pull-left">
         <div class="page-title"><?php
            echo $_SESSION['clinic_name'];
            ?></div>
      </div>
      <ol class="breadcrumb page-breadcrumb pull-right">
         <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php
            echo base_url("dashboard");
            ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
         </li>
         <li class="active">Investigations</li>
      </ol>
     <!--  <div class="col-lg-6 align-self-center text-right">
                 <a class="btn btn-primary btn-rounded box-shadow btn-icon" data-toggle="modal" data-target="#exampleModalCenter">Bulk Upload</a> 
        </div> -->
   </div>
</div>
<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="investigation_list" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>INVESTIGATION&nbsp;CODE</th>
                                        <th>INVESTIGATION</th>
                                        <th>CATEGORY</th>
                                       
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($investigations as $value) { ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td><?php echo $value->item_code; ?></td>
                                        <td><?php echo $value->investigation; ?></td>
                                        <td><?php echo $value->category; ?></td>
                                      
                                        <td>
                                          <a class="btn btn-primary btn-xs" href="<?php echo base_url('Investigation/investigation_update/'.$value->investigation_id);?>"><i class="fa fa-edit"></i></a>
                                          <a class="btn btn-danger btn-xs" href="<?php echo base_url('Investigation/investigation_delete/'.$value->investigation_id);?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a></td>
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
$output .= form_open(base_url('Investigation/template_lineitems_save'), 'class="form-horizontal" enctype="multipart/form-data"');  
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
      $('#investigation_list').dataTable();
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



 