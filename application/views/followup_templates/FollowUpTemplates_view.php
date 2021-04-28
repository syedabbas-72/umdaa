<div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">FOLLOW-UP TEMPLATES LIST</a></li>
            <li class="breadcrumb-item active">FOLLOW-UP TEMPLATES VIEW</li> 
          </ol>
        </div>
       
    </div>
    <section class="main-content">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
              <div class="card-body">
                <table id="clinic_doctor_list" class="table dt-responsive nowrap">
                  <thead>
                      <tr>
                          <th><h4 style ="font-weight: 600;">Follow-up Title :</h4> <span style="font-size:15px;font-weight: 200;"><?php echo $FollowUpTemplates_val->FollowUpTitle;?></span></th>
                          <th><h4 style ="font-weight: 600;">DISEASE NAME :</h4> <span style="font-size:15px;font-weight: 200;"><?php echo $FollowUpTemplates_val->disease_name;?></span></th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
                <br/><br/>
                <?php $ClinicalParameters_val = $FollowUpTemplates_val->Parameter_name;
                $ClinicalParameters_array = explode(",",$ClinicalParameters_val);?>
                <table id="clinic_doctor_list" class="table  dt-responsive ">             
                  <tbody>
                    <tr>
                      <td style="width:20%;">Date</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <?php foreach($ClinicalParameters_array as $clinical_param){?>
                       <tr>
                        <td ><?php echo $clinical_param;?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                      </tr>
                  <?php } ?>
                  </tbody>

              </div>
            </div>
          </div>
        </div>
    </section>
              