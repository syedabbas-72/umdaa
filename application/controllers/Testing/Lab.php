<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class Lab extends REST_Controller1
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

    //Get Investigations if present for that particular appointment_id
    public function investigations_get($pil_id='')
    {
        $this->db->select("*");
        $this->db->from("patient_investigation p");
        $this->db->join("patient_investigation_line_items pl","p.patient_investigation_id=pl.patient_investigation_id");
        $this->db->where("pl.patient_investigation_line_item_id='".$pil_id."'");
        $invInfo = $this->db->get()->row();
        // echo $this->db->last_query();
        // $data['query'] = $this->db->last_query();
        $lab_template_info = $this->db->select('lab_template_id, template_name, template_type')->from('lab_templates')->where('investigation_id =',$invInfo->investigation_id)->get()->row();
        $data['template_type'] = $lab_template_info->template_type;
        // echo $this->db->last_query();
        // $data['query1'] = $this->db->last_query();
        // Get lab template line items concern to lab_template_id
        // ->join('clinic_investigation_range CIR','LI.investigation_id = CIR.investigation_id','inner')
        $params = $this->db->distinct()
        ->select('LI.lab_template_line_item_id, LI.parent_investigation_id, LI.investigation_id, LI.parameter,CIR.remarks')
        ->from('lab_template_line_items LI')->join('clinic_investigation_range CIR','LI.investigation_id = CIR.investigation_id','inner')
        ->where('LI.lab_template_id =',$lab_template_info->lab_template_id)
        ->group_by('LI.lab_template_line_item_id')->get()->result();
        // echo $this->db->last_query();
        if(count($params)>0)
        {
            // echo $this->db->last_query();
            $i = 0;
            foreach($params as $value)
            {
                $prev = $this->db->query("select plri.value from patient_lab_reports plr,patient_lab_report_line_items plri where plr.patient_lab_report_id=plri.patient_lab_report_id and plr.appointment_id='".$invInfo->appointment_id."' and plr.patient_investigation_line_item_id='".$pil_id."' and plri.investigation_id='".$value->investigation_id."'")->row();
                // echo $this->db->last_query();        
                $data['parameters'][$i]['lab_template_line_item_id'] = $value->lab_template_line_item_id;
                $data['parameters'][$i]['parent_investigation_id'] = $value->parent_investigation_id;
                $data['parameters'][$i]['investigation_id'] = $value->investigation_id;
                $data['parameters'][$i]['parameter'] = $value->parameter;
                $data['parameters'][$i]['remarks'] = $value->remarks;
                $data['parameters'][$i]['value'] = ($prev->value == '')?'':$prev->value;
                $i++;
            }
        }
        else{
            $params = $this->db->distinct()
            ->select('LI.lab_template_line_item_id, LI.parent_investigation_id, LI.investigation_id, LI.parameter')
            ->from('lab_template_line_items LI')
            ->where('LI.lab_template_id =',$lab_template_info->lab_template_id)
            ->group_by('LI.lab_template_line_item_id')->get()->result();
            if(count($params)>0)
            {
                // echo $this->db->last_query();                                                                               
                $i = 0;
                foreach($params as $value)
                {
                    $prev = $this->db->query("select plri.value from patient_lab_reports plr,patient_lab_report_line_items plri where plr.patient_lab_report_id=plri.patient_lab_report_id and plr.appointment_id='".$invInfo->appointment_id."' and plri.investigation_id='".$value->investigation_id."'")->row();
                    // echo $this->db->last_query();        
                    $data['parameters'][$i]['lab_template_line_item_id'] = $value->lab_template_line_item_id;
                    $data['parameters'][$i]['parent_investigation_id'] = $value->parent_investigation_id;
                    $data['parameters'][$i]['investigation_id'] = $value->investigation_id;
                    $data['parameters'][$i]['parameter'] = $value->parameter;
                    $data['parameters'][$i]['remarks'] = $value->remarks;
                    $data['parameters'][$i]['value'] = ($prev->value == '')?'':$prev->value;
                    $i++;
                }
            }
        }
        $this->response(array("code"=>"200","message"=>"Investigations With Parameters","result"=>$data));
    }

    //Post for Investigations Parameters
    public function investigations_post()
    {
        $datetime = date("Y-m-d H:i:s");
        if(isset($_POST))
        {
            extract($_POST);
            // echo "<pre>"; 
            // print_r($_POST);
            // echo "</pre>";
            // exit();
            $appInfo = $this->db->select("clinic_id,doctor_id,patient_id,umr_no")->from("appointments")->where("appointment_id='".$appointment_id."'")->get()->row();
            $check = $this->db->query("select * from patient_lab_reports plr,patient_lab_report_line_items plri where plr.patient_lab_report_id=plri.patient_lab_report_id and plr.appointment_id='".$appointment_id."' and plr.patient_investigation_line_item_id='".$patient_investigation_line_item_id."'")->row();
            // echo $this->db->last_query();
            // exit;/
            if(count($check)>0)
            {
                $parameters = json_decode($parameters);
                $paramCount = count($parameters);

                for($i = 0;$i < $paramCount;$i++)
                {
                    $data1['patient_lab_report_id'] = $check->patient_lab_report_id;
                    $data1['lab_template_line_item_id'] = $parameters[$i]->lab_template_line_item_id;
                    $data1['parent_investigation_id'] = $parameters[$i]->parent_investigation_id;
                    $data1['investigation_id'] = $parameters[$i]->investigation_id;
                    $data1['value'] = $parameters[$i]->value; 
                    $data1['status'] = 1;
                    $data1['id'] = $this->Generic_model->updateData("patient_lab_report_line_items",$data1,array('patient_lab_reports_line_item_id'=>$check->patient_lab_reports_line_item_id));
                    unset($data1);
                }
            }
            else
            {
                $data['report_type'] = "out";
                $data['appointment_id'] = $appointment_id;
                $data['clinic_id'] = $appInfo->clinic_id;
                $data['doctor_id'] = $appInfo->doctor_id;
                $data['umr_no'] = $appInfo->umr_no;
                $data['patient_id'] = $appInfo->patient_id;
                $data['template_type'] = $template_type;
                $data['patient_investigation_line_item_id'] = $patient_investigation_line_item_id;
                $data['investigation_id'] = $investigation_id;
                $data['consultant_remark'] = $reviews;
                $data['created_by'] = $user_id;
                $data['created_date_time'] = $datetime;
                $data['modified_date_time'] = $datetime;
                $patient_lab_report_id = $this->Generic_model->insertDataReturnId("patient_lab_reports",$data);
                
                $parameters = json_decode($parameters);
                $paramCount = count($parameters);

                for($i = 0;$i < $paramCount;$i++)
                {
                    $data1['patient_lab_report_id'] = $patient_lab_report_id;
                    $data1['lab_template_line_item_id'] = $parameters[$i]->lab_template_line_item_id;
                    $data1['parent_investigation_id'] = $parameters[$i]->parent_investigation_id;
                    $data1['investigation_id'] = $parameters[$i]->investigation_id;
                    $data1['value'] = $parameters[$i]->value; 
                    $data1['status'] = 1;
                    $data1['id'] = $this->Generic_model->insertData("patient_lab_report_line_items",$data1);
                    unset($data1);
                }
                $data2['patient_investigation_line_item_id'] = $patient_investigation_line_item_id;
                $data2['investigation_id'] = $investigation_id;
                $data2['report_status'] = "RDY";
                $data2['report_entry_status'] = 1;
                $data2['report_entry_by'] = $user_id;
                $data2['created_by'] = $user_id;
                $data2['created_date_time'] = $datetime;
                $this->Generic_model->insertData("investigation_status",$data2);
            }

            $param = "Successfully Saved.";
            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));

        }
        else
        {
            $data = "UnAuthorized Access";    
            $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$data));
        }        
    }

    // Lab Reports based on investigation_id
    public function investigationReports_post()
    {
        if(isset($_POST) && $_POST['plr_id']!="")
        {
            $plr_id = substr($_POST['plr_id'], 1, -1);
            $idCount = count(explode(",",$plr_id));
            $plr_id = explode(",", $plr_id);
            $this->load->library('M_pdf');

        if($idCount > 0)
        {
            $i = 0;
            foreach ($plr_id as $value) 
            {
                $this->db->select("*");
                $this->db->from("patient_lab_reports");
                $this->db->where("patient_lab_report_id='".$value."'");
                $plrInfo = $this->db->get()->row();
                echo $plrInfo;
                // if()
                if(count($plrInfo)>0)
                {
                    $this->db->select("*");
                    $this->db->from("patient_investigation p");
                    $this->db->join("patient_investigation_line_items pl","p.patient_investigation_id=pl.patient_investigation_id");
                    $this->db->where("pl.patient_investigation_line_item_id='".$pil_id."'");
                    $invInfo = $this->db->get()->row();

                    $data['appointments'] = $this->db->select("a.*, c.clinic_id,c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,c.clinic_id,p.title, p.first_name as pname, p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,p.allergy,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")->from("appointments a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p","a.patient_id = p.patient_id","left")->join("doctors d","a.doctor_id = d.doctor_id","left")->join('department dep','d.department_id = dep.department_id')->where("a.appointment_id='" . $plrInfo->appointment_id . "'")->get()->row();
                    // Get Template type
                    $data['template_type'] = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$plrInfo->investigation_id));

                    // Get the lab report id 
                    $data['patient_lab_report_id'] = $value;

                    // Get the Consultant Remark
                    $data['consultant_remark'] = $plrInfo->consultant_remark;

                    // Get the medical test name
                    $data['test_name'] = $this->Generic_model->getFieldValue('investigations','investigation',array('investigation_id' => $plrInfo->investigation_id));

                    // Get the investigation results w.r.to investigation id
                    $data['lab_results'] = $this->db->query("select * from patient_lab_reports plr,patient_lab_report_line_items plri where plr.patient_lab_report_id=plri.patient_lab_report_id and plr.patient_lab_report_id='".$value."' order by plri.position ASC")->result_array();
                    // $data['lab_results'] = $this->db->select('PLR.consultant_remark, LI.patient_lab_reports_line_item_id, LI.investigation_id, LI.template_type, LI.value, LI.remarks, CI.clinic_investigation_id, CI.clinic_id, CIR.low_range, CIR.high_range, CIR.units, CIR.method, CI.other_information, I.investigation, I.item_code')->from('patient_lab_report_line_items LI')->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','inner')->join('clinic_investigation_range CIR','CI.investigation_id = CIR.investigation_id','inner')->join('investigations I','LI.investigation_id = I.investigation_id','inner')->join('patient_lab_reports PLR','LI.patient_lab_report_id = PLR.patient_lab_report_id')->where('LI.parent_investigation_id =', $plrInfo->investigation_id)->where('LI.patient_lab_report_id =',$data['patient_lab_report_id'])->get()->result_array();
                    $data1[$i] = $data['lab_results'];

                    if($i > 0 && count($data['lab_results']) > 0){
                        $data['page_break'] = 1;
                    }
                    // PDF Settings
                    $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='".$data['appointments']->clinic_id."'")->get()->row();
                    
                    $data1[$i] = $data;

                    $html = $this->load->view('lab/report_pdf_android',$data,true);     
                    // $this->m_pdf->pdf->AddPage();
                    $pdfFilePath = $data['appointments']->appointment_id.time().".pdf";
                    $stylesheet = file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
                    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
                    $this->m_pdf->pdf->autoScriptToLang = true;
                    $this->m_pdf->pdf->autoLangToFont = true;

                    $this->m_pdf->pdf->shrink_tables_to_fit = 1;
                    $this->m_pdf->pdf->setAutoTopMargin = "stretch";
                    $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
                    $this->m_pdf->pdf->defaultheaderline = 0;
                    // echo $html;

                    $this->m_pdf->pdf->WriteHTML($stylesheet,1);
                    $this->m_pdf->pdf->WriteHTML($html,2);
                }
                else
                {
                    continue;
                }

               
                $i++;
                
            }
            // echo "<pre>";
            // print_r($data1);
            // echo "</pre>";
            // exit;
            $this->m_pdf->pdf->Output("./uploads/lab_reports/".$pdfFilePath, "F");
            

            $param['filepath'] = base_url("uploads/lab_reports/".$pdfFilePath);
            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
            
        }
        else
        {
            $param = "Error Occurred";
            $this->response(array('code'=>'200','message'=>'Error Occurred','result'=>$param));
        }
        
    }
        else
        {
            $data = "UnAuthorized Access";    
            $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$data));
        }
        
    }

}
?>