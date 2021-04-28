<div class="page-bar">
    <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-12">
            <div class="page-title-breadcrumb">
                <ol class="breadcrumb page-breadcrumb">
                    <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a></li>
                    <li class="parent-item">Umdaa Clinics <i class="fa fa-angle-right"></i></li>
                    <li class="active">Wallet Requests</li>
                </ol>
            </div>
        </div>
    </div>  
</div>

<!-- Modals Starts Here -->
<div class="modal fade" role="dialog" id="addMoney">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Add Money</h4>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post" action="<?=base_url('Wallet/addReqMoney')?>">
                <input type="hidden" name="doctor_id" id="doctor_id">
                <input type="hidden" name="req_id" id="req_id">
                <div class="modal-body">
                    <span class="m-0 pl-1 trade_name" id="docName"></span><hr>
                    <div class="form-group">
                        <label class="mb-1">Amount</label>
                        <input type="text" class="form-control" oninput="return numeric()" id="amount" name="amount" required >
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-app" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modals Ends Here -->

<!-- Main content Goes here -->
<section class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table customTable">
                                <thead>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Requested Date</th>
                                    <th>Actions</th>
                                    <!-- <th>Actions</th> -->
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach($requestsList as $value)
                                    {
                                        $docInfo = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$value->doctor_id."'")->row();
                                        $data = "Dr. ".$docInfo->first_name." ".$docInfo->last_name."*".$docInfo->doctor_id."*".$value->amount."*".$value->wallet_amount_request_id;
                                        ?>
                                        <tr>
                                            <td><?=$i?></td>
                                            <td><p class="trade_name mb-0 p-0">Dr. <?=$docInfo->first_name." ".$docInfo->last_name?></p> <span class="formulation m-0"><?=$docInfo->qualification?> - <?=$docInfo->department_name?></span></td>
                                            <td><span class="trade_name">Rs. <?=number_format($value->amount,2)?> /-</span></td>
                                            <td><?=date("M d Y h:i A", strtotime($value->created_date_time))?></td>
                                            <td>
                                            <?php 
                                            if($value->req_status == 0)
                                            {
                                                ?>
                                                <button class="btn btn-xs btn-app addmoney_btn" data-toggle="modal" data-target="#addMoney" data-values="<?=$data?>">ADD MONEY</button>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <p class="trade_name text-success m-0 p-0">Money Added.</p>
                                                <?php
                                            }
                                            ?>
                                            </td>
                                            <!-- <td></td> -->
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
        </div>
    </div>
</section>
<!-- Main Content Ends Here -->


<!-- JS Scripts -->
<script>
    $(document).ready(function(){
        $('.table').DataTable();
        $('.addmoney_btn').on("click", function(){
            var data = $(this).attr("data-values");
            data = data.split("*");
            $('#docName').html(data[0]);
            $('#doctor_id').val(data[1]);
            $('#amount').val(data[2]);
            $('#req_id').val(data[3]);
        });
    });
</script>
<!-- JS Scripts -->
