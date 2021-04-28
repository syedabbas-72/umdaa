<style>
/* page preloader image */
.no-js #loader { display: none;  }
.js #loader { display: block; position: absolute; left: 100px; top: 0; }
.se-pre-con {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url(images/loader-64x/Preloader_2.gif) center no-repeat #fff;
}
</style>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <div class="pull-left">
            <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
        </div>
        <ol class="breadcrumb page-breadcrumb pull-right">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i></li>          
            <li class="active">Patient Excel Report</li>
        </ol>
    </div>
</div>         
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <!-- get patient demographic details -->
                <?php 

                // excel reporting
                // prepare headers
                $headers['demographics'] = array('patient_id','umr_no','gender','date_of_birth','age','location');
                
                // get vitals for headers
                $vitals = $this->db->select('vital_sign')->distinct()->get('patient_vital_sign')->result_array();
                $headers['vitals'] = array();

                foreach($vitals as $hdrRec){
                    array_push($headers['vitals'],$hdrRec['vital_sign']);
                }

                // get appointments for patient report records
                $appointments = $this->db->get('appointments')->result_array();

                if(count($appointments) > 0){ // if appointments exists

                    // initialize counter
                    $i = 0;

                    // generate report for the patient
                    foreach($appointments as $appointmentRec){
                        // extract appointment Record
                        extract($appointmentRec);

                        // get demographic details
                        $demographics = $this->db->select('patient_id,umr_no,gender,date_of_birth,age,location')->where('patient_id=',$patient_id)->get('patients')->row();

                        $report[$i]['demographics'] = (array)$demographics;

                        // get vitals
                        $vitals = $this->db->select('vital_sign,vital_result')->where('appointment_id = ',$appointment_id)->get('patient_vital_sign')->result_array();

                        // initialize an array for patientVitals
                        $patientVitals = array();
                        foreach($vitals as $vitalRec){
                            $patientVitals[$vitalRec['vital_sign']] = $vitalRec['vital_result'];
                        }

                        // get header vitals and assign vital results w.r.to the header vitals
                        for($x=0; $x<count($headers['vitals']); $x++){
                            if(array_key_exists($headers['vitals'][$x], $patientVitals)){
                                $report[$i]['vitals'][$headers['vitals'][$x]] = $patientVitals[$headers['vitals'][$x]];
                            }else{
                                $report[$i]['vitals'][$headers['vitals'][$x]] = 0;
                            }
                        }

                        $i++; // increase counter
                    }    

                }
                ?>
            </div>
        </div>
    </div>
</div>