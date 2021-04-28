<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class Charts extends REST_Controller1
{
    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('PHPMailer');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->load->library('SMTP');
        $this->load->library('phpqrcode/qrlib');
        $this->load->library('zip');
        $this->load->model('Generic_model');
    }

    public function getChartsTrends_get($doctor_id,$interval_period,$startDate,$endDate)
    {
        $clinics_info = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."'")->result();

        $clinic_id = $clinics_info[0]->clinic_id;

        if($interval_period != "")
        {
            if($interval_period == "Weekly")
            {
            $int = "Week";
            $period = "1 week";
            $grp = "week";
            $days = 'WEEK';  
            }
            elseif($interval_period == "Monthly")
            {
            $int = "month";
            $period = "1 month";
            $grp = "Month";
            $days = 'MONTH';  
            }
            elseif($interval_period == "Quarterly")
            {
            $int = "quarter";
            $period = "3 month";
            $grp = "Quarter";
            }
            elseif($interval_period == "Half-Yearly")
            {
            $int =  "half";
            $period = "6 month";
            $grp =  "CEIL(MONTH('".$startDate."')/6)";
            }
            elseif($interval_period == "Annually")
            {
            $int = "year";
            $period = "12 month";
            $grp = "year";
            }
     // start consultation
        
     if($int == "half")
     {
        $time_period = $this->db->query("select  CEIL(MONTH('".$startDate."')/6) as start,CEIL(MONTH('".$endDate."')/6) as end")->row();
     }else{
        $time_period = $this->db->query("select ".$grp."('".$startDate."') as start,".$grp."('".$endDate."') as end")->row();
     }

    $start_time = $time_period->start;
    $end_time = $time_period->end;

    for($a=$start_time;$a<=$end_time;$a++)
    {
        $con = $this->db->query("SELECT 
        SUM(IF(".$int." = '".$a."', total, 0)) AS '".$int."',
        SUM(total) AS total_yearly
        FROM (
        select ".$int."('".$startDate."%') as start,".$int."('".$endDate."%') as end,
        ".$int."(b.created_date_time) AS
        ".$int.",sum(bl.amount) as total from
        billing b,billing_line_items bl where
        b.billing_id=bl.billing_id and
        b.clinic_id = '".$clinic_id."' and
        b.doctor_id = '".$doctor_id."' and
        b.status NOT IN (2,3) and
        bl.item_information = 'Consultation'
        and DATE(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' 
        group by ".$int.") as sub")->result();
        // echo $this->db->last_query();
        // exit();

        $discountsINR = $this->db->query("SELECT 
        SUM(IF(".$int." = '".$a."', discount, 0)) AS 'discount',discount_unit,".$int."
        FROM (
            SELECT b.billing_id,bl.billing_line_item_id, bl.amount as total,
            bl.discount as discount,
            bl.amount-bl.discount as paid_amount,
            bl.discount_unit as discount_unit, 
            ".$int."(b.created_date_time) as ".$int." FROM billing b, billing_line_items bl where
             b.doctor_id='".$doctor_id."'  and b.billing_id=bl.billing_id and bl.discount!='0' and 
             bl.item_information='Consultation' and b.clinic_id='".$clinic_id."' and
              bl.discount_unit='INR' 
            and Date(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%'
         ) as sub")->result();

        //   echo $this->db->last_query();
        //   exit();

        $discountsPercent = $this->db->query("SELECT 
        SUM(IF(".$int." = '".$a."', discount_amount, 0)) AS 'discount',".$int."
        FROM (
            SELECT b.billing_id,bl.billing_line_item_id,bl.discount as discount,
            bl.amount as total_amount,b.created_date_time as date,
            bl.discount_unit as discount_unit,".$int."(b.created_date_time) as ".$int.",
            ((bl.amount*bl.discount)/100) as discount_amount,
            ((bl.amount)-(bl.amount*bl.discount)/100) as paid_amount
            FROM billing b,billing_line_items bl where b.doctor_id='".$doctor_id."'  and
            b.billing_id=bl.billing_id and bl.discount!='0' and bl.discount_unit='%' and
            bl.item_information='Consultation' and b.clinic_id='".$clinic_id."'
            and Date(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%') as sub")
            ->result();
    // echo $this->db->last_query();
    //       exit();

     $counts[] = count($con);

     if(count($con)>0)
     {
        foreach($con as $value)
        {
            // $pieconAmount=0;
                $discountsAmountt = $discountsPercent[0]->discount; 
                $discountsAmounttINR = $discountsINR[0]->discount; 
               // $totalDiscount += $discountsPercent[0]->discount+ $discountsINR[0]->discount;
                $conAmount = $value->$int-$discountsAmountt-$discountsAmounttINR;
                $pieconAmount += $value->$int-$discountsAmountt-$discountsAmounttINR;
                $json['Analytics'][0]['trends'][0]['data'][] = (int)$conAmount;
                $json['Analytics'][0]['trends'][0]['label']= "Consultation";

                $json['AnalyticsTrends'][0]['amount'][] = (int)$conAmount;
                $json['AnalyticsTrends'][0]['label']= "Consultation";

        }
     }
    }

     // End consultation
  
            // Start Procedure
            for($a=$start_time;$a<=$end_time;$a++)
            {
                $pro = $this->db->query("SELECT 
                        SUM(IF(".$int." = '".$a."', total, 0)) AS '".$int."',
                        SUM(total) AS total_yearly
                        FROM (
                        select ".$int."('".$startDate."%') as start,".$int."('".$endDate."%') as end,
                        ".$int."(b.created_date_time) AS
                        ".$int.",sum(bl.amount) as total from
                        billing b,billing_line_items bl where
                        b.billing_id=bl.billing_id and
                        b.clinic_id = '".$clinic_id."' and
                        b.doctor_id = '".$doctor_id."' and
                        b.status NOT IN (2,3) and
                        b.billing_type = 'Procedure'
                        and DATE(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' 
                        group by ".$int.") as sub")->result();
                // echo $this->db->last_query();
                // exit();
                $discountsProPercent = $this->db->query("SELECT 
                SUM(IF(".$int." = '".$a."', discount_amount, 0)) AS 'discount',".$int."
                FROM (
                    SELECT b.billing_id,bl.billing_line_item_id,bl.discount as discount,
                    bl.amount as total_amount,b.created_date_time as date,
                    bl.discount_unit as discount_unit,".$int."(b.created_date_time) as ".$int.",
                    ((bl.amount*bl.discount)/100) as discount_amount,
                    ((bl.amount)-(bl.amount*bl.discount)/100) as paid_amount
                    FROM billing b,billing_line_items bl where b.doctor_id='".$doctor_id."'  and
                    b.billing_id=bl.billing_id and bl.discount!='0' and bl.discount_unit='%' and
                    b.billing_type='Procedure' and b.clinic_id='".$clinic_id."'
                    and Date(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%') as sub")
                    ->result();

                    // echo $this->db->last_query();
                    // exit();

            $discountsProINR = $this->db->query("SELECT 
            SUM(IF(".$int." = '".$a."', discount, 0)) AS 'discount',discount_unit,".$int."
            FROM (
                SELECT b.billing_id,bl.billing_line_item_id, bl.amount as total,
                bl.discount as discount,
                bl.amount-bl.discount as paid_amount,
                bl.discount_unit as discount_unit, 
                ".$int."(b.created_date_time) as ".$int." FROM billing b, billing_line_items bl where
                    b.doctor_id='".$doctor_id."'  and b.billing_id=bl.billing_id and bl.discount!='0' and 
                    b.billing_type='Procedure' and b.clinic_id='".$clinic_id."' and
                    bl.discount_unit='INR' 
                and Date(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%'
                ) as sub")->result();

             $counts[] = count($pro);
        
             if(count($pro)>0)
             {
                foreach($pro as $value)
                {
                $discountProAmount =  $discountsProPercent[0]->discount;
                $discountsAmounttINR = $discountsProINR[0]->discount;
                        // $proChartAmount += $value->amount;
                $amount = $value->$int - $discountProAmount - $discountsAmounttINR;
                $pieproAmount += $value->$int - $discountProAmount - $discountsAmounttINR;
      
                $json['Analytics'][0]['trends'][1]['data'][] = (int)$amount;
                $json['Analytics'][0]['trends'][1]['label']= "Procedure";

                $json['AnalyticsTrends'][1]['amount'][] = (int)$amount;
                $json['AnalyticsTrends'][1]['label']= "Procedure";
                }
                }
             }
            // }          

            // End Procedure

            // Start Lab
            for($a=$start_time;$a<=$end_time;$a++)
            {
                // $lab = $this->db->query("SELECT 
                //         SUM(IF(".$int." = '".$a."', total, 0)) AS '".$int."',
                //         SUM(total) AS total_yearly
                //         FROM (
                //         select ".$int."('".$startDate."%') as start,".$int."('".$endDate."%') as end,
                //         ".$int."(b.created_date_time) AS
                //         ".$int.",sum(bl.amount) as total from
                //         billing b,billing_line_items bl where
                //         b.billing_id=bl.billing_id and
                //         b.clinic_id = '".$clinic_id."' and
                //         b.doctor_id = '".$doctor_id."' and
                //         b.status NOT IN (2,3) and
                //         b.billing_type = 'Lab'
                //         and DATE(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' 
                //         group by ".$int.") as sub")->result();
                $lab = $this->db->query("SELECT 
                SUM(IF(".$int." = '".$a."', total, 0)) AS '".$int."',
                SUM(total) AS total_yearly
                FROM (
                    select ".$int."('".$startDate."%') as start,".$int."('".$endDate."%') as end,
                    ".$int."(b.created_date_time) AS ".$int.",sum(bi.invoice_amount) as total 
                     from billing b,billing_invoice bi where b.billing_id=bi.billing_id 
                     and b.clinic_id ='".$clinic_id."' and b.doctor_id = '".$doctor_id."' and 
                     b.status NOT IN (2,3) 
                     and b.billing_type = 'Lab' and DATE(b.created_date_time) 
                    BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as ".$int."")->result();
   
                    // echo $this->db->last_query();
                    // exit();
             $counts[] = count($lab);
        
             if(count($lab)>0)
             {
                foreach($lab as $value)
                {
                        $amount = $value->$int;
                        $pielabAmount +=$value->$int;
                    // }
                $json['Analytics'][0]['trends'][2]['data'][] = (int)$amount;
                $json['Analytics'][0]['trends'][2]['label']= "Lab";

                $json['AnalyticsTrends'][2]['amount'][] = (int)$amount;
                $json['AnalyticsTrends'][2]['label']= "Lab";

                }
             }
            }          

            // End Lab

            // Start Lost Revenue
            $clinicDocInfo = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."'")->row();

            for($a=$start_time;$a<=$end_time;$a++)
            {
                $foLost = $this->db->query("SELECT 
                SUM(IF(".$int." = '".$a."', count, 0)) AS '".$int."'
                FROM (
                select ".$int."('".$startDate."%') as start,".$int."('".$endDate."%') as end,
                ".$int."(appointment_date) AS ".$int.",
                count(appointment_id) as count
                from appointments where
                clinic_id = '".$clinic_id."' and
                doctor_id = '".$doctor_id."' and
                payment_status='0' and DATE(appointment_date) 
                BETWEEN '".$startDate."%' and '".$endDate."%' 
                group by ".$int.") as sub")->result();
                // echo $this->db->last_query();
                // exit();
        
             $counts[] = count($foLost);
        
             if(count($foLost)>0)
             {
                foreach($foLost as $value)
                {
                    // $json['Analytics'][0]['trends'][3]['data'][] = $value->count*$clinicDocInfo->consulting_fee;
                    // $json['Analytics'][0]['trends'][3]['label'] = "Lost Revenue";

                    $lostRevenueChart = $value->$int*$clinicDocInfo->consulting_fee; 
                    $pielrAmount += $value->$int*$clinicDocInfo->consulting_fee; 

                $json['Analytics'][0]['trends'][3]['data'][] = (int)$lostRevenueChart;
                $json['Analytics'][0]['trends'][3]['label']=  "Lost Revenue";

                $json['AnalyticsTrends'][3]['amount'][] = (int)$lostRevenueChart;
                $json['AnalyticsTrends'][3]['label']= "Lost Revenue";


                }
             }
            }   
        
            // End Lost Revenue

            // Start TeleConsultation
            $clinicDocInfo = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."' and primary_clinic='1'")->result();
            for($a=$start_time;$a<=$end_time;$a++)
            {
                $tele = $this->db->query("SELECT 
                SUM(IF(".$int." = '".$a."', total, 0)) AS '".$int."'
                FROM (
                    select ".$int."('".$startDate."%') as start,".$int."('".$endDate."%') as end,
                    ".$int."(ap.appointment_date) AS ".$int.",sum(bl.amount) as total,
                    ap.appointment_id,b.billing_id,bl.billing_line_item_id
                    from billing b,appointments ap,billing_line_items bl where
                    ap.slot_type = 'video call' and 
                    ap.appointment_id=b.appointment_id and bl.billing_id=b.billing_id and
                    ap.doctor_id ='".$doctor_id."' and bl.item_information='Consultation' and 
                    ap.payment_status='1' and DATE(ap.created_date_time)
                    BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")->result();
                // echo $this->db->last_query();
                // exit();
        
             $counts[] = count($tele);
        
             if(count($tele)>0)
             {
                foreach($tele as $value)
                {
                    // $json['Analytics'][0]['trends'][3]['data'][] = $value->count*$clinicDocInfo->consulting_fee;
                    // $json['Analytics'][0]['trends'][3]['label'] = "Lost Revenue";

                $teleAmount = $value->$int; 
                $telePieAmount += $value->$int; 
                    // $teleAmount = $value->$int*$clinicDocInfo[0]->online_consulting_fee; 
                    // $telePieAmount += $value->$int*$clinicDocInfo[0]->online_consulting_fee; 

                $json['Analytics'][0]['trends'][4]['data'][] = (int)$teleAmount;
                $json['Analytics'][0]['trends'][4]['label']=  "Tele Consultation";

                $json['AnalyticsTrends'][4]['amount'][] = (int)$teleAmount;
                $json['AnalyticsTrends'][4]['label']= "Tele Consultation";


                }
             }
            }   
        
            // End Tele Consultation

      

            // Start Discounts
            for($a=$start_time;$a<=$end_time;$a++)
            {
                $discountsConINR = $this->db->query("SELECT 
                SUM(IF(".$int." = '".$a."', discount, 0)) AS 'discount',discount_unit,".$int."
                FROM (
                    SELECT b.billing_id,bl.billing_line_item_id, bl.amount as total,
                    bl.discount as discount,
                    bl.amount-bl.discount as paid_amount,
                    bl.discount_unit as discount_unit, 
                    ".$int."(b.created_date_time) as ".$int." FROM billing b, billing_line_items bl where
                     b.doctor_id='".$doctor_id."'  and b.billing_id=bl.billing_id and bl.discount!='0' and 
                     bl.item_information='Consultation' and b.clinic_id='".$clinic_id."' and
                      bl.discount_unit='INR' 
                    and Date(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%'
                 ) as sub")->result();

                //   echo $this->db->last_query();
                //   exit();

                $discountsConPercent = $this->db->query("SELECT 
                SUM(IF(".$int." = '".$a."', discount_amount, 0)) AS 'discount',".$int."
                FROM (
                    SELECT b.billing_id,bl.billing_line_item_id,bl.discount as discount,
                    bl.amount as total_amount,b.created_date_time as date,
                    bl.discount_unit as discount_unit,".$int."(b.created_date_time) as ".$int.",
                    ((bl.amount*bl.discount)/100) as discount_amount,
                    ((bl.amount)-(bl.amount*bl.discount)/100) as paid_amount
                    FROM billing b,billing_line_items bl where b.doctor_id='".$doctor_id."'  and
                    b.billing_id=bl.billing_id and bl.discount!='0' and bl.discount_unit='%' and
                    bl.item_information='Consultation' and b.clinic_id='".$clinic_id."'
                    and Date(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%') as sub")
                    ->result();

                $discountsProPercent = $this->db->query("SELECT 
                SUM(IF(".$int." = '".$a."', discount_amount, 0)) AS 'discount',".$int."
                FROM (
                    SELECT b.billing_id,bl.billing_line_item_id,bl.discount as discount,
                    bl.amount as total_amount,b.created_date_time as date,
                    bl.discount_unit as discount_unit,".$int."(b.created_date_time) as ".$int.",
                    ((bl.amount*bl.discount)/100) as discount_amount,
                    ((bl.amount)-(bl.amount*bl.discount)/100) as paid_amount
                    FROM billing b,billing_line_items bl where b.doctor_id='".$doctor_id."'  and
                    b.billing_id=bl.billing_id and bl.discount!='0' and bl.discount_unit='%' and
                    b.billing_type='Procedure' and b.clinic_id='".$clinic_id."'
                    and Date(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%') as sub")
                    ->result();

                    // echo $this->db->last_query();
                    // exit();

            $discountsProINR = $this->db->query("SELECT 
            SUM(IF(".$int." = '".$a."', discount, 0)) AS 'discount',discount_unit,".$int."
            FROM (
                SELECT b.billing_id,bl.billing_line_item_id, bl.amount as total,
                bl.discount as discount,
                bl.amount-bl.discount as paid_amount,
                bl.discount_unit as discount_unit, 
                ".$int."(b.created_date_time) as ".$int." FROM billing b, billing_line_items bl where
                    b.doctor_id='".$doctor_id."'  and b.billing_id=bl.billing_id and bl.discount!='0' and 
                    b.billing_type='Procedure' and b.clinic_id='".$clinic_id."' and
                    bl.discount_unit='INR' 
                and Date(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%'
                ) as sub")->result();


                // $discountsLabPercent = $this->db->query("SELECT 
                // SUM(IF(".$int." = '".$a."', discount_amount, 0)) AS 'discount',".$int."
                // FROM (
                //     SELECT b.billing_id,bl.billing_line_item_id,bl.discount as discount,
                //     bl.amount as total_amount,b.created_date_time as date,
                //     bl.discount_unit as discount_unit,".$int."(b.created_date_time) as ".$int.",
                //     ((bl.amount*bl.discount)/100) as discount_amount,
                //     ((bl.amount)-(bl.amount*bl.discount)/100) as paid_amount
                //     FROM billing b,billing_line_items bl where b.doctor_id='".$doctor_id."'  and
                //     b.billing_id=bl.billing_id and bl.discount!='0' and bl.discount_unit='%' and
                //     b.billing_type='Procedure' and b.clinic_id='".$clinic_id."'
                //     and Date(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%') as sub")
                //     ->result();

                    $discountsLabPercent = $this->db->query("SELECT 
                    SUM(IF(".$int." = '".$a."', total_discount_amount, 0)) AS 'discount',".$int."
                    FROM (
                        SELECT b.billing_id,b.discount as discount,
                        b.total_amount as total_amount,b.created_date_time as date,
                        b.discount_unit as discount_unit,".$int."(b.created_date_time) as ".$int.",
                        ((b.total_amount*b.discount)/100) as discount_amount,sum((b.total_amount*b.discount)/100) as total_discount_amount,
                        ((b.total_amount)-(b.total_amount*b.discount)/100) as paid_amount
                        FROM billing b where b.doctor_id='".$doctor_id."'  and
                        b.discount!='0' and b.discount_unit='%' and
                        b.billing_type='Lab' and b.clinic_id='".$clinic_id."'
                        and Date(b.created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")
                        ->result();

                     

                $discountsPharmacyPercent = $this->db->query("select SUM(IF(".$int." = '".$a."', final_total, 0)) 
                AS 'discount',".$int." FROM (select ".$int."(b.created_date_time) as ".$int.",sum(bl.total_amount-bl.amount) 
                as final_total from billing b,billing_line_items bl where b.billing_id=bl.billing_id 
                and b.doctor_id='".$doctor_id."' and b.clinic_id='".$clinic_id."' and b.billing_type='Pharmacy' and Date(b.created_date_time) 
                BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")
                    ->result();
            
                    // echo $this->db->last_query();
                    // exit();

                if(count($discountsConPercent)>0)
                {

                    $discountsAmountt = $discountsConPercent[0]->discount; 
                    $discountsAmounttINR = $discountsConINR[0]->discount;
                    $discountProAmount =  $discountsProPercent[0]->discount;
                    $discountsProAmounttINR = $discountsProINR[0]->discount;
                    $labDiscounts = $discountsLabPercent[0]->discount;
                    $pharmacyDiscounts  = $discountsPharmacyPercent[0]->discount;
                    // $totalDiscount += $discountsConPercent[0]->discount+ $discountsConINR[0]->discount+
                    // $discountsProPercent[0]->discount+$discountsProINR[0]->discount;
                    $totalDiscount += $discountsConPercent[0]->discount+ $discountsConINR[0]->discount+ 
                    $discountsProPercent[0]->discount+$discountsProINR[0]->discount+$labDiscounts+$pharmacyDiscounts;
                    // $totalDiscount += $pharmacyDiscounts;

                    $json['Analytics'][0]['trends'][5]['data'][] = (int)$discountsAmountt+
                    (int)$discountsAmounttINR+ (int)$discountProAmount+(int)$discountsProAmounttINR+$labDiscounts;
                    $json['Analytics'][0]['trends'][5]['label']=  "Discount";
    
                    $json['AnalyticsTrends'][5]['amount'][] = (int)$discountsAmountt+(int)$discountsAmounttINR+
                    (int)$discountProAmount+(int)$discountsProAmounttINR+$labDiscounts;
                    $json['AnalyticsTrends'][5]['label']= "Discount";
                    $json['AnalyticsTrends'][5]['fill']= "false";

                }    
            }
            // End Discounts

                  // Start Pharmacy
                  for($a=$start_time;$a<=$end_time;$a++)
                  {
                  $pharmacy = $this->db->query("select SUM(IF(".$int." = '".$a."', total_amount, 0))
                  AS 'amount'
                  FROM (SELECT b.created_date_time,b.billing_id,bl.billing_line_item_id,
                  ".$int."(b.created_date_time) AS ".$int.",bl.item_information,bl.amount,sum(amount) as total_amount FROM billing
                  b,billing_line_items bl WHERE
                  b.billing_id=bl.billing_id and b.billing_type='Pharmacy' and b.doctor_id='".$doctor_id."'
                  and b.clinic_id='".$clinic_id."' and Date(b.created_date_time)
                  BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")->result();
                  // echo $this->db->last_query();
                  // exit();
                          $counts[] = count($pharmacy);
                      
                          if(count($pharmacy)>0)
                          {
                              foreach($pharmacy as $value)
                              {
                              $pharmacy_count = $value->amount;
                              $pharmacy_total_count += $value->amount;
                              
                              $json['Analytics'][0]['trends'][6]['data'][] = (int)$pharmacy_count;
                              $json['Analytics'][0]['trends'][6]['label']= "Pharmacy Revenue";
      
                              $json['AnalyticsTrends'][6]['amount'][] = (int)$pharmacy_count;
                              $json['AnalyticsTrends'][6]['label']= "Pharmacy Revenue";
                              $json['AnalyticsTrends'][6]['fill'] = "false";
                              }
                          }
                      } 
                    // End Pharmacy

            $counts[] = count($foLost);
            //Start Axis
            for($d=$start_time;$d<=$end_time;$d++)
            {
                // $xvalues[] = $int." ".$counts;
                $xvalues[] = $int." ".$d;
            }

            $json['AnalyticsPie'][0]['label'][]= "Consultation";
            $json['AnalyticsPie'][0]['data'][]= (int)$pieconAmount;
            $json['AnalyticsPie'][0]['label'][]= "Procedure";
            $json['AnalyticsPie'][0]['data'][]= (int)$pieproAmount;
            $json['AnalyticsPie'][0]['label'][]= "Lab";
            $json['AnalyticsPie'][0]['data'][]= (int)$pielabAmount;
            $json['AnalyticsPie'][0]['label'][]= "Lost Revenue";
            $json['AnalyticsPie'][0]['data'][]= (int)$pielrAmount;
            $json['AnalyticsPie'][0]['label'][]= "Tele Consultation";
            $json['AnalyticsPie'][0]['data'][]= (int)$telePieAmount;
            $json['AnalyticsPie'][0]['label'][]= "Discount";
            $json['AnalyticsPie'][0]['data'][]= (int)$totalDiscount;
            $json['AnalyticsPie'][0]['label'][]= "Pharmacy Revenue";
            $json['AnalyticsPie'][0]['data'][]= (int)$pharmacy_total_count;

            
            $json['AnalyticsTable'][0]['data'][0]['name']= "Consultation";
            $json['AnalyticsTable'][0]['data'][0]['no']= "1";
            $json['AnalyticsTable'][0]['data'][0]['amount'][] = (int)($pieconAmount);

            $json['AnalyticsTable'][0]['data'][1]['name']= "Procedure";
            $json['AnalyticsTable'][0]['data'][1]['no']= "2";
            $json['AnalyticsTable'][0]['data'][1]['amount']= (int)$pieproAmount;

            $json['AnalyticsTable'][0]['data'][2]['name']= "Lab";
            $json['AnalyticsTable'][0]['data'][2]['no']= "3";
            $json['AnalyticsTable'][0]['data'][2]['amount']=(int)$pielabAmount; 

            $json['AnalyticsTable'][0]['data'][3]['name']= "Pharmacy";
            $json['AnalyticsTable'][0]['data'][3]['no']= "4";
            $json['AnalyticsTable'][0]['data'][3]['amount']= (int)$pharmacy_total_count;

            $json['AnalyticsTable'][0]['data'][4]['name']= "Tele Consultation";
            $json['AnalyticsTable'][0]['data'][4]['no']= "5";
            $json['AnalyticsTable'][0]['data'][4]['amount']= (int)$telePieAmount;

            $json['AnalyticsTable'][0]['data'][5]['no']= "6";
            $json['AnalyticsTable'][0]['data'][5]['amount']= (int)($pieconAmount)+(int)$pieproAmount+(int)$pielabAmount+
            (int)$pharmacy_total_count+(int)$telePieAmount;
            $json['AnalyticsTable'][0]['data'][5]['name']= "Total Revenue";

            $json['AnalyticsTable'][0]['data'][6]['no']= "7";
            $json['AnalyticsTable'][0]['data'][6]['amount']=  (int)$totalDiscount;
            $json['AnalyticsTable'][0]['data'][6]['name']= "Discounts";

            $json['AnalyticsTable'][0]['data'][7]['no']= "8";
            $json['AnalyticsTable'][0]['data'][7]['amount']= (int)$pielrAmount;
            $json['AnalyticsTable'][0]['data'][7]['name']="Lost Revenue";


            //End Axis
            $this->response(array('code'=>'200','message'=>$data,'result'=>$json,'x-axis'=>($xvalues)));
        }
    }
    

    public function getCharts_get($doctor_id,$interval_period,$startDate,$endDate)
    {
        $clinics_info = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."'")->result();

        $clinic_id = $clinics_info[0]->clinic_id;

        if($interval_period != "")
        {
            if($interval_period == "Weekly")
            {
            $int = "Week";
            $period = "1 week";
            $grp = "week";  
            }
            elseif($interval_period == "Monthly")
            {
            $int = "Month";
            $period = "1 month";
            $grp = "month";
            }
            elseif($interval_period == "Quarterly")
            {
            $int = "Quarter";
            $period = "3 month";
            $grp = "quarter";
            }
            elseif($interval_period == "Half-Yearly")
            {
            $int = "Half Year";
            $period = "6 month";
            $grp = "half";
            }
            elseif($interval_period == "Annually")
            {
            $int = "Year";
            $period = "12 month";
            $grp = "year";
            }

                    
            if($int == "half")
            {
                $time_period = $this->db->query("select  CEIL(MONTH('".$startDate."')/6) as start,CEIL(MONTH('".$endDate."')/6) as end")->row();
            }else{
                $time_period = $this->db->query("select ".$grp."('".$startDate."') as start,".$grp."('".$endDate."') as end")->row();
            }

            $start_time = $time_period->start;
            $end_time = $time_period->end;

        // Start WOM
        for($a=$start_time;$a<=$end_time;$a++)
        {
            $wom = $this->db->query("select SUM(IF(".$int." = '".$a."', patients_no, 0)) AS 'patients', 
            SUM(patients_no) AS total_yearly 
            FROM ( SELECT ".$int."('".$startDate."%') as start,
            ".$int."('".$endDate."%') as end,count(*) as patients_no,
            ".$int."(created_date_time) AS ".$int." FROM appointments where
            doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."' and 
            referred_by_type='wom' and DATE(created_date_time) 
            BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")->result();
            // echo $this->db->last_query();
            // exit();
                $counts[] = count($wom);
            
                if(count($wom)>0)
                {
                    foreach($wom as $value)
                    {
                    $patients_count = $value->patients;
                    $patients_total_count += $value->patients;
        
                    $json['Analytics'][0]['trends'][0]['data'][] = (int)$patients_count;
                    $json['Analytics'][0]['trends'][0]['label']= "Word Of Mouth";

                    $json['AnalyticsTrends'][0]['amount'][] = (int)$patients_count;
                    $json['AnalyticsTrends'][0]['label']= "Word Of Mouth";
                    }
                }
            } 
            // End WOM

            // Start Online
            for($a=$start_time;$a<=$end_time;$a++)
            {
                $online = $this->db->query("select SUM(IF(".$int." = '".$a."', patients_no, 0)) AS 'patients', 
                SUM(patients_no) AS total_yearly 
                FROM ( SELECT ".$int."('".$startDate."%') as start,
                ".$int."('".$endDate."%') as end,count(*) as patients_no,
                ".$int."(created_date_time) AS ".$int." FROM appointments where
                doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."' and 
                referred_by_type='online' and DATE(created_date_time) 
                BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")->result();
                // echo $this->db->last_query();
                // exit();
                    $counts[] = count($online);
                
                    if(count($online)>0)
                    {
                        foreach($online as $value)
                        {
                        $patients_online_count = $value->patients;
                        $patients_online_total_count += $value->patients;
            
                        $json['Analytics'][0]['trends'][1]['data'][] = (int)$patients_online_count;
                        $json['Analytics'][0]['trends'][1]['label']= "Online";
    
                        $json['AnalyticsTrends'][1]['amount'][] = (int)$patients_online_count;
                        $json['AnalyticsTrends'][1]['label']= "Online";
                        }
                    }
                } 
            // End Online

            // Start Referral Doctors
            for($a=$start_time;$a<=$end_time;$a++)
            {
                $referral = $this->db->query("select SUM(IF(".$int." = '".$a."', patients_no, 0)) AS 'patients', 
                SUM(patients_no) AS total_yearly 
                FROM ( SELECT ".$int."('".$startDate."%') as start,
                ".$int."('".$endDate."%') as end,count(*) as patients_no,
                ".$int."(created_date_time) AS ".$int." FROM appointments where
                doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."' and 
                referred_by_type='doctor' and DATE(created_date_time) 
                BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")->result();
                // echo $this->db->last_query();
                // exit();
                    $counts[] = count($referral);
                
                    if(count($referral)>0)
                    {
                        foreach($referral as $value)
                        {
                        $patients_referal_count = $value->patients;
                        $patients_referal_total_count += $value->patients;
            
                        $json['Analytics'][0]['trends'][2]['data'][] = (int)$patients_referal_count;
                        $json['Analytics'][0]['trends'][2]['label']= "Referral Doctors";
    
                        $json['AnalyticsTrends'][2]['amount'][] = (int)$patients_referal_count;
                        $json['AnalyticsTrends'][2]['label']= "Referral Doctors";
                        }
                    }
                } 
            // End Referral Doctors

            // Start Location Wise

            // End Location Wise
    }
    
            for($d=$start_time;$d<=$end_time;$d++)
            {
                // $xvalues[] = $int." ".$counts;
                $xvalues[] = $int." ".$d;
            }

            $json['AnalyticsPie'][0]['label'][]= "Word Of Mouth";
            $json['AnalyticsPie'][0]['data'][]= (int)$patients_total_count;
            $json['AnalyticsPie'][0]['label'][]= "Online";
            $json['AnalyticsPie'][0]['data'][]= (int)$patients_online_total_count;
            $json['AnalyticsPie'][0]['label'][]= "Referral Doctor";
            $json['AnalyticsPie'][0]['data'][]= (int)$patients_referal_total_count;

           
            $json['AnalyticsTable'][0]['data'][0]['name']=  "Word Of Mouth";
            $json['AnalyticsTable'][0]['data'][0]['no']= "1";
            $json['AnalyticsTable'][0]['data'][0]['amount'][] = (int)($patients_total_count);
            $json['AnalyticsTable'][0]['data'][1]['name']=  "Online";
            $json['AnalyticsTable'][0]['data'][1]['no']= "2";
            $json['AnalyticsTable'][0]['data'][1]['amount'][] = (int)($patients_online_total_count);
            $json['AnalyticsTable'][0]['data'][2]['name']=  "Referral Doctors";
            $json['AnalyticsTable'][0]['data'][2]['no']= "3";
            $json['AnalyticsTable'][0]['data'][2]['amount'][] = (int)($patients_referal_total_count);
 
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $json, 'requestname' => $method,'x-axis'=>$xvalues));  
        }

        public function locationWise_get($doctor_id,$interval_period,$startDate,$endDate)
        {
            extract($_POST);
            $clinics_info = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."'")->result();

            $clinic_id = $clinics_info[0]->clinic_id;
                  $dateCond = " and (p.created_date_time BETWEEN '".$startDate."%' AND '".$endDate."%')";
                  $dCond = " (created_date_time BETWEEN '".$startDate."%' AND '".$endDate."%')";

                    $doctors = $this->db->select("first_name,last_name")->from("doctors")->where("doctor_id='".$doctor_id."' ")->get()->row();
                    $ptCount = $this->db->query("select * from patients where ".$dCond." ")->num_rows();
                    $patients = $this->db->query("select count(location) as count,cdp.doctor_id,p.location from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and cdp.doctor_id='".$doctor_id."' and p.location!='' ".$dateCond." group by cdp.doctor_id,p.location order by count DESC LIMIT 5")->result();
                    $others = $this->db->query("select sum(count) as sum from (select count(location) as count,cdp.doctor_id,p.location from clinic_doctor_patient cdp,patients p where p.patient_id=cdp.patient_id and cdp.clinic_id='".$clinic_id."' and cdp.doctor_id='".$doctor_id."' and p.location!='' ".$dateCond." group by cdp.doctor_id,p.location order by count DESC LIMIT 5,".$ptCount.") as sum")->row();
                    $j=0;
                    if(count($patients)>0)
                    {
                      foreach ($patients as $pt) 
                      {
                        $json['AnalyticsPie']['label'][]=  ucwords($pt->location);
                        $json['AnalyticsPie']['data'][]= (int)$pt->count;

                        $json['AnalyticsTable'][0]['data'][$j]['name']= ucwords($pt->location);
                        $json['AnalyticsTable'][0]['data'][$j]['no']= $j+1;
                        $json['AnalyticsTable'][0]['data'][$j]['amount'][] = (int)$pt->count;
                        $j++;
                      }  
                      if(count($others)>0)
                      {
                        $json['AnalyticsPie']['label'][]=  "Others";
                        $json['AnalyticsPie']['data'][]= (int)$others->sum;

                        $json['AnalyticsTable'][0]['data'][$j]['name']=  "Others";
                        $json['AnalyticsTable'][0]['data'][$j]['no']= "6";
                        $json['AnalyticsTable'][0]['data'][$j]['amount'][] = (int)$others->sum;
                      }
                    
                    }

                    $this->response(array('code' => '200', 'message' => 'Success', 'result' => $json, 'requestname' => $method));  
        }

        
        public function custumerBehaviour_get($doctor_id,$interval_period,$startDate,$endDate)
        {
            $clinics_info = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."'")->result();

            $clinic_id = $clinics_info[0]->clinic_id;
    
            if($interval_period != "")
            {
                if($interval_period == "Weekly")
                {
                $int = "Week";
                $period = "1 week";
                $grp = "week";  
                }
                elseif($interval_period == "Monthly")
                {
                $int = "Month";
                $period = "1 month";
                $grp = "month";
                }
                elseif($interval_period == "Quarterly")
                {
                $int = "Quarter";
                $period = "3 month";
                $grp = "quarter";
                }
                elseif($interval_period == "Half-Yearly")
                {
                $int = "Half Year";
                $period = "6 month";
                $grp = "half";
                }
                elseif($interval_period == "Annually")
                {
                $int = "Year";
                $period = "12 month";
                $grp = "year";
                }
    
                        
                if($int == "half")
                {
                    $time_period = $this->db->query("select  CEIL(MONTH('".$startDate."')/6) as start,CEIL(MONTH('".$endDate."')/6) as end")->row();
                }else{
                    $time_period = $this->db->query("select ".$grp."('".$startDate."') as start,".$grp."('".$endDate."') as end")->row();
                }
    
                $start_time = $time_period->start;
                $end_time = $time_period->end;

             // Start New Appointments
                for($a=$start_time;$a<=$end_time;$a++)
                {
                $new_appointments = $this->db->query("select SUM(IF(".$int." = '".$a."', newcount, 0)) AS 'patients_new', 
                SUM(newcount) AS total_yearly 
                FROM (select ".$int."(created_date_time) AS ".$int.",
                count(*) as newcount from appointments where doctor_id='".$doctor_id."' and 
                clinic_id='".$clinic_id."' and 
                appointment_type='New' and DATE(created_date_time) 
                BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")->result();
                    // echo $this->db->last_query();
                    // exit();
                        $counts[] = count($new_appointments);
                    
                        if(count($new_appointments)>0)
                        {
                            foreach($new_appointments as $value)
                            {
                            $new_appointments_count = $value->patients_new;
                            $new_appointments_totalcount += $value->patients_new;
                
                            $json['Analytics'][0]['trends'][0]['data'][] = (int)$new_appointments_count;
                            $json['Analytics'][0]['trends'][0]['label']= "New Appointments";

                            $json['AnalyticsTrends'][0]['amount'][] = (int)$new_appointments_count;
                            $json['AnalyticsTrends'][0]['label']= "New Appointments";
                            }
                        }
                    } 
            // End New Appointments

            // Start F/u Appointments
            for($a=$start_time;$a<=$end_time;$a++)
            {
            $followup_appointments = $this->db->query("select SUM(IF(".$int." = '".$a."', newcount, 0)) AS 'patients_new', 
            SUM(newcount) AS total_yearly 
            FROM (select ".$int."(created_date_time) AS ".$int.",
            count(*) as newcount from appointments where doctor_id='".$doctor_id."' and 
            clinic_id='".$clinic_id."' and 
            appointment_type='Follow-up' and DATE(created_date_time) 
            BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")->result();
                // echo $this->db->last_query();
                // exit();
                    $counts[] = count($followup_appointments);
                
                    if(count($followup_appointments)>0)
                    {
                        foreach($followup_appointments as $value)
                        {
                        $followup_appointments_count = $value->patients_new;
                        $followup_appointments_totalcount += $value->patients_new;
            
                        $json['Analytics'][0]['trends'][1]['data'][] = (int)$followup_appointments_count;
                        $json['Analytics'][0]['trends'][1]['label']= "Follow Up Appointments";

                        $json['AnalyticsTrends'][1]['amount'][] = (int)$followup_appointments_count;
                        $json['AnalyticsTrends'][1]['label']= "Follow Up Appointments";
                        }
                    }
                } 
                // End F/u Appointments

                // Start Pharamcy Inside
                for($a=$start_time;$a<=$end_time;$a++)
                {
                $pharmacy = $this->db->query("select SUM(IF(".$int." = '".$a."', total_amount, 0))
                AS 'amount'
                FROM (SELECT b.created_date_time,b.billing_id,bl.billing_line_item_id,
                ".$int."(b.created_date_time) AS ".$int.",bl.item_information,bl.amount,sum(amount) as total_amount FROM billing
                b,billing_line_items bl WHERE
                b.billing_id=bl.billing_id and b.billing_type='Pharmacy' and b.doctor_id='".$doctor_id."'
                and b.clinic_id='".$clinic_id."' and Date(b.created_date_time)
                BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")->result();
                // echo $this->db->last_query();
                // exit();
                        $counts[] = count($pharmacy);
                    
                        if(count($pharmacy)>0)
                        {
                            foreach($pharmacy as $value)
                            {
                            $pharmacy_count = $value->amount;
                            $pharmacy_total_count += $value->amount;
                
                            $json['Analytics'][0]['trends'][2]['data'][] = (int)$pharmacy_count;
                            $json['Analytics'][0]['trends'][2]['label']= "Pharmacy Inside Revenue";
    
                            $json['AnalyticsTrends'][2]['amount'][] = (int)$pharmacy_count;
                            $json['AnalyticsTrends'][2]['label']= "Pharmacy Inside Revenue";
                            }
                        }
                    } 
                // End Pharamcy Inside

                // Start Pharmacy Outside
                for($a=$start_time;$a<=$end_time;$a++)
                {
                $pharmacyOutside = $this->db->query("select SUM(IF(".$int." = '".$a."', total_amount, 0))
                AS 'amount'
                FROM (SELECT b.created_date_time,b.billing_id,bl.billing_line_item_id,
                ".$int."(b.created_date_time) AS ".$int.",bl.item_information,bl.amount,sum(amount) as total_amount FROM billing
                b,billing_line_items bl WHERE
                b.billing_id=bl.billing_id and b.billing_type='Pharmacy' and b.doctor_id='0'
                and b.clinic_id='".$clinic_id."' and b.doctor_id='0' and Date(b.created_date_time)
                BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")->result();
                // echo $this->db->last_query();
                // exit();
                        $counts[] = count($pharmacyOutside);
                    
                        if(count($pharmacyOutside)>0)
                        {
                            foreach($pharmacyOutside as $value)
                            {
                            $pharmacyoutside_count = $value->amount;
                            $pharmacyoutside_total_count += $value->amount;
                
                            $json['Analytics'][0]['trends'][3]['data'][] = (int)$pharmacyoutside_count;
                            $json['Analytics'][0]['trends'][3]['label']= "Pharmacy Outside Revenue";
    
                            $json['AnalyticsTrends'][3]['amount'][] = (int)$pharmacyoutside_count;
                            $json['AnalyticsTrends'][3]['label']= "Pharmacy Outside Revenue";
                            }
                        }
                    } 
                // End Pharmacy Outside

                 // Start Lab Inside
            for($a=$start_time;$a<=$end_time;$a++)
            {
                $lab = $this->db->query("SELECT 
                SUM(IF(".$int." = '".$a."', total, 0)) AS '".$int."',
                SUM(total) AS total_yearly
                FROM (
                    select ".$int."('".$startDate."%') as start,".$int."('".$endDate."%') as end,
                    ".$int."(b.created_date_time) AS ".$int.",sum(bi.invoice_amount) as total 
                     from billing b,billing_invoice bi where b.billing_id=bi.billing_id 
                     and b.clinic_id ='".$clinic_id."' and b.doctor_id = '".$doctor_id."' and 
                     b.status NOT IN (2,3) 
                     and b.billing_type = 'Lab' and DATE(b.created_date_time) 
                    BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as ".$int."")->result();
   
                    // echo $this->db->last_query();
                    // exit();
             $counts[] = count($lab);
        
             if(count($lab)>0)
             {
                foreach($lab as $value)
                {
                        $amount = $value->$int;
                        $pielabAmount +=$value->$int;
                    // }
                $json['Analytics'][0]['trends'][4]['data'][] = (int)$amount;
                $json['Analytics'][0]['trends'][4]['label']= "Lab Inside Revenue";

                $json['AnalyticsTrends'][4]['amount'][] = (int)$amount;
                $json['AnalyticsTrends'][4]['label']= "Lab Inside Revenue";

                }
             }
            }          

            // End Lab Inside
            
             // Start Lab Outside
             for($a=$start_time;$a<=$end_time;$a++)
             {
                 $lab = $this->db->query("SELECT 
                 SUM(IF(".$int." = '".$a."', total, 0)) AS '".$int."',
                 SUM(total) AS total_yearly
                 FROM (
                     select ".$int."('".$startDate."%') as start,".$int."('".$endDate."%') as end,
                     ".$int."(b.created_date_time) AS ".$int.",sum(bi.invoice_amount) as total 
                      from billing b,billing_invoice bi where b.billing_id=bi.billing_id 
                      and b.clinic_id ='".$clinic_id."' and b.doctor_id='0' and
                      b.status NOT IN (2,3) 
                      and b.billing_type = 'Lab' and DATE(b.created_date_time) 
                     BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as ".$int."")->result();
    
                    //  echo $this->db->last_query();
                    //  exit();
              $counts[] = count($lab);
         
              if(count($lab)>0)
              {
                 foreach($lab as $value)
                 {
                         $amount = $value->$int;
                         $pielabOutsideAmount +=$value->$int;
                     // }
                 $json['Analytics'][0]['trends'][5]['data'][] = (int)$amount;
                 $json['Analytics'][0]['trends'][5]['label']= "Lab Outside Revenue";
 
                 $json['AnalyticsTrends'][5]['amount'][] = (int)$amount;
                 $json['AnalyticsTrends'][5]['label']= "Lab Outside Revenue";
 
                 }
              }
             }          
 
             // End Lab Outside

            //  Lab avg ticket size start 

            // total amount
   
              // Lab avg ticket size end 

 


            }    
 
            // $followup = $this->db->query("select MONTH(created_date_time) AS month,WEEK(created_date_time) AS week,CEIL(MONTH(created_date_time)/3) AS `quarter`,CEIL(MONTH(created_date_time)/6) AS `half`,CEIL(MONTH(created_date_time)/12) AS `year`,count(*) as fucount from appointments where clinic_id='".$clinic_id."' and doctor_id='".$doctor_id."' and appointment_type='Follow-up' and DATE(created_date_time) BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$grp."")->result();
       
            for($d=$start_time;$d<=$end_time;$d++)
            {
                // $xvalues[] = $int." ".$counts;
                $xvalues[] = $int." ".$d;
            }

            $json['AnalyticsPie'][0]['label'][]= "New Appointments";
            $json['AnalyticsPie'][0]['data'][]= (int)$new_appointments_totalcount;
            $json['AnalyticsPie'][0]['label'][]= "Follow Up";
            $json['AnalyticsPie'][0]['data'][]= (int)$followup_appointments_totalcount;
            $json['AnalyticsPie'][0]['label'][]= "Pharmacy Inside Revenue";
            $json['AnalyticsPie'][0]['data'][]= (int)$pharmacy_total_count;
            $json['AnalyticsPie'][0]['label'][]= "Pharmacy Outside Revenue";
            $json['AnalyticsPie'][0]['data'][]= (int)$pharmacyoutside_total_count;
            $json['AnalyticsPie'][0]['label'][]= "Lab Inside Revenue";
            $json['AnalyticsPie'][0]['data'][]= (int)$pielabAmount;
            $json['AnalyticsPie'][0]['label'][]= "Lab Outside Revenue";
            $json['AnalyticsPie'][0]['data'][]= (int)$pielabOutsideAmount;
            // $json['AnalyticsPie'][0]['label'][]= "Online";
            // $json['AnalyticsPie'][0]['data'][]= (int)$patients_online_total_count;
            // $json['AnalyticsPie'][0]['label'][]= "Referral Doctor";
            // $json['AnalyticsPie'][0]['data'][]= (int)$patients_referal_total_count;

           
            $json['AnalyticsTable'][0]['data'][0]['name']= "New Appointments";
            $json['AnalyticsTable'][0]['data'][0]['no']= "1";
            $json['AnalyticsTable'][0]['data'][0]['amount'][] = (int)($new_appointments_totalcount);
            $json['AnalyticsTable'][0]['data'][1]['name']=  "Follow Up";
            $json['AnalyticsTable'][0]['data'][1]['no']= "2";
            $json['AnalyticsTable'][0]['data'][1]['amount'][] = (int)($followup_appointments_totalcount);
            $json['AnalyticsTable'][0]['data'][2]['name']=  "Pharmacy Inside Revenue";
            $json['AnalyticsTable'][0]['data'][2]['no']= "3";
            $json['AnalyticsTable'][0]['data'][2]['amount'][] = 'Rs.'.(int)$pharmacy_total_count.'/-';
            $json['AnalyticsTable'][0]['data'][3]['name']=  "Pharmacy Outside Revenue";
            $json['AnalyticsTable'][0]['data'][3]['no']= "4";
            $json['AnalyticsTable'][0]['data'][3]['amount'][] = 'Rs.'.(int)$pharmacyoutside_total_count.'/-';
            $json['AnalyticsTable'][0]['data'][4]['name']= "Lab Inside Revenue";
            $json['AnalyticsTable'][0]['data'][4]['no']= "5";
            $json['AnalyticsTable'][0]['data'][4]['amount'][] = 'Rs.'.(int)$pielabAmount.'/-';
            $json['AnalyticsTable'][0]['data'][5]['name']= "Lab Outside Revenue";
            $json['AnalyticsTable'][0]['data'][5]['no']= "6";
            $json['AnalyticsTable'][0]['data'][5]['amount'][] = 'Rs.'.(int)$pielabOutsideAmount.'/-';
 
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $json, 'requestname' => $method,'x-axis'=>$xvalues));  
        }

        public function avgPatientSize_get($doctor_id,$interval_period,$startDate,$endDate)
        {
            $clinics_info = $this->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."'")->result();

            $clinic_id = $clinics_info[0]->clinic_id;
    
            if($interval_period != "")
            {
                if($interval_period == "Weekly")
                {
                $int = "Week";
                $period = "1 week";
                $grp = "week";
                $days = 'WEEK';  
                }
                elseif($interval_period == "Monthly")
                {
                $int = "month";
                $period = "1 month";
                $grp = "Month";
                $days = 'MONTH';  
                }
                elseif($interval_period == "Quarterly")
                {
                $int = "quarter";
                $period = "3 month";
                $grp = "Quarter";
                }
                elseif($interval_period == "Half-Yearly")
                {
                $int =  "half";
                $period = "6 month";
                $grp =  "CEIL(MONTH('".$startDate."')/6)";
                }
                elseif($interval_period == "Annually")
                {
                $int = "year";
                $period = "12 month";
                $grp = "year";
                }
            
         if($int == "half")
         {
            $time_period = $this->db->query("select  CEIL(MONTH('".$startDate."')/6) as start,CEIL(MONTH('".$endDate."')/6) as end")->row();
         }else{
            $time_period = $this->db->query("select ".$grp."('".$startDate."') as start,".$grp."('".$endDate."') as end")->row();
         }
    
        $start_time = $time_period->start;
        $end_time = $time_period->end;
    
        // Lab Avg Ticket Size
            for($a=$start_time;$a<=$end_time;$a++)
            {
               $lab_amount = $this->db->query("SELECT 
                SUM(IF(".$int." = '".$a."', total, 0)) AS '".$int."',
                SUM(total) AS total_yearly
                FROM (
                select ".$int."('".$startDate."%') as start,".$int."('".$endDate."%') as end,
                ".$int."(b.created_date_time) AS ".$int.",sum(bi.invoice_amount) as total 
                from billing b,billing_invoice bi where b.billing_id=bi.billing_id 
                and b.clinic_id ='".$clinic_id."' and b.doctor_id = '".$doctor_id."' and 
                b.status NOT IN (2,3) 
                and b.billing_type = 'Lab' and DATE(b.created_date_time) 
                BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as ".$int."")->result();

                $patient_count = $this->db->query("select SUM(IF(".$int." = '".$a."', total_no_patients, 0)) AS '".$int."',
                SUM(total_no_patients) AS total_yearly FROM (SELECT ".$int."('".$startDate."%') as start,
                ".$int."('".$endDate."%') as end, ".$int."(b.created_date_time) AS ".$int.",
                count(b.billing_id) as total_no_patients FROM billing b,billing_invoice bi 
                WHERE bi.billing_id=b.billing_id and b.doctor_id='".$doctor_id."' and b.clinic_id='".$clinic_id."' and
                 bi.payment_type != 'OSA' and b.billing_type='Lab' and Date(b.created_date_time) 
                 BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as ".$int."")->result();

                $counts[] = count($lab_amount);
         
                if(count($lab_amount)>0)
                {
                    $lab_amountt = (int)$lab_amount[0]->$int; 
                    $patient_countt = (int)$patient_count[0]->$int;
                    $amount[] = array('no' => $a,'amount' => $lab_amountt,'count' => $patient_countt,'avg' =>($lab_amountt == 0 || $patient_countt == 0)?0:(int)($lab_amountt/$patient_countt) );
                    // $count[] = array_push($count, array('count' => $patient_countt));
                    $json['AnalyticsTable'][0]['data'] = $amount;
                }
            }  

            // Lab Avg Ticket Size

            // Pharmacy Avg Ticket Size
            for($a=$start_time;$a<=$end_time;$a++)
            {
                $pharmacy_amount = $this->db->query("select SUM(IF(".$int." = '".$a."', total_amount, 0))
                AS 'amount'
                FROM (SELECT b.created_date_time,b.billing_id,bl.billing_line_item_id,
                ".$int."(b.created_date_time) AS ".$int.",bl.item_information,bl.amount,sum(amount) as total_amount FROM billing
                b,billing_line_items bl WHERE
                b.billing_id=bl.billing_id and b.billing_type='Pharmacy' and b.doctor_id='".$doctor_id."'
                and b.clinic_id='".$clinic_id."' and Date(b.created_date_time)
                BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as sub")->result();

            //    echo $this->db->last_query();
            //    exit();

        
                $patient_pharmacy_count = $this->db->query("select SUM(IF(".$int." = '".$a."', total_no_patients, 0)) AS '".$int."',
                SUM(total_no_patients) AS total_yearly FROM (SELECT ".$int."('".$startDate."%') as start,
                ".$int."('".$endDate."%') as end, ".$int."(b.created_date_time) AS ".$int.",
                 count(DISTINCT b.billing_id) as total_no_patients FROM
                  billing b,billing_line_items bl WHERE bl.billing_id=b.billing_id and 
                  b.doctor_id='".$doctor_id."' and b.clinic_id='".$clinic_id."' and b.billing_type = 'Pharmacy' and
                 Date(b.created_date_time)
                 BETWEEN '".$startDate."%' and '".$endDate."%' group by ".$int.") as ".$int."")->result();

                 
            //    echo $this->db->last_query();
            //    exit();

                $counts[] = count($pharmacy_amount);
         
                if(count($pharmacy_amount)>0)
                {
                    $pharmacy_amountt = (int)$pharmacy_amount[0]->amount; 
                    $patient_pharmacy_countt = (int)$patient_pharmacy_count[0]->$int;
                    $amountt[] = array('no' =>$a,'amount' => $pharmacy_amountt,'count' => $patient_pharmacy_countt,'avg' =>($pharmacy_amountt == 0 || $patient_pharmacy_countt == 0)?0:(int)($pharmacy_amountt/$patient_pharmacy_countt) );
                    // $count[] = array_push($count, array('count' => $patient_countt));
                    $json['AnalyticsTable'][1]['pharmacy'] = $amountt;
                }
            }  
            // Pharmacy Avg Ticket Size
            for($d=$start_time;$d<=$end_time;$d++)
            {
                // $xvalues[] = $int." ".$counts;
                $xvalues[] = $int." ".$d;
            }
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $json, 
            'requestname' => $method,'x-axis'=>$xvalues));  
        }
        // $json
      
    }

}
    
