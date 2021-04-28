<div class="row page-header">
   <div class="col-lg-6 align-self-center">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">DRUG</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>
        </ol>
  </div>
   <div class="col-lg-6 align-self-center text-right">
                 <a class="btn btn-primary btn-rounded box-shadow btn-icon" data-toggle="modal" data-target="#exampleModalCenter">Bulk Upload</a> 
        </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                          <form method="POST" action="<?php echo base_url('Drug/drug_add');?>" role="form">
                            <div class="row col-md-12">
                                <div class="col-md-3"><div class="form-group">
                                    <label for="salt" class="col-form-label">SALT </label>
                                     <select id="salt_id" name="salt_id" type="text" placeholder="" class="form-control" >
                                        <option>--select--</option>
                                        <?php foreach ($salt as $value) {?>
                                        <option value="<?php echo $value->salt_id;?>">
                                          <?php echo $value->salt_name;?>
                                         </option>
                                      <?php } ?>
                                    </select>
                                </div></div>
                                
                                <div class="col-md-3"><div class="form-group">
                                    <label for="trade_name" class="col-form-label">TRADE NAME</label>    
                                    <input id="trade_name" name="trade_name" type="text" placeholder="" class="form-control-demo" required="">
                                </div>
                              </div>
                            </div> 


                            <div class="row col-md-12">
                                <div class="col-md-3"><div class="form-group">
                                    <label for="formulation" class="col-form-label">FORMULATION</label>    
                                    <input id="formulation" name="formulation" type="text" placeholder="" class="form-control-demo" required="">
                                </div></div>
                                <div class="col-md-3"><div class="form-group">
                                    <label for="composition" class="col-form-label">COMPOSITION</label>
                                   <input id="composition" name="composition" type="text" placeholder="" class="form-control-demo" required="">
                                  </div>
                                </div>
                                
                            </div> 
                
                                <div class="col-sm-6">
                                        <div class="pull-right">
                                            <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                        </div>
                                    </div>
                                </form>
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
$output .= form_open(base_url('drug/save'), 'class="form-horizontal" enctype="multipart/form-data"');  
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