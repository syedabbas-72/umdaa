<style type="text/css">
  .radio label::after{
    top:10px !important;
  }
</style>
<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                              
                                <li class="active">Profile</li>
                            </ol>
                        </div>
                    </div>
<section class="main-content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">

      <?php if($appointment_id!="") { $this->load->view('profile/appointment_info_header'); } ?>
        <div class="row col-md-12" style="margin-top: 20px"> 
          <div class="col-md-3" id="view_casesheet">
      <div class="col-md-12">
      
      <div class="form-group ulgroup" >
        <?php $this->load->view('profile/patient_info_left_nav'); ?>
      </div>
     </div>
      
      </div>
          <div class="col-md-9" id="" class="">
      <div class="card">
        <div class="row col-md-12" style="padding: 10px" >
          <div class="col-md-12"> 
     <table class="table-bordered table">
            <thead>
              <tr>
                <th>S#</th>
               
                <th>Prescription Date</th>
                <th>treatment</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i=1;
        foreach($patient_prescription as $prescription)
        {
          $patient_prescription_drug=$this->db->query("select GROUP_CONCAT(pd.medicine_name) as medicine from patient_prescription_drug pd left join drug d on (pd.drug_id=d.drug_id) where pd.patient_prescription_id='" . $prescription->patient_prescription_id . "' ")->row();
          ?>
           <tr>
            <td><?php echo $i++; ?></td>
           
            <td class="text-left"><?php echo date("d-m-Y",strtotime($prescription->pdate)); ?></td>
            <td><?php echo $patient_prescription_drug->medicine; ?></td>
            <td class="text-left"><a target="_blank" href="<?php echo base_url('pharmacy_prescription/print/'.$prescription->patient_prescription_id); ?>"><i class="fas fa-print"></i></a></td>
            </tr>
            <?php } ?>
            
             
            </tbody>
          </table>
        </div>
      </div>
    </div>
      </div>
        </div>
      </div>
    </div>
  </div>


</section>
