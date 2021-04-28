<div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">IMMUNIZATION</a></li>
            <li class="breadcrumb-item active">LIST</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
                 <a href="<?php echo base_url('Immunization/Immunization_insert');?>" class="btn btn-primary btn-rounded box-shadow btn-icon"><i class="fa fa-plus"></i>ADD NEW VACCINE</a>  
        </div>
    </div>
    <section class="main-content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <table id="pharmacy_list" class="table table-bordered dt-responsive nowrap">
                  <thead>
                    <tr>
                      <th>AGE:</th>
                      <th>VACCINE</th>                                 
                      <th>ACTION</th>
                  </tr>
              </thead>
              <tbody>
                <?php foreach ($im_info as $key => $value) { ?>
                  <tr><td><?php if($value->from_age ==0){ echo "BIRTH"; } else if($value->to_age ==0){ echo $value->from_age." ".$value->age_unit; } else{
                    echo $value->from_age."-".$value->to_age." ".$value->age_unit;
                  } ?></td>
                    <td><?php echo $value->vaccine; ?></td>
                    <td></td>
                  </tr>
               <?php  }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>

