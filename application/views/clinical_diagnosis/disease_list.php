<div class="row page-header no-background no-shadow margin-b-0">
    <div class="col-lg-6 align-self-center">          
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">Clinical Diagnosis</li>
        </ol>
    </div>
    <div class="col-lg-6 align-self-center text-right">
        <a href="<?= base_url('Clinical_diagnosis/disease_add'); ?>" class="btn btn-primary btn-rounded box-shadow btn-icon"><i class="fa fa-plus"></i> ADD</a> <a class="btn btn-primary btn-rounded box-shadow btn-icon" data-toggle="modal" data-target="#exampleModalCenter">Bulk Upload</a> 
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="pharmacy_list" class="table table-striped dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>S.No:</th>
                                <th>Disease Name</th>
                                <th>Code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach ($diseases as $value) { ?> 
                                <tr>
                                    <td><?php echo $i++;?></td>
                                    <td><?php echo $value->disease_name; ?></td>
                                    <td><?php echo $value->code; ?></td>
                                    
                                    <td><!-- <a target="_blank" href=""><i class="fa fa-eye"></i></a> -->
                                        <a href="<?php echo base_url('Clinical_diagnosis/disease_update/'.$value->clinical_dianosis_id);?>"><i class="fa fa-edit"></i></a>
                                        <a href="<?php echo base_url('Clinical_diagnosis/disease_delete/'.$value->clinical_diagnosis_id);?>" ><i class="fa fa-trash"></i></a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php /*
        <div>
            
            <form action="<?=base_url('Clinical_diagnosis/bulk_save'); ?>" method="POST" id="importForm" enctype="multipart/form-data">
                <input type="file" id="myFile" name="userfile">
                <input type="submit" value="Import" name="importfile" >
            </form>
            
            <form action="<?=base_url('Clinical_diagnosis/bulk_save'); ?>" method="post" enctype="multipart/form-data">
              <p>Upload file: <input type="file" name="file3">
              <p><button type="submit" value="Import" name="importfile">Submit</button>
            </form>
        </div>
        */ ?>
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
                    $output .= form_open(base_url('Clinical_diagnosis/bulk_save'), 'class="form-horizontal" enctype="multipart/form-data"');  
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
                    $output .= '</div><span style="color:red;">*Please choose an Excel file(.xls or .xlxs) as Input</span></div>';
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
            $('#pharmacy_list').dataTable();
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



