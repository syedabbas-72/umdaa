   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">INVESTIGATION</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('Investigation/investigation_add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i>Add</a><a href = ""  class="btn btn-primary btn-rounded box-shadow btn-icon" data-toggle="modal" data-target="#exampleModalCenter">Bulk Upload</a>
        </div>
    </div>
<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table  table-sm">
                                <thead class="table-info">
                                    <tr>
                                        <th>S.No</th>
                                        <th>INVESTIGATION CODE</th>
                                        <th>INVESTIGATION</th>
                                        <th>CATEGORY</th>
                                        <th>MRP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach($investigations_list as $value) { 
                                    $inv_deatails = $this->Generic_model->getSingleRecord('investigations',array('investigation_id'=>$value->investigation_id),$order='');
                                    ?> 
                                    <tr>
                                        <td><label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id = "inv" value = "<?php echo $inv_deatails->investigation_id;
                                        ?>">
                                        <span class="custom-control-indicator"></span>
                                        </label></td>
                                        <td><?php echo $inv_deatails->investigation_code;?></td>
                                        <td><?php echo $inv_deatails->investigation;?></td>
                                        <td><?php echo $inv_deatails->category;?></td>
                                        <td><?php echo $inv_deatails->mrp;?></td>   
                                    </tr>
                                  <?php } ?>
                                </tbody>
                            </table>
                          <div class = "row">
                            <div class = "col-sm-2">
                            <a class = "btn btn-success" id = "display" style = "display:none;">
                               Generate Invoice
                              </a>
                            </div>
                          </div>
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
$output .= form_open(base_url('Investigation/save'), 'class="form-horizontal" enctype="multipart/form-data"');  
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
  <script type="text/javascript">
    $(document).on('click','#inv',function(){
       var id = $(this).attr('value');
      if($('#display').css('display') == 'none')
      {
        //alert('<?php echo base_url();?>');
        $('#display').css('display','block');
        $('#display').attr('href','<?php echo base_url();?>Prescription/prescription_view_submit/'+id);
      }
      else
      {
        $('#display').css('display','none');
      }
    });
  </script>



