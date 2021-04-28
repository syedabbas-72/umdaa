<div class="page-bar">
    <div class="page-title-breadcrumb">
        <div class=" pull-left">
            <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
        </div>
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">UMDAA</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>

            <li class="active">Consent Form Mapping</li>
        </ol>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="clinic_doctor_list" class="table table-bordered customTable ">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>TITLE</th>
                                <th>Before</th>
                                <th>After</th>
                                <th>During</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody> 
                            <?php $i=1; foreach ($consentform_list as $value) {
                                $before = 0;$after = 0;$during = 0;
                                $before = $this->db->query("select * from checklist_consent_form where category='Before' and patient_consent_form_id='".$value->consent_form_id."'")->num_rows();
                                $after = $this->db->query("select * from checklist_consent_form where category='after' and patient_consent_form_id='".$value->consent_form_id."'")->num_rows();
                                $during = $this->db->query("select * from checklist_consent_form where category='during' and patient_consent_form_id='".$value->consent_form_id."'")->num_rows();
                                ?> 
                                <tr>
                                    <td><?php echo $i++;?></td>
                                    <td><?php echo $value->consent_form_title; ?></td>
                                    <td><?=$before?></td>
                                    <td><?=$after?></td>
                                    <td><?=$during?></td>
                                    <td>
                                        <a href="<?=base_url('Checklist_department/Checklist_update/').$value->consent_form_id?>"><i class="fa fa-edit"></i></a>
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

    <script>
        $(document).ready(function () {
            $('#clinic_doctor_list').dataTable();

            $('.edit').on("click",function(){
                var consent_form_id = $(this).attr("data-id");
                $.post("<?=base_url('Checklist_department/getSelectedChecklist')?>",{consent_form_id:consent_form_id},function(data){
                    console.log(data)
                    $('.checklist_modal_body').html(data);
                }); 
            });

        });
    </script>