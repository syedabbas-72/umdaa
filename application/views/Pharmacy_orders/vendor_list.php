


<section class="main-content">

    <!-- Add Modal -->
    <div class="modal fade" id="addVendor">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Add Vendor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="<?=base_url("Pharmacy_orders/addVendor")?>" class="form-horizontal ModalForm form" method="post">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="col-form-label font-weight-bold">Store Name <span>*</span></label>
                                            <input type="hidden" class="vendor_id"  name="vendor_id">
                                            <input type="text" maxlength="50" required class="form-control text-capitalize storeName" name="storeName">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="col-form-label font-weight-bold">Contact Person</label>
                                            <input type="text" maxlength="50" onkeypress="return alpha()" class="form-control text-capitalize vendorName" name="vendorName">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="col-form-label font-weight-bold">Mobile Number <span>*</span></label>
                                            <input type="text" maxlength="10" required class="form-control mobile" onkeypress="return numeric()" name="mobile">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="col-form-label font-weight-bold">Email <span>*</span></label>
                                            <input type="email" maxlength="50" required class="form-control email" name="email">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="" class="col-form-label font-weight-bold">Address</label>
                                            <input type="text" maxlength="50" class="form-control text-capitalize address" name="address">
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="col-form-label font-weight-bold">Location</label>
                                            <input type="text" maxlength="35" class="form-control text-capitalize location" name="location">
                                        </div>
                                    </div> -->
                                </div>
                                <div class="row text-center">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary" name="submit">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row col-md-12 docInfoHdr">
                        <div class="col-md-6">
                            <span class="text-left col-form-label font-weight-bold">Vendors</span>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addVendor"><i class="fa fa-plus-circle"></i> Add Vendor</button>    
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?php
                        if(isset($_GET['usuccess']))
                        {
                            ?>
                            <div class="alert alert-success" role="alert">
                                <b>Successfully Updated!</b>
                            </div>
                            <?php
                        }
                        if(isset($_GET['asuccess']))
                        {
                            ?>
                            <div class="alert alert-success" role="alert">
                                <b>Successfully Added!</b>
                            </div>
                            <?php
                        }
                        if(isset($_GET['dsuccess']))
                        {
                            ?>
                            <div class="alert alert-success" role="alert">
                                <b>Successfully Deleted!</b>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <table cellspacing="0" cellpadding="0" class="table customTable" id="vendorsTable">
                        <thead>
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:25%">Store Name</th>
                                <th style="width:15%">Contact Person</th>
                                <th style="width:15%">Mobile</th>
                                <th style="width:15%">Email</th>
                                <th style="width:15%">Address</th>
                                <th style="width:10%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($vendor_list as $value) {
                                $data_id = $value->vendor_id."*".$value->vendor_storeName."*".$value->vendor_name."*".$value->vendor_mobile."*".$value->vendor_email."*".$value->vendor_address."*".$value->vendor_location;
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><?=ucwords(strtolower($value->vendor_storeName))?></td>
                                    <td><?=ucwords(strtolower($value->vendor_name))?></td>
                                    <td><?=$value->vendor_mobile?></td>
                                    <td><?=$value->vendor_email?></td>
                                    <td><?=ucwords(strtolower($value->vendor_address))?></td>
                                    <td class="actions text-center">
                                        <a id="<?=$data_id?>" class="update" data-toggle="modal" data-target="#addVendor"><i class="fas fa-pencil-alt editSmall" title="Edit"></i></a>
                                        <a href="<?=base_url('Pharmacy_orders/deleteVendor/'.$value->vendor_id)?>"><i class="fas fa-trash-alt deleteSmall" title="Delete"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>                   


</section>  
<script>
    $(document).ready(function(){
        $('.update').on("click",function(){
            var id = $(this).attr("id");
            var splitData = id.split("*");
            $('.ModalForm').removeAttr("action");
            $('.ModalForm').attr("action","<?=base_url('Pharmacy_orders/editVendor')?>");      
            $('.vendor_id').val(splitData[0]);
            $('.storeName').val(splitData[1]);
            $('.vendorName').val(splitData[2]);
            $('.mobile').val(splitData[3]);
            $('.email').val(splitData[4]);
            $('.address').val(splitData[5]);
            $('.location').val(splitData[6]);
            $('.modal-title').html("Update Vendor");
            $('#addVendor').modal();
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#vendorsTable').DataTable();
    });
</script>
