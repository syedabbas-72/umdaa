<div class="page-bar">
    <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-md-6">
        <div class="page-title-breadcrumb">
            <ol class="breadcrumb page-breadcrumb">
                <li><i class="fa fa-home"></i></li>
                <li class="parent-item">Umdaa Clinics <i class="fa fa-angle-right"></i></li>
                <li class="active">Expert Opinion</li>
            </ol>
        </div>
        </div>
        <div class="col-lg-6 text-right mt-2">
            <button class="btn btn-primary  box-shadow btn-icon btn-rounded" data-toggle="modal" data-target="#addWalletModal"><i class="fa fa-plus"></i> Add Wallet Amount</button>
        </div> 
    </div>
</div>

<!-- Messages View Modal -->
<div class="modal fade" id="msgsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title">Messages</h4>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 messages_body"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Messages View Modal Ends -->

<section class="main-content">
    <div class="card">
        <div class="card-body">
            <span class="page-title noBdr">Expert Opinion</span>
            <div class="row">
                <div class="col-md-12">
                    <table class="table customTable" id="expert_opinion">
                        <thead>
                            <th>#</th>
                            <th>From Doctor</th>
                            <th>To Doctor</th>
                            <th>Case Type</th>
                            <th>Consultation fee</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                        <?php
                        $i = 1;
                        foreach($ExpertOpinion as $value)
                        {
                            $transactionInfo = $this->db->query("select * from eo_wallet_history where expert_opinion_id='".$value->expert_opinion_id."' and transaction_status!='1'")->row();
                            ?>
                            <tr>
                                <td><?=$i?></td>
                                <td><span><?=getDoctorName($value->parent_doctor_id)?></span></td>
                                <td><span><?=getDoctorName($value->referred_doctor_id)?></span></td>
                                <td><label class="text-uppercase"><?=$value->case_type?></label></td>
                                <td><label>Rs.<?=number_format($transactionInfo->transaction_amount,2)?> /-</label></td>
                                <td>
                                    <a href="" data-toggle="modal" data-target="#msgsModal" class="getMsgs" id="<?=$value->expert_opinion_id?>"><i class="fa fa-eye"></i></a>
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
        $('#expert_opinion').DataTable();

        //Get Messages Related to Expert Opinion ID
        $('.getMsgs').on("click",function(){
            var id = $(this).attr("id");
            $.post("<?=base_url('ExpertOpinion/getMessages')?>",{id:id},function(data){
                $('.messages_body').html(data);
            });
        });

    });
</script>