<?php

defined('BASEPATH') or exit('No direct script access allowed');

// include "phpqrcode/qrlib.php";
if (!function_exists('is_logged_in')) {

    function is_logged_in()
    {
        $CI = &get_instance();

        $user = $CI->session->userdata('is_logged_in');

        if (!isset($user)) {
            return false;
        } else {
            return true;
        }
    }
    // -----------------------------------------------------------------
    // PaymentFunctions For PayTM
    // -----------------------------------------------------------------


    function encrypt_e($input, $ky)
    {
        $key   = html_entity_decode($ky);
        $iv = "@@@@&&&&####$$$$";
        $data = openssl_encrypt($input, "AES-128-CBC", $key, 0, $iv);
        return $data;
    }

    function decrypt_e($crypt, $ky)
    {
        $key   = html_entity_decode($ky);
        $iv = "@@@@&&&&####$$$$";
        $data = openssl_decrypt($crypt, "AES-128-CBC", $key, 0, $iv);
        return $data;
    }

    function generateSalt_e($length)
    {
        $random = "";
        srand((float) microtime() * 1000000);

        $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
        $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
        $data .= "0FGH45OP89";

        for ($i = 0; $i < $length; $i++) {
            $random .= substr($data, (rand() % (strlen($data))), 1);
        }

        return $random;
    }

    function checkString_e($value)
    {
        if ($value == 'null')
            $value = '';
        return $value;
    }

    function getChecksumFromArray($arrayList, $key, $sort = 1)
    {
        if ($sort != 0) {
            ksort($arrayList);
        }
        $str = getArray2Str($arrayList);
        $salt = generateSalt_e(4);
        $finalString = $str . "|" . $salt;
        $hash = hash("sha256", $finalString);
        $hashString = $hash . $salt;
        $checksum = encrypt_e($hashString, $key);
        return $checksum;
    }
    function getChecksumFromString($str, $key)
    {

        $salt = generateSalt_e(4);
        $finalString = $str . "|" . $salt;
        $hash = hash("sha256", $finalString);
        $hashString = $hash . $salt;
        $checksum = encrypt_e($hashString, $key);
        return $checksum;
    }

    function verifychecksum_e($arrayList, $key, $checksumvalue)
    {
        $arrayList = removeCheckSumParam($arrayList);
        ksort($arrayList);
        $str = getArray2StrForVerify($arrayList);
        $paytm_hash = decrypt_e($checksumvalue, $key);
        $salt = substr($paytm_hash, -4);

        $finalString = $str . "|" . $salt;

        $website_hash = hash("sha256", $finalString);
        $website_hash .= $salt;

        $validFlag = "FALSE";
        if ($website_hash == $paytm_hash) {
            $validFlag = "TRUE";
        } else {
            $validFlag = "FALSE";
        }
        return $validFlag;
    }

    function verifychecksum_eFromStr($str, $key, $checksumvalue)
    {
        $paytm_hash = decrypt_e($checksumvalue, $key);
        $salt = substr($paytm_hash, -4);

        $finalString = $str . "|" . $salt;

        $website_hash = hash("sha256", $finalString);
        $website_hash .= $salt;

        $validFlag = "FALSE";
        if ($website_hash == $paytm_hash) {
            $validFlag = "TRUE";
        } else {
            $validFlag = "FALSE";
        }
        return $validFlag;
    }

    function getArray2Str($arrayList)
    {
        $findme   = 'REFUND';
        $findmepipe = '|';
        $paramStr = "";
        $flag = 1;
        foreach ($arrayList as $key => $value) {
            $pos = strpos($value, $findme);
            $pospipe = strpos($value, $findmepipe);
            if ($pos !== false || $pospipe !== false) {
                continue;
            }

            if ($flag) {
                $paramStr .= checkString_e($value);
                $flag = 0;
            } else {
                $paramStr .= "|" . checkString_e($value);
            }
        }
        return $paramStr;
    }

    function getArray2StrForVerify($arrayList)
    {
        $paramStr = "";
        $flag = 1;
        foreach ($arrayList as $key => $value) {
            if ($flag) {
                $paramStr .= checkString_e($value);
                $flag = 0;
            } else {
                $paramStr .= "|" . checkString_e($value);
            }
        }
        return $paramStr;
    }

    function redirect2PG($paramList, $key)
    {
        $hashString = getchecksumFromArray($paramList);
        $checksum = encrypt_e($hashString, $key);
    }

    function removeCheckSumParam($arrayList)
    {
        if (isset($arrayList["CHECKSUMHASH"])) {
            unset($arrayList["CHECKSUMHASH"]);
        }
        return $arrayList;
    }

    function getTxnStatus($requestParamList)
    {
        return callAPI(PAYTM_STATUS_QUERY_URL, $requestParamList);
    }

    function getTxnStatusNew($requestParamList)
    {
        return callNewAPI(PAYTM_STATUS_QUERY_NEW_URL, $requestParamList);
    }

    function initiateTxnRefund($requestParamList)
    {
        $CHECKSUM = getRefundChecksumFromArray($requestParamList, PAYTM_MERCHANT_KEY, 0);
        $requestParamList["CHECKSUM"] = $CHECKSUM;
        return callAPI(PAYTM_REFUND_URL, $requestParamList);
    }

    function callAPI($apiURL, $requestParamList)
    {
        $jsonResponse = "";
        $responseParamList = array();
        $JsonData = json_encode($requestParamList);
        $postData = 'JsonData=' . urlencode($JsonData);
        $ch = curl_init($apiURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData)
            )
        );
        $jsonResponse = curl_exec($ch);
        $responseParamList = json_decode($jsonResponse, true);
        return $responseParamList;
    }

    function callNewAPI($apiURL, $requestParamList)
    {
        $jsonResponse = "";
        $responseParamList = array();
        $JsonData = json_encode($requestParamList);
        $postData = 'JsonData=' . urlencode($JsonData);
        $ch = curl_init($apiURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData)
            )
        );
        $jsonResponse = curl_exec($ch);
        $responseParamList = json_decode($jsonResponse, true);
        return $responseParamList;
    }
    function getRefundChecksumFromArray($arrayList, $key, $sort = 1)
    {
        if ($sort != 0) {
            ksort($arrayList);
        }
        $str = getRefundArray2Str($arrayList);
        $salt = generateSalt_e(4);
        $finalString = $str . "|" . $salt;
        $hash = hash("sha256", $finalString);
        $hashString = $hash . $salt;
        $checksum = encrypt_e($hashString, $key);
        return $checksum;
    }
    function getRefundArray2Str($arrayList)
    {
        $findmepipe = '|';
        $paramStr = "";
        $flag = 1;
        foreach ($arrayList as $key => $value) {
            $pospipe = strpos($value, $findmepipe);
            if ($pospipe !== false) {
                continue;
            }

            if ($flag) {
                $paramStr .= checkString_e($value);
                $flag = 0;
            } else {
                $paramStr .= "|" . checkString_e($value);
            }
        }
        return $paramStr;
    }
    function callRefundAPI($refundApiURL, $requestParamList)
    {
        $jsonResponse = "";
        $responseParamList = array();
        $JsonData = json_encode($requestParamList);
        $postData = 'JsonData=' . urlencode($JsonData);
        $ch = curl_init($apiURL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $refundApiURL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $jsonResponse = curl_exec($ch);
        $responseParamList = json_decode($jsonResponse, true);
        return $responseParamList;
    }


    // -----------------------------------------------------------------
    // PaymentFunctions For PayTM
    // -----------------------------------------------------------------

    function getvitalsbydate($date, $sign, $appointment_id)
    {
        $CI = &get_instance();
        // $res = $CI->db->query("select * from patient_vital_sign where vital_sign_recording_date_time = (select max(vital_sign_recording_date_time) From patient_vital_sign where vital_sign_recording_date_time like '".$date."%') and vital_sign='".$sign."' and appointment_id='".$appointment_id."'  order by vital_sign_recording_date_time DESC")->row();
        $res = $CI->db->query("select vital_result from patient_vital_sign where appointment_id='" . $appointment_id . "' and vital_sign='" . $sign . "' order by patient_vital_id DESC LIMIT 1")->row();
        // return $CI->db->last_query();
        if ($res) {
            return $res->vital_result;
        } else {
            return "";
        }
    }

    // get vitals based on appointmentid
    function getVitals($appointment_id)
    {
        $CI = &get_instance();
        $vitalsCheck = $CI->db->select("vital_sign, vital_result")->from("patient_vital_sign")->where("appointment_id", $appointment_id)->order_by("position", "ASC")->get()->result();
        if (count($vitalsCheck) > 0) {
            $i = 0;
            foreach ($vitalsCheck as $value) {
                $vitals[$i] = $value->vital_sign . " - " . $value->vital_result;
                $i++;
            }
            echo implode(", ", $vitals);
        } else {
            echo "N/A";
        }
    }

    // get symptoms based on appointment_id
    function getSymptoms($appointment_id)
    {

        $CI = &get_instance();
        $CI->db->select("symptom_data");
        $CI->db->from("patient_ps_line_items psl");
        $CI->db->join("patient_presenting_symptoms pps", "pps.patient_presenting_symptoms_id=psl.patient_presenting_symptoms_id");
        $CI->db->where("pps.appointment_id", $appointment_id);
        $symptomsCheck = $CI->db->get()->result();

        // $symptomsCheck = $CI->db->query("SELECT psline.symptom_data from patient_presenting_symptoms pps INNER JOIN patient_ps_line_items psline ON psline.patient_presenting_symptoms_id=pps.patient_presenting_symptoms_id WHERE pps.appointment_id='" . $appointment_id . "'")->result();
        // print_r($appointment_id);
        if (count($symptomsCheck) > 0) {
            $i = 0;
            foreach ($symptomsCheck as $value) {
                $symptoms[$i] = $value->symptom_data;
                $i++;
            }
            echo implode(", ", $symptoms);
        } else {
            echo "N/A";
        }
    }

    // get entities
    function getEntities($profile_id)
    {
        $CI = &get_instance();
        $entities = $CI->db->query("select * from profile_permissions pp, user_entities ue where pp.user_entity_id=ue.user_entity_id and pp.profile_id='" . $profile_id . "'")->result();
        // echo $CI->db->last_query();
        return $entities;
    }

    // get clinical diagnosis based on appointment_id
    function getClinicalDiagnosis($appointment_id)
    {
        // $appointment_id = 2;
        $CI = &get_instance();
        $CI->db->select("disease_name");
        $CI->db->from("patient_cd_line_items pcdl");
        $CI->db->join("patient_clinical_diagnosis pcd", "pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id");
        $CI->db->where("pcd.appointment_id", $appointment_id);
        $cdCheck = $CI->db->get()->result();

        $cdCheck = $CI->db->query("SELECT * FROM patient_clinical_diagnosis pcd INNER JOIN patient_cd_line_items pcdline ON pcd.patient_clinical_diagnosis_id=pcdline.patient_clinical_diagnosis_id WHERE pcd.appointment_id='" . $appointment_id . "'")->result();

        if (count($cdCheck) > 0) {
            $i = 0;
            foreach ($cdCheck as $value) {
                $cd[$i] = $value->disease_name;
                $i++;
            }
            echo implode(", ", $cd);
        } else {
            echo "N/A";
        }
    }

    // get investigations based on appointment_id
    function getInvestigations($appointment_id)
    {
        $CI = &get_instance();
        $CI->db->select("investigation_name");
        $CI->db->from("patient_investigation_line_items pinl");
        $CI->db->join("patient_investigation pin", "pin.patient_investigation_id=pinl.patient_investigation_id");
        $CI->db->where("pin.appointment_id", $appointment_id);
        $invCheck = $CI->db->get()->result();
        // $invChecck = $CI->db->query("SELECT piline.investigation_name FROM patient_investigation pi INNER JOIN patient_investigation_line_items piline ON pi.patient_investigation_id=piline.patient_investigation_id WHERE appointment_id='" . $appointment_id . "'")->result();

        if (count($invCheck) > 0) {
            $i = 0;
            foreach ($invCheck as $value) {
                $inv[$i] = $value->investigation_name;
                $i++;
            }
            echo implode(", ", $inv);
        } else {
            echo "N/A";
        }
    }

    // get Prescriptions based on appointment_id
    function getPrescriptions($appointment_id)
    {
        $CI = &get_instance();
        $CI->db->select("medicine_name");
        $CI->db->from("patient_prescription_drug ppd");
        $CI->db->join("patient_prescription pp", "pp.patient_prescription_id=ppd.patient_prescription_id");
        $CI->db->where("pp.appointment_id", $appointment_id);
        $drugCheck = $CI->db->get()->result();
        if (count($drugCheck) > 0) {
            $i = 0;
            foreach ($drugCheck as $value) {
                $drug[$i] = $value->medicine_name;
                $i++;
            }
            echo implode(", ", $drug);
        } else {
            echo "N/A";
        }
    }

    function getNotes($appointment_id)
    {
        $CI = &get_instance();
        $notes = $CI->db->query("SELECT note_details FROM patient_notes pn INNER JOIN patient_notes_line_items pnli ON pn.patient_notes_id=pnli.patient_notes_id WHERE pn.appointment_id='" . $appointment_id . "'")->result();
        if (count($notes) > 0) {
            $i = 0;
            foreach ($notes as $not) {
                $patient_notes[$i] = $not->note_details;
                $i++;
            }
            echo implode(",", $patient_notes);
        } else {
            echo "N/A";
        }
    }

    // get child entities
    function getchildEntities($profile_id, $entity_id)
    {
        $CI = &get_instance();
        $result = $CI->db->query("select * from profile_permissions pp, user_entities ue where pp.user_entity_id=ue.user_entity_id and pp.profile_id='" . $profile_id . "' and ue.parent_id='" . $entity_id . "'")->result();
        return $result;
    }

    //check entity is there for role in clinic
    function checkEntityStatus($clinic_id, $clinic_role_id, $entity_id)
    {
        $CI = &get_instance();
        $result = $CI->db->query("select * from clinic_role_permissions where clinic_id='" . $clinic_id . "' and clinic_role_id='" . $clinic_role_id . "' and entity_id='" . $entity_id . "'")->row();
        // return $CI->db->last_query();
        return count($result);
    }

    //check entity is there for role in clinic
    function checkEntityStatusFromMaster($clinic_role_id, $entity_id)
    {
        $CI = &get_instance();
        $result = $CI->db->query("select * from clinic_role_entities where clinic_role_id='" . $clinic_role_id . "' and entity_id='" . $entity_id . "'")->row();
        // return $CI->db->last_query();
        return count($result);
    }

    // Get Doctor Name from Doctor Id
    function getDoctorName($docID)
    {
        $CI = &get_instance();
        $docInfo = $CI->db->query("select * from doctors where doctor_id='" . $docID . "'")->row();
        return "Dr." . $docInfo->first_name . " " . $docInfo->last_name;
    }

    function getDocPackage($doc_id)
    {
        $CI = &get_instance();
        $result = $CI->db->query("select * from doctor_packages dp,packages p where p.package_id=dp.package_id and dp.doctor_id='" . $doc_id . "'")->row();
        return $result;
    }

    function doctorDetails($docId)
    {
        $CI = &get_instance();
        $docInfo = $CI->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='" . $docId . "'")->row();
        return $docInfo;
    }

    function getPackageAccess($clinic_id)
    {
        $CI = &get_instance();
        $packageInfo = $CI->db->query("select * from doctor_packages dp,packages p where p.package_id=dp.package_id and dp.clinic_id='" . $clinic_id . "'")->row();
        $featInfo = $CI->db->query("select * from package_features pf,features f where pf.feature_id=f.feature_id and f.feature_type='Module' and pf.package_id='" . $packageInfo->package_id . "'")->result();
        $i = 0;
        foreach ($featInfo as $value) {
            $data['features'][$i]['feature_name'] = $value->feature_name;
            $i++;
        }
        return $data;
    }

    function clinicDetails($clinic_id)
    {
        $CI = &get_instance();
        $clinicInfo = $CI->db->query("select * from clinics where clinic_id='" . $clinic_id . "'")->row();
        return $clinicInfo;
    }

    function getDocClinicDetails($docId)
    {
        $CI = &get_instance();
        $clinicsInfo = $CI->db->query("select * from clinic_doctor where doctor_id='" . $docId . "'")->result();
        return $clinicsInfo;
    }

    // get patient details from appointment_id
    function getPatientDetails($patient_id)
    {
        $CI = &get_instance();
        $patientInfo = $CI->db->query("select * from patients where patient_id='" . $patient_id . "'")->row();
        return $patientInfo;
    }

    // get patient details from appointment_id
    function getPatientName($patient_id)
    {
        $CI = &get_instance();
        $patientInfo = $CI->db->query("select * from patients where patient_id='" . $patient_id . "'")->row();
        $title = $patientInfo->title;
        if ($title == "") {
            $fullname = $patientInfo->first_name . " " . $patientInfo->last_name;
        } else {
            $fullname = $title . ". " . $patientInfo->first_name . " " . $patientInfo->last_name;
        }
        return $fullname; 
    }

    // get features based on pacakge id
    function getFeaturesIDByPackage($package_id)
    {
        $CI = &get_instance();
        $featInfo = $CI->db->select("GROUP_CONCAT(entity_id) as feat")->from("package_features")->where("package_id", $package_id)->where("feature_type", "Module")->get()->row();
        return $featInfo;
    }


    // get functionalities based on pacakge id
    function getFunctionalitiesByPackageID($package_id)
    {
        $CI = &get_instance();
        $featInfo = $CI->db->select("GROUP_CONCAT(functionality_id) as func")->from("package_features")->where("package_id", $package_id)->where("feature_type", "Functionality")->get()->row();
        return $featInfo;
    }

    //Get Drug Price from Clinic Pharmacy Inventory
    function getDrugPrice($clinic_id, $drug_id, $qty)
    {
        $CI = &get_instance();
        $priceInfo = $CI->db->query("select mrp/pack_size as amount_per_unit from clinic_pharmacy_inventory_inward where clinic_id='" . $clinic_id . "' and drug_id='" . $drug_id . "' order by clinic_pharmacy_inventory_inward_id DESC")->row();
        // echo $CI->db->last_query();
        if (count($priceInfo) > 0) {
            return $priceInfo->amount_per_unit * $qty;
        } else {
            return 0;
        }
    }

    //get Investigation Price from clinic or masters(if not exists in clinic)
    function getInvestigationPrice($investigation_id, $clinic_id)
    {
        $CI = &get_instance();
        $clinicInvStatus = $CI->db->query("select cip.price from clinic_investigations ci,clinic_investigation_price cip where ci.investigation_id=cip.investigation_id and ci.investigation_id='" . $investigation_id . "' and ci.clinic_id='" . $clinic_id . "'")->row();
        // echo $CI->db->last_query();
        if (count($clinicInvStatus) > 0) {
            $price = $clinicInvStatus->price;
        } else {
            $price = 0.00;
        }
        return $price;
    }

    // get stock info 
    function getStockInfo($clinic_id, $drug_id)
    {

        $today = date('Y-m-d');
        $edate = strtotime($value['expiry_date']);
        $nxt_date = strtotime("+3 month");

        $CI = &get_instance();
        $inward = $CI->db->select("clinic_id, batch_no, drug_id, sum(quantity) as quantity_supplied, expiry_date, status")
            ->from("clinic_pharmacy_inventory_inward")
            ->where("clinic_id = '" . $clinic_id . "' AND drug_id='" . $drug_id . "' AND expiry_date > '" . $today . "' and status = 1 AND archieve=0")
            ->group_by("drug_id")->get()->result();
        // $para[20]['p_inventory']->query = $CI->db->last_query();     
        // echo count($inward);
        // $para['inQ']= $CI->db->last_query();
        $i = 0;
        if (count($inward) > 0) {
            // $para['oo'] = $inward;
            foreach ($inward as $pharmacy_inventory) {

                $drugInfo = getDrugInfo($pharmacy_inventory->drug_id);

                $outward = $CI->db->select("batch_no, drug_id, sum(quantity) as quantity_sold")
                    ->from("clinic_pharmacy_inventory_outward")
                    ->where("batch_no = '" . $pharmacy_inventory->batch_no . "' and drug_id = '" . $pharmacy_inventory->drug_id . "'
                 AND clinic_id = '" . $clinic_id . "' ")
                    ->group_by("drug_id")->get()->row();
                // echo $CI->db->last_query();sss
                if (count($outward)) {
                    $qty_supplied = $pharmacy_inventory->quantity_supplied;
                    $qty_sold = $outward->quantity_sold;

                    // available quantity
                    $pharmacy_inventory->available_quantity = (string)($qty_supplied - $qty_sold);
                } else {
                    // available quantity
                    $pharmacy_inventory->available_quantity = (string) ($pharmacy_inventory->quantity_supplied);
                }

                // 

                $para[$i]['clinic_id'] = $clinic_id;
                $para[$i]['batch_no'] = $pharmacy_inventory->batch_no;
                $para[$i]['drug_id'] = $pharmacy_inventory->drug_id;
                $para[$i]['quantity_supplied'] = $pharmacy_inventory->available_quantity;
                $para[$i]['expiry_date'] = $pharmacy_inventory->expiry_date;
                $para[$i]['status'] = 1;
                $para[$i]['trade_name'] = $drugInfo->trade_name;
                // $para[$i][] = $drugInfo->trade_name;
                // $para[$i][]->trade_name = htmlspecialchars_decode("<strong>".$drugInfo->trade_name."</strong>&emsp;<label class='badge badge-primary'>".$pharmacy_inventory->expiry_date."</label>&emsp;<label class='badge badge-success'>QTY : ".$pharmacy_inventory->available_quantity."</label>");
                $para[$i]['formulation'] = $drugInfo->formulation;
                $para[$i]['category'] = $drugInfo->category;

                $i++;
            }
        } else {
            $para = array();
        }
        return $para;
        // print_r( $pharmacy_inventory->quantity_supplied);
    }

    //Get Drug Price from Clinic Pharmacy Inventory
    function getDrugInfo($id)
    {
        $CI = &get_instance();
        $drugInfo = $CI->db->query("select * from drug where drug_id = '" . $id . "' ")->row();
        return $drugInfo;
    }

    function hasPermission($entity_id, $field)
    {
        $CI = &get_instance();
        $profile_id = $CI->session->userdata('role_id');

        $entity_list_id = $CI->db->query("select * from user_entities where user_entity_alias ='" . $entity_id . "'")->row();


        $role_permessions = $CI->db->query("select * from profile_permissions where user_entity_id ='" . $entity_list_id->user_entity_id . "' and profile_id ='" . $profile_id . "' and $field = 1")->row();

        if (count($role_permessions) > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Get CRUD Access Information for the Concern Entity for the Role/Profile
    function getCRUDInfo($entity_url)
    {

        $CI = &get_instance();
        $profile_id = $CI->session->userdata('profile_id');

        // Get entity id
        $entity_id = $CI->Generic_model->getFieldValue('user_entities', 'user_entity_id', array('entity_url' => $entity_url));

        $crudInfo = $CI->db->select('p_create, p_read, p_update, p_delete')->from('profile_permissions')->where('user_entity_id =', $entity_id)->where('profile_id =', $profile_id)->get()->row();

        if ($crudInfo) {
            return $crudInfo;
        }
    }


    // Get Accessibility Status for User Property
    function getPropertyAccess($property_name)
    {

        $CI = &get_instance();
        $profile_id = $CI->session->userdata('profile_id');

        // Get user_property_id with $property_name
        $user_property_id = $CI->Generic_model->getFieldValue('user_properties', 'user_property_id', array('property_name' => $property_name));

        // Get Property Access 
        $status = $CI->Generic_model->getFieldValue('profile_property_accessibility', 'status', array('profile_id' => $profile_id, 'user_property_id' => $user_property_id));

        return $status;
    }


    // Get Home Page for the profile
    function getHome($profile_id = NULL)
    {

        $CI = &get_instance();

        if ($profile_id == NULL) {
            $profile_id = $CI->session->userdata('profile_id');
        }

        // Home Page
        $home = $CI->db->select('P.profile_id, P.profile_name, UE.entity_url as home_url')->from('profiles P')->join('user_entities UE', 'P.user_entity_id = UE.user_entity_id', 'inner')->where('P.profile_id =', $profile_id)->get()->row();

        if (count($home) > 0) {
            return $home->home_url;
        } else {
            return 0;
        }
    }

    function getUserEntityName($id){
        $CI =& get_instance();
        $info = $CI->db->query("select * from user_entities where user_entity_id='".$id."'")->row();
        return $info->user_entity_name;
    }

    function userEntityInfo($id){
        $CI =& get_instance();
        $info = $CI->db->query("select * from user_entities where user_entity_id='".$id."'")->row();
        return $info;
    }

    function FunctionalityInfo($id){
        $CI =& get_instance();
        $info = $CI->db->query("select * from features where feature_id='".$id."'")->row();
        return $info;
    }

    // function getNextAvailabilityInfo($doctor_id,){
    //     $CI =& get_instance();
    //     $clinicDocInfo = $CI->db->query("select * from clinic_doctor where doctor_id='".$doctor_id."'")->result();
    //     if(count($clinicDocInfo) > 0){
    //         foreach($clinicDocInfo as $value){
    //             $docQ = $CI->db->query("select")->row();
    //         }
    //     }
    // }

    function getLabPatientReports($package_line_item_id, $lab_patient_report_id, $type)
    {
        $CI = &get_instance();
        $info = $CI->db->query("select * from lab_patient_report_line_items where lab_patient_report_id='" . $lab_patient_report_id . "' and clinic_lab_package_line_item_id='" . $package_line_item_id . "'")->row();
        if (count($info) > 0) {
            return $info->$type;
        } else {
            return "";
        }
    }


    // Get Access Right Information w.r.to Access Control Object 'CRUD'
    function getAccessInfo($entity, $entity_url, $aco)
    {

        $CI = &get_instance();
        $profile_id = $CI->session->userdata('profile_id');

        // Get entity id
        $entity_id = $CI->Generic_model->getFieldValue('user_entities', 'user_entity_id', array('user_entity_name' => $entity, 'entity_url' => $entity_url));

        $access = $CI->db->select('*')->from('profile_permissions')->where('user_entity_id =', $entity_id)->where('profile_id =', $profile_id)->where($aco, 1)->get()->num_rows();

        if ($access) {
            return 1;
        } else {
            return 0;
        }
    }


    function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }



    function getparameters($id, $date)
    {
        $CI = &get_instance();
        $res = $CI->db->query("select * from patient_followup pf inner join patient_followup_line_items pfl on(pf.patient_followup_id = pfl.patient_followup_id)  where pfl.parameter_id = '" . $id . "' and pfl.appointment_id='" . $date . "'")->row();

        if (count($res) > 0) {
            return $res->parameter_value;
        } else {
            return "";
        }
    }

    function textlocalSend($mobilenumbers, $message)
    {
        // Account details
        $apiKey = urlencode('UVeJillKP5k-DdP0aRMj0OG2B9TLn07IOfVzdC5ohu');

        // Message details
        $numbers = array($mobilenumbers);
        $sender = urlencode('OUMDAA');

        // $apt = "Dear $PName,%nYour Appointment is fixed with $DName on $Date @ $Time.%nFrom,%n$CName."; //working
        // $fuap = "Dear $PName,%nYour Follow Up Appointment with $DName on $Date @ $Time.%nFrom,%n$CName."; //working
        // $otpsms = "Dear User,%nYour One Time Password (OTP) is $otp. Please enter the OTP to proceed.%nThank You,%nUMDAA Health Care."; //working
        // $welcome = "Dear $Name,%nWelcome to UMDAA Family. Your account has been created successfully. You can log into our App with your Reg. Mobile/Email. For further information check you Reg. email account. Please visit this link for further instructions https://tx.gl/r/26DIV/#AdvdTrack# ."; //working
        // $reg = "New Doctor has Registered. Name: $Name; Mobile No.: $Number; Specialty: $Specialty."; //working

        $message = rawurlencode($message);

        $numbers = implode(',', $numbers);

        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

        // Send the POST request with cURL
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Process your response here
        // echo $response."<br>";
        // echo $message;
    }

    function sendsms($mobilenumbers, $message)
    {

        /*
        $api_key = '55CEFE04537378';
        $contacts = $mobilenumbers;
        $from = 'OUMDAA';
        $sms_text = urlencode($message); 

        //Submit to server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://smsgateway4.deifysolutions.com/app/smsapi/index.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "key=".$api_key."&campaign=7279&routeid=3&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
        */
        // echo "mobiile". $mobilenumbers;
        // echo "message".$message;





        // $data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
        // $ch = curl_init('http://api.textlocal.in/send/?');
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $result = curl_exec($ch); // This is the result from the API
        // curl_close($ch);
        // return $response;

        $profile_id = 't5umdaa';
        $api_key = '010km0X150egpk3lD9dQ';
        $sender_id = 'UMDAAO';
        $mobile = $mobilenumbers;
        $sms_text = urlencode($message);

        //Submit to server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.nimbusit.info/api/pushsms.php?");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=" . $profile_id . "&key=" . $api_key . "&sender=" . $sender_id . "&mobile=" . $mobile . "&text=" . $sms_text);
        $response = curl_exec($ch);
        return $response;
        curl_close($ch);
    }

    function sendBulkSMS($mobilenumber, $message)
    {
        $profile_id = 't5umdaa';
        $api_key = '010km0X150egpk3lD9dQ';
        $sender_id = 'UMDAAO';
        $mobile = $mobilenumber;
        $sms_text = urlencode($message);

        //Submit to server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.nimbusit.info/api/pushsms.php?");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=" . $profile_id . "&key=" . $api_key . "&sender=" . $sender_id . "&mobile=" . $mobile . "&text=" . $sms_text);
        $response = curl_exec($ch);
        // echo $response;
        curl_close($ch);
        return $response;
    }


    function send_otp($mobilenumber, $otp, $message)
    {

        $username = "itservices@umdaa.co";
        $hash = "98ee8fab23e5a937d65ce4705700b6651e0cd3654091a1ac0d7010d19a792d47";

        // Config variables. Consult http://api.textlocal.in/docs for more info.
        $test = "0";
        $mobile = "91" . $mobilenumbers;
        $sms_text = rawurlencode($message);

        // Data for text message. This is the text message data.
        $sender = "OUMDAA"; // This is who the message appears to be from.
        $numbers = $mobile; // A single number or a comma-seperated list of numbers
        $message = $sms_text;
        // 612 chars or less
        // A single number or a comma-seperated list of numbers

        $apiKey = urlencode('UVeJillKP5k-DdP0aRMj0OG2B9TLn07IOfVzdC5ohu');

        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

        // echo "<pre>";print_r($data);echo "</pre>";

        // Send the POST request with cURL
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    function getDoctorSlots($slot, $type, $cd_id, $day)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from("clinic_doctor_weekdays cdw");
        $CI->db->join("clinic_doctor_weekday_slots cdws", "cdw.clinic_doctor_weekday_id=cdws.clinic_doctor_weekday_id");
        $CI->db->where("cdw.slot", $slot);
        $CI->db->where("cdw.clinic_doctor_id", $cd_id);
        $CI->db->where("cdws.session", $type);
        $CI->db->where("cdw.weekday", $day);
        $res = $CI->db->get()->row();

        return $res;
    }

    function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {

        $array = array();

        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }

    /*
    Function rendering 
    created by , modified by, created date time & modified date time
    */
    function get_CM_by_dates()
    {
        $cmInfo['created_by'] = $cmInfo['modified_by'] = $_SESSION['user_id'];
        $cmInfo['created_date_time'] = $cmInfo['modified_date_time'] = date('Y-m-d H:i:s');

        return $cmInfo;
    }

    // Using this for charts by vikram
    function getDatesFromRange1($start, $end, $format = 'd-m-Y')
    {

        $array = array();

        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }

    function array_flatten($array)
    {
        if (!is_array($array)) {
            return FALSE;
        }
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, array_flatten($value));
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    function checkAccess($entity_id, $module_id, $package_id){
        $CI = &get_instance();
        $check = $CI->db->query("select * from package_features where module_id='".$module_id."' and entity_id='".$entity_id."' and package_id='".$package_id."'")->row();
        // return $CI->db->last_query();
        if(count($check) > 0){
            return 1;
        }
        else{
            return 0;
        }

    }

    function accessprofile($entity_id, $field)
    {

        $CI = &get_instance();
        $profile_id = $CI->session->userdata('profile_id');
        $entity_list_id = $CI->db->query("select * from user_entities where method_name ='" . $entity_id . "'")->row();

        $profile_permessions = $CI->db->query("select * from profile_permissions where user_entity_id ='" . $entity_list_id->user_entity_id . "' and profile_id ='" . $profile_id . "' and " . $field . " = 1")->row();
        //echo $CI->db->last_query();
        if ($profile_permessions) {
            return true;
        } else {
            return false;
        }
    }

    function getvalue($name)
    {

        $CI = &get_instance();
        $vital_id = $CI->db->query("select * from patient_vital_sign where vital_sign ='" . $name . "'")->row();

        if (count($vital_id) > 0) {
            return $vital_id->vital_result;
        } else {
            return false;
        }
    }

    function generate_invoice_no($clinic_id = '')
    {

        $CI = &get_instance();

        // Get last invoice no w.r.ro the clinic id
        $invRec = $CI->db->select("invoice_no_alias")->from('billing')->where('clinic_id =', $clinic_id)->order_by('billing_id', 'DESC')->get()->row();
        $last_inv_no = $invRec->invoice_no_alias;


        if (count($invRec) == 0) {
            // Make new invoice no.
            $generatedNo = date('ymd') . "1";
        } else {
            // Generate a new invoice no.
            $last_no = trim(substr($last_inv_no, 6));
            $current_no = $last_no + 1;
            $generatedNo = date('ymd') . $current_no;
        }
        return $generatedNo;
    }

    function generate_invoice_no_lab($clinic_id = '')
    {

        $CI = &get_instance();
        $generatedNo = date('ymd') . rand(10, 3000);
        return $generatedNo;
    }

    function generate_billing_invoice_no($clinic_id = '')
    {
        $CI = &get_instance();
        $invRec = $CI->db->select("invoice_no_alias")->from('billing_invoice')->order_by('billing_invoice_id', 'DESC')->get()->row();
        $last_inv_no = $invRec->invoice_no_alias;

        if (count($invRec) == 0) {
            // Make new invoice no.
            $generatedNo = date('ymd') . "1";
        } else {
            // Generate a new invoice no.
            $last_no = trim(substr($last_inv_no, 6));
            $current_no = $last_no + 1;
            $generatedNo = date('ymd') . $current_no;
        }
        return $generatedNo;
    }


    function checkexpiry()
    {
        $month = date('n');
        $year = date('Y');
        $IsLeapYear = date('L');
        $NextYear = $year + 1;
        $IsNextYearLeap = date('L', mktime(0, 0, 0, 1, 1, $NextYear));
        $TodaysDate = date('j');
        if (strlen($month + 3) < 10) {
            $UpdateMonth = "0" . ($month + 3);
        }
        if ($month > 9) {
            if ($month == 10) {
                $UpdateMonth = "01";
            } else if ($month == 11) {
                $UpdateMonth = "02";
            } else {
                $UpdateMonth = "03";
            }
        }

        if (($month != 10) && ($month != 11) && ($month != 12)) {
            if (($month & 1) && ($TodaysDate != 31)) {
                $DateAfterThreeMonths = $year . "-" . $UpdateMonth . "-" . $TodaysDate;
            } else if (($month & 1) && ($TodaysDate == 31)) {
                $DateAfterThreeMonths = $year . "-" . $UpdateMonth . "-30";
            } else {
                $DateAfterThreeMonths = $year . "-" . $UpdateMonth . "-" . $TodaysDate;
            }
        } else if ($month == 11) {
            if (($TodaysDate == 28) || ($TodaysDate == 29) || ($TodaysDate == 30)) {
                if ($IsLeapYear == 1) {
                    $DateAfterThreeMonths = ($year + 1) . "-" . $UpdateMonth . "-28";
                } else if ($IsNextYearLeap == 1) {
                    $DateAfterThreeMonths = ($year + 1) . "-" . $UpdateMonth . "-29";
                } else {
                    $DateAfterThreeMonths = ($year + 1) . "-" . $UpdateMonth . "-28";
                }
            } else {
                $DateAfterThreeMonths = ($year + 1) . "-" . $UpdateMonth . "-" . $TodaysDate;
            }
        } else {
            $DateAfterThreeMonths = ($year + 1) . "-" . $UpdateMonth . "-" . $TodaysDate;
        }
        return $DateAfterThreeMonths;
    }

    //Encryption And Decryption
    function DataCrypt($string, $action)
    {

        $CI = &get_instance();

        $secret_key = 'Goldfish';
        $secret_iv = 'UMDAA';

        $output = "";
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($string == "") {
            return null;
        } else {
            if ($action == 'encrypt') {
                $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
            } else if ($action == 'decrypt') {
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            }

            return $output;
        }
    }

    function base64_to_jpeg($base64_string, $output_file)
    {
        // open the output file for writing
        $ifp = fopen($output_file, 'wb');

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64_string);

        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($data[1]));

        // clean up the file resource
        fclose($ifp);

        return $output_file;
    }

    function translate($text, $target)
    {

        $apiKey = '9d79185aaeb5416788dc38c674a80e0a';
        $url = "https://api.cognitive.microsofttranslator.com/translate?api-version=3.0&to=" . $target;

        $data_string = json_encode(array("Text" => $text));
        //Send blindly the json-encoded string.
        //The server, IMO, expects the body of the HTTP request to be in JSON
        $fields = array('Text' => $text);

        $headers = array(
            'Ocp-Apim-Subscription-Key: ' . $apiKey,
            'Ocp-Apim-Subscription-Region: centralindia',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "[" . json_encode($fields) . "]");
        $result = curl_exec($ch);
        $array = json_decode($result);
        return @$array[0]->translations[0]->text;
    }

    // function generateQRCode($patient_id){
    //     $CI =& get_instance();
    //     $patientInfo = $CI->db->query("select * from patients where patient_id='".$patient_id."'")->row();
    //     $tempDir = './uploads/qrcodes/patients/';
    //     $codeContents = $patientInfo->umr_no;
    //     $qrname = $umr_no . md5($codeContents) . '.png';
    //     $pngAbsoluteFilePath = $tempDir . $qrname;
    //     echo $urlRelativeFilePath = base_url() . 'uploads/qrcodes/patients/' . $qrname;

    //     if (!file_exists($pngAbsoluteFilePath)) {
    //         QRcode::png($codeContents, $pngAbsoluteFilePath);
    //     }

    //     $data['qrcode'] = $qrname;
    //     $CI->Generic_model->updateData('patients', $data, array('patient_id'=>$patient_id));

    //     return $CI->db->last_query();
    // }

    function masterDrugInfo($drug_id)
    {
        $CI = &get_instance();
        $drugInfo = $CI->db->query("select * from drug where drug_id='" . $drug_id . "'")->row();
        return $drugInfo;
    }

    function masterCDInfo($cd_id)
    {
        $CI = &get_instance();
        $cdInfo = $CI->db->query("select * from clinical_diagnosis where clinical_diagnosis_id='" . $cd_id . "'")->row();
        return $cdInfo;
    }

    function masterInvInfo($investigation_id)
    {
        $CI = &get_instance();
        $invInfo = $CI->db->query("select * from investigations where investigation_id='" . $investigation_id . "'")->row();
        return $invInfo;
    }

    function getreferalDoctorname($doc_id)
    {
        $CI = &get_instance();
        $docInfo = $CI->db->query("select * from referral_doctors where rfd_id='$doc_id'")->row();
        return $docInfo->doctor_name;
    }

    //getProfile Completeness percentage
    function profileCompletion($id)
    {
        $CI = &get_instance();
        $query = "SELECT IF (title IS NULL OR title = '', 1, 0) +  IF (first_name IS NULL OR first_name = '', 1, 0) + IF (gender IS NULL OR gender = '', 1, 0) + IF (age IS NULL OR age = '', 1, 0) + IF (age_unit IS NULL OR age_unit = '', 1, 0) + IF (country IS NULL OR country = '', 1, 0) + IF (email_id IS NULL OR email_id = '', 1, 0) + IF (address_line IS NULL OR address_line = '', 1, 0) + IF (district_id IS NULL OR district_id = '', 1, 0) + IF (state_id IS NULL OR state_id = '', 1, 0) + IF (preferred_language IS NULL OR preferred_language = '', 1, 0) + IF (pincode IS NULL OR pincode = '', 1, 0) as empty_field_count FROM patients where patient_id='$id'";
        $result = $CI->db->query($query)->row();
        $percentage = round(($result->empty_field_count * 100) / 12);
        return 100 - $percentage;
    }

    function checkToken($user_id, $accessToken)
    {
        $CI = &get_instance();
        return $accessToken;

        if ($accessToken == "") {
            return 0;
        } else {
            $token = DataCrypt($accessToken, 'encrypt');
            $check = $CI->db->query("select * from users_device_info where user_id='" . $user_id . "' and secureAccessToken='" . $token . "'")->row();
            // echo $CI->db->last_query();
            if (count($check) > 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    //Generate Access Token
    function generateAccessToken()
    {
        $time = time();
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $randStr = substr(str_shuffle($str), 0, 15);
        $token = md5(str_shuffle($randStr . $time));
        return $token;
    }

    //getAccessToken By UserID
    function getAccessToken($id)
    {
        $CI = &get_instance();
        $data = $CI->db->select("secure_accessToken")->from("users")->where("user_id='" . $id . "'")->get()->row();
        return $data->secure_accessToken;
    }

    // Filter string
    function filterString($str)
    {

        $CI = &get_instance();

        if (get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }
        return $CI->db->real_escape_string($str);
    }


    // Update string for breaks 
    function breakStr($str)
    {
        return trim(nl2br(htmlentities($str, ENT_QUOTES, 'UTF-8')));
    }

    //SMS Counter
    function smsCounter($doctor_id)
    {
        $CI = &get_instance();
        $dateObj = DateTime::createFromFormat('!m', date('m'));
        $month = $dateObj->format('F');
        $year = date("Y");
        $credits = "1";
        $modified_date_time = date("Y-m-d H:i:s");
        $sms_counter = $CI->db->query("select count(*) as count," . $month . ",sms_counter_id from sms_counter where `doctor_id`='" . $doctor_id . "' and `year`='" . $year . "'")->row();
        if ($sms_counter->count > 0) {
            $counter = $sms_counter->$month + $credits;
            $data[$month] = $counter;
            $CI->Generic_model->updateData('sms_counter', $data, array('sms_counter_id' => $sms_counter->sms_counter_id));
        } else {
            $data[$month] = $credits;
            $data['year'] = $year;
            $data['doctor_id'] = $doctor_id;
            $data['modified_date_time'] =
                $CI->Generic_model->insertData('sms_counter', $data);
        }
    }

    // Update investigation master
    function update_investigation_master_version($clinic_id)
    {

        $master_name = "investigation";

        $CI = &get_instance();

        // Load the file with required data and make the JSON fie ready
        $masterData = $CI->db->distinct()->select('IR.investigation_id, I.investigation, IR.sample_type')->from('investigation_range IR')->join('investigations I', 'IR.investigation_id = I.investigation_id', 'inner')->order_by('IR.investigation_id', 'ASC')->get()->result_array();
        // $masterData = $CI->db->select($field)->distinct()->from($table)->get()->result_array();

        // echo '<pre>';
        // print_r($masterData);
        // echo '</pre>';

        $prefix = '';
        $prefix .= '[';
        foreach ($masterData as $record) {
            if ($record['investigation'] != '') {
                $prefix .= json_encode($record['investigation'] . " Sample - " . $record['sample_type']);
                $prefix .= ',';
            }
        }
        $prefix .= ']';

        $json_file = str_replace(",]", "]", trim($prefix, ","));

        // Check if the master_name exists w.r.to clinic_id
        $master = $CI->db->select('master_version_id, version_code, json_file_name as old_file')->from('master_version')->where('clinic_id =', $clinic_id)->where('master_name =', $master_name)->get()->row();

        if (count($master) > 0) {
            // Update the version and generate the new file 
            // Increase the version code and generate the new file name
            $version_code = $master->version_code;
            $version_code = ++$version_code;

            if ($clinic_id != 0) {
                $path_user = './uploads/' . $clinic_id . '_' . $master_name . '_v' . $version_code . '.json';
            } else {
                $path_user = './uploads/' . $master_name . '_v' . $version_code . '.json';
            }

            $old_path_user = './uploads/' . $master->old_file;

            $newData['version_code'] = $version_code;

            if ($clinic_id != 0) {
                $newData['json_file_name'] = $clinic_id . '_' . $master_name . '_v' . $version_code . '.json';
                $json_file_name = $clinic_id . '_' . $master_name . '_v' . $version_code . '.json';
            } else {
                $newData['json_file_name'] = $master_name . '_v' . $version_code . '.json';
                $json_file_name = $master_name . '_v' . $version_code . '.json';
            }

            // Update the master version table with investigation
            $CI->Generic_model->updateData('master_version', $newData, array('master_version_id' => $master->master_version_id));
        } else {
            // Create the record with master version, version code and generate new file name
            $newData['version_code'] = 1;

            if ($clinic_id != 0) {
                $newData['json_file_name'] = $clinic_id . '_' . $master_name . '_v1.json';
                $json_file_name = $clinic_id . '_' . $master_name . '_v1.json';
            } else {
                $newData['json_file_name'] = $master_name . '_v1.json';
                $json_file_name = $master_name . '_v1.json';
            }

            $newData['clinic_id'] = $clinic_id;
            $newData['master_name'] = $master_name;
            $newData['created_date_time'] = $newData['modified_date_time'] = date('Y-m-d H:i:s');
            $newData['status'] = 1;

            // Insert new record into the master version for investigation master
            $CI->Generic_model->insertData('master_version', $newData);

            if ($clinic_id != 0) {
                $path_user = './uploads/' . $clinic_id . '_' . $master_name . '_v1.json';
            } else {
                $path_user = './uploads/' . $master_name . '_v1.json';
            }

            $old_path_user = '';
        }

        // Remove the old file from the location
        if (file_exists($old_path_user)) {
            unlink($old_path_user);
        }

        // Save the file to the location
        if (!file_exists($path_user)) {
            $fp = fopen('./uploads/' . $json_file_name, 'w');
            fwrite($fp, $json_file);
        } else {
            unlink($path_user);
            $fp = fopen('./uploads/' . $json_file_name, 'w');
            fwrite($fp, $json_file);
        }
    }


    // Update investigation master
    function update_clinic_investigation_master_version($clinic_id)
    {

        $master_name = "clinic_investigations";

        $CI = &get_instance();

        // Load the file with required data and make the JSON fie ready
        $master_CIR_data = $CI->db->distinct()->select('CIR.investigation_id, CI.clinic_investigation_id, I.investigation, CIR.sample_type')->from('clinic_investigation_range CIR')->join('investigations I', 'CIR.investigation_id = I.investigation_id', 'inner')->join('clinic_investigations CI', 'CIR.investigation_id = CI.investigation_id')->where('CI.clinic_id =', $clinic_id)->order_by('CIR.investigation_id', 'ASC')->get()->result_array();

        $master_CI_data = $CI->db->distinct()->select('CI.investigation_id, CI.clinic_investigation_id, I.investigation')->from('clinic_investigations CI')->join('investigations I', 'CI.investigation_id = I.investigation_id', 'inner')->where('CI.clinic_id =', $clinic_id)->where('CI.package =', 1)->order_by('CI.investigation_id', 'ASC')->get()->result_array();

        $master_packages_data = $CI->db->distinct()->select('clinic_investigation_package_id, package_name')->from('clinic_investigation_packages')->where('clinic_id =', $clinic_id)->order_by('clinic_investigation_package_id', 'ASC')->get()->result_array();

        $masterData = array_merge($master_CI_data, $master_CIR_data, $master_packages_data);

        $prefix = '';
        $prefix .= '[';
        foreach ($masterData as $record) {
            if ($record['investigation'] != '') {
                if (isset($record['sample_type'])) {
                    if ($record['sample_type'] != '') {
                        $prefix .= json_encode($record['investigation'] . " (Sample: " . $record['sample_type'] . ")");
                    } else {
                        $prefix .= json_encode($record['investigation']);
                    }
                } else {
                    $prefix .= json_encode($record['investigation']);
                }
                $prefix .= ',';
            } elseif (isset($record['package_name'])) {
                $prefix .= json_encode($record['package_name'] . " (Package)");
                $prefix .= ',';
            }
        }
        $prefix .= ']';

        $json_file = str_replace(",]", "]", trim($prefix, ","));

        // Check if the master_name exists w.r.to clinic_id
        $master = $CI->db->select('master_version_id, version_code, json_file_name as old_file')->from('master_version')->where('clinic_id =', $clinic_id)->where('master_name =', $master_name)->get()->row();

        if (count($master) > 0) {
            // Update the version and generate the new file 
            // Increase the version code and generate the new file name
            $version_code = $master->version_code;
            $version_code = ++$version_code;

            if ($clinic_id != 0) {
                $path_user = './uploads/clinic_investigations/' . $clinic_id . '_' . $master_name . '_v' . $version_code . '.json';
            } else {
                $path_user = './uploads/clinic_investigations/' . $master_name . '_v' . $version_code . '.json';
            }

            $old_path_user = './uploads/clinic_investigations/' . $master->old_file;

            $newData['version_code'] = $version_code;

            if ($clinic_id != 0) {
                $newData['json_file_name'] = $clinic_id . '_' . $master_name . '_v' . $version_code . '.json';
                $json_file_name = $clinic_id . '_' . $master_name . '_v' . $version_code . '.json';
            } else {
                $newData['json_file_name'] = $master_name . '_v' . $version_code . '.json';
                $json_file_name = $master_name . '_v' . $version_code . '.json';
            }

            // Update the master version table with investigation
            $CI->Generic_model->updateData('master_version', $newData, array('master_version_id' => $master->master_version_id));
        } else {
            // Create the record with master version, version code and generate new file name
            $newData['version_code'] = 1;

            if ($clinic_id != 0) {
                $newData['json_file_name'] = $clinic_id . '_' . $master_name . '_v1.json';
                $json_file_name = $clinic_id . '_' . $master_name . '_v1.json';
            } else {
                $newData['json_file_name'] = $master_name . '_v1.json';
                $json_file_name = $master_name . '_v1.json';
            }

            $newData['clinic_id'] = $clinic_id;
            $newData['master_name'] = $master_name;
            $newData['created_date_time'] = $newData['modified_date_time'] = date('Y-m-d H:i:s');
            $newData['status'] = 1;

            // Insert new record into the master version for investigation master
            $CI->Generic_model->insertData('master_version', $newData);

            if ($clinic_id != 0) {
                $path_user = './uploads/clinic_investigations/' . $clinic_id . '_' . $master_name . '_v1.json';
            } else {
                $path_user = './uploads/clinic_investigations/' . $master_name . '_v1.json';
            }

            $old_path_user = '';
        }

        // Remove the old file from the location
        if (file_exists($old_path_user)) {
            unlink($old_path_user);
        }

        // Save the file to the location
        if (!file_exists($path_user)) {
            $fp = fopen('./uploads/clinic_investigations/' . $json_file_name, 'w');
            fwrite($fp, $json_file);
        } else {
            unlink($path_user);
            $fp = fopen('./uploads/clinic_investigations/' . $json_file_name, 'w');
            fwrite($fp, $json_file);
        }
    }


    // Update master version
    function update_master_version($table, $field, $master_name, $clinic_id)
    {

        $CI = &get_instance();

        // Load the file with required data and make the JSON fie ready
        $masterData = $CI->db->select($field)->distinct()->from($table)->get()->result_array();

        $prefix = '';
        $prefix .= '[';
        foreach ($masterData as $record) {
            if ($record[$field] != '') {
                $prefix .= json_encode($record[$field]);
                $prefix .= ',';
            }
        }
        $prefix .= ']';

        $json_file = str_replace(",]", "]", trim($prefix, ","));

        // Check if the master_name exists w.r.to clinic_id
        $master = $CI->db->select('master_version_id, version_code, json_file_name as old_file')->from('master_version')->where('clinic_id =', $clinic_id)->where('master_name =', $master_name)->get()->row();

        if (count($master) > 0) {
            // Update the version and generate the new file 
            // Increase the version code and generate the new file name
            $version_code = $master->version_code;
            $version_code = ++$version_code;

            if ($clinic_id != 0) {
                $path_user = './uploads/' . $clinic_id . '_' . $master_name . '_v' . $version_code . '.json';
            } else {
                $path_user = './uploads/' . $master_name . '_v' . $version_code . '.json';
            }

            $old_path_user = './uploads/' . $master->old_file;

            $newData['version_code'] = $version_code;

            if ($clinic_id != 0) {
                $newData['json_file_name'] = $clinic_id . '_' . $master_name . '_v' . $version_code . '.json';
                $json_file_name = $clinic_id . '_' . $master_name . '_v' . $version_code . '.json';
            } else {
                $newData['json_file_name'] = $master_name . '_v' . $version_code . '.json';
                $json_file_name = $master_name . '_v' . $version_code . '.json';
            }

            // Update the master version table with investigation
            $CI->Generic_model->updateData('master_version', $newData, array('master_version_id' => $master->master_version_id));
        } else {
            // Create the record with master version, version code and generate new file name
            $newData['version_code'] = 1;

            if ($clinic_id != 0) {
                $newData['json_file_name'] = $clinic_id . '_' . $master_name . '_v1.json';
                $json_file_name = $clinic_id . '_' . $master_name . '_v1.json';
            } else {
                $newData['json_file_name'] = $master_name . '_v1.json';
                $json_file_name = $master_name . '_v1.json';
            }

            $newData['clinic_id'] = $clinic_id;
            $newData['master_name'] = $master_name;
            $newData['created_date_time'] = $newData['modified_date_time'] = date('Y-m-d H:i:s');
            $newData['status'] = 1;

            // Insert new record into the master version for investigation master
            $CI->Generic_model->insertData('master_version', $newData);

            if ($clinic_id != 0) {
                $path_user = './uploads/' . $clinic_id . '_' . $master_name . '_v1.json';
            } else {
                $path_user = './uploads/' . $master_name . '_v1.json';
            }

            $old_path_user = '';
        }

        // Remove the old file from the location
        if (file_exists($old_path_user)) {
            unlink($old_path_user);
        }

        // Save the file to the location
        if (!file_exists($path_user)) {
            $fp = fopen('./uploads/' . $json_file_name, 'w');
            fwrite($fp, $json_file);
        } else {
            unlink($path_user);
            $fp = fopen('./uploads/' . $json_file_name, 'w');
            fwrite($fp, $json_file);
        }
    }
}