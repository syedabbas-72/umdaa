<div class="row page-header">
			<div class="col-lg-6 align-self-center ">
			  
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active"><a href="#">Profile</a></li>					
				</ol>
			</div>
			<!-- <div class="col-lg-6 align-self-center text-right">
					<a href="<?php echo base_url('Admin/add_profile');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i>ADD PROFILE</a>
				</div> -->
		</div>



<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="profile_list" class="table table-striped dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>S.No:</th>
                                <th>Profile Name</th>
                                <th>Status</th>
                                <th>Action</th>
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
                                    <td><?php echo $status; ?></td>
                                    <td>
									 <a href="<?php echo base_url('Admin/settings_view/' . $value->profile_id); ?>"><i class="fa fa-cog"></i></a>
                                      
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
        $('#profile_list').dataTable();
    });
</script>
<script>
    function doconfirm()
    {
        if (confirm("Delete selected messages ?")) {
            return true;
        } else {
            return false;
        }
    }
</script>



