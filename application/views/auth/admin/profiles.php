<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">UMDAA CLINICS</a>&nbsp;<i class="fa fa-angle-right"></i></li>

            <li class="active">Profiles Master</li>
        </ol>

        <div class="pull-right tPadding bcBtn">
            <a href="<?= base_url('Admin/add_profile'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> Add New Profile</a>
        </div>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="profile_list" class="table customTable">
                        <thead>
                            <tr>
                                <th style="width: 5%">S.No:</th>
                                <th style="width: 55%">Profile Name</th>
                                <th style="width: 10%" class="text-center">Status</th>
                                <th style="width: 10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($profiles_list as $value) {
                                $status = $value->status;
                                if ($status == 1) {
                                    $status = 'Active';
                                } else {
                                    $status = 'In-Active';
                                }
                                ?> 
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $value->profile_name; ?></td>
                                    <td class="text-center"><?php echo $status; ?></td>
                                    <td class="text-center actions">
                                        <span class="special"><a href="<?php echo base_url('Admin/profile_view/'.$value->profile_id); ?>"><i class="fas fa-cog deleteSmall"></i></a></span>
                                        <a href="<?php echo base_url('Admin/update_profile/'.$value->profile_id); ?>"><i class="fas fa-pencil-alt editSmall" title="Edit Profile"></i></a>
                                        <a href="<?php echo base_url('Admin/delete_profile/'.$value->profile_id); ?>" onClick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall" title="Delete"></i></a>
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

<script type="text/javascript">
    $(document).ready(function () {
        $('#profile_list').dataTable();
    });

    function doconfirm()
    {
        if (confirm("Delete selected messages ?")) {
            return true;
        } else {
            return false;
        }
    }
</script>