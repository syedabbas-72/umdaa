<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">Check List Items</li>
        </ol>
        <div class="pull-right mt-2">
          <a href="<?=base_url('Checklist/checklist_add')?>" class="btn btn-app"><i class="fa fa-plus"></i>&nbsp;Add Checklist</a>
        </div>
    </div>
</div>
<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="checklist_items_tbl" class="table table-bordered customTable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Check list Item</th>
                                <th>Check list Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach ($checklist_items as $item) { ?> 
                                <tr>
                                    <td><?php echo $i++;?></td>
                                    <td><?php echo $item->description; ?></td>
                                    <td><?php echo $item->type; ?></td>
                                    <td style="padding-right: 5px;">
                                        <a href="<?php echo base_url('Checklist/description_edit/'.$item->checklist_id);?>"><i class="fa fa-edit"></i></a>
                                        <a href="<?php echo base_url('Checklist/checklist_delete/'.$item->checklist_id);?>" onclick="return confirm('Are you sure you want to delete the check list item?');"><i class="fa fa-trash"></i></a></td>
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
            $('#checklist_items_tbl').dataTable();
        });
    </script>