<div class="row page-header">

			<div class="col-lg-6 align-self-center ">

			  

				<ol class="breadcrumb">

					<li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>

					<li class="breadcrumb-item active"><a href="#">ROLES</a></li>					

				</ol>

			</div>

			<div class="col-lg-6 align-self-center text-right">

					<a href="<?php echo base_url('Admin/add_role');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> ADD ROLE</a>

				</div>

		</div>







<section class="main-content">

    <div class="row">

        <div class="col-md-12">

            <div class="card">

                <div class="card-body">

                    <table id="roleslist" class="table table-striped dt-responsive nowrap">

                        <thead>

                            <tr>

                                <th>S.No:</th>

                                <th>Role Name</th>

								<th>Role Reports To</th>

                                <th>Status</th>

                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php

                            $i = 1;

                            foreach ($roles_list as $value) {

                                $status = $value->status;

                                if ($status == 1) {

                                    $status = 'Active';

                                } else {

                                    $status = 'In-Active';

                                }

                                ?> 

                                <tr>

                                    <td><?php echo $i++; ?></td>

                                    <td><?php echo $value->role_name; ?></td>

                                    <td><?php echo $value->role_reports_to; ?></td>

                                    <td><?php echo $status; ?></td>

                                    <td>

									 <!--<a href=""><i class="fa fa-eye"></i></a> -->

                                        <a href="<?php echo base_url('Admin/update_role/' . $value->role_id); ?>"><i class="fa fa-edit"></i></a>

                                        <a href="<?php echo base_url('Admin/delete_role/' . $value->role_id); ?>" onClick="return doconfirm();"><i class="fa fa-gavel"></i></a></td>

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

        $('#roleslist').dataTable();

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







