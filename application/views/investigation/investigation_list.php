<?php
// get CRUD info
$crudInfo = getcrudInfo('Investigation');
?>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Lab Investigations</li>           
        </ol>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="row col-md-12 page-title">
                    <div class="pull-left col-md-6">Lab Investigation List</div>
                    <!-- Create -->
                    <?php if($crudInfo->p_create){ ?>
                    <div class="pull-right col-md-6 text-right actionButtons">
                        <i class="fas fa-plus add"></i>
                    </div>
                    <?php } ?>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table id="investigation_list" class="table customTable">
                            <thead>
                                <tr>
                                    <th style="width: 8%" class="text-center">S.No.</th>
                                    <th style="width: 15%" class="text-center">Item code</th>
                                    <th style="width: 50%">Investigation</th>
                                    <th style="width: 12%" class="text-center">Category</th>
                                    <th style="width: 15%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach ($investigations as $value) { ?> 
                                    <tr>
                                        <td class="text-center"><?php echo $i++;?></td>
                                        <td class="text-center"><?php echo $value->item_code; ?></td>
                                        <td><?php echo $value->investigation; ?></td>
                                        <td class="text-center"><?php echo $value->category; ?></td>
                                        <td class="text-center actions">
                                            <!-- Edit -->
                                            <?php if($crudInfo->p_update == 1){ ?>
                                                <a href="<?php echo base_url('Investigation/investigation_update/'.$value->investigation_id);?>"><i class="fas fa-pencil-alt editSmall" title="Edit"></i></a>
                                            <?php } ?>

                                            <!-- Delete -->
                                            <?php if($crudInfo->p_delete == 1){ ?>
                                                <a href="<?php echo base_url('Investigation/investigation_delete/'.$value->investigation_id);?>" onClick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall" title="Delete"></i></a>
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