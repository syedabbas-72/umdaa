<div class="page-bar">
    <div class="page-title-breadcrumb">
        <div class=" pull-left">
            <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
        </div>
        <ol class="breadcrumb page-breadcrumb pull-right">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>

            <li class="active">Consent Forms</li>
        </ol>
    </div>
</div>
<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="clinic_doctor_list" class="table table-striped dt-responsive ">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>TITLE</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach ($Consentform_list as $value) { ?> 
                                <tr>
                                    <td><?php echo $i++;?></td>
                                    <td><?php echo $value->consent_form_title; ?></td>
                                    <td style="padding-right: 5px;">
                                        <a class="btn btn-info btn-xs" style="margin-right:0px !important" target="_blank" href="<?php echo base_url('Consentform/Consentform_view/'.$value->consent_form_id);?>"><i class="fa fa-eye"></i></a>
                                        <a class="btn btn-primary  btn-xs" style="margin-right:0px !important"  href="<?php echo base_url('Consentform/Consentform_edit/'.$value->consent_form_id);?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger  btn-xs" href="<?php echo base_url('Consentform/Consentform_delete/'.$value->consent_form_id);?>" onclick="return confirm('Are you sure you want to delete?');"><i class="fa fa-trash"></i></a></td>
                                    </tr>
                                <?php } ?>  
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        <div>
            <form action="<?=base_url('Consentform/bulk_save'); ?>" method="post" enctype="multipart/form-data">
                Upload file: <input type="file" name="file3">
                <button type="submit" value="Import" name="importfile">Submit</button>
            </form>
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
                    $output .= form_open(base_url('Consentform/save'), 'class="form-horizontal" enctype="multipart/form-data"');  
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
            $('#clinic_doctor_list').dataTable();
        });
    </script>