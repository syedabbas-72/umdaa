<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class PatientsModel extends CI_Model{
    public function get_count($clinic_id = '',$where='') 
	{
        if($clinic_id == "0")
        {
            if($where == "Doctors"){
                return $this->db->query("select * from patients where patient_id!=created_by")->num_rows();
            }
            else if($where == "Citizens"){
                return $this->db->query("select * from patients where patient_id=created_by")->num_rows();
            }
        }
        else
        {
            if($where == "Doctors"){
                return $this->db->query("select * from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and p.patient_id!=p.created_by and cd.clinic_id='$clinic_id' group by cd.patient_id")->num_rows();
            }
            else if($where == "Citizens"){
                return $this->db->query("select * from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and p.patient_id=p.created_by and cd.clinic_id='$clinic_id' group by cd.patient_id")->num_rows();
            }
        }
    }

    public function getPatients($limit, $start, $clinic_id='',$where) 
	{
        if($clinic_id == "0")
        {
            if($where == "Doctors"){
                $data = $this->db->query("select * from patients where patient_id!=created_by  order by patient_id DESC  LIMIT ".$start.",".$limit)->result();
            }
            else if($where == "Citizens"){
                $data = $this->db->query("select * from patients where patient_id=created_by order by patient_id DESC  LIMIT ".$start.",".$limit)->result();
            }
        }
        else
        {
            if($where == "Doctors"){
                $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and p.patient_id!=p.created_by and cd.clinic_id='$clinic_id' group by cd.patient_id order by cd.patient_id DESC LIMIT ".$start.",".$limit)->result();
            }
            else if($where == "Citizens"){
                $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and p.patient_id=p.created_by and cd.clinic_id='$clinic_id' group by cd.patient_id order by cd.patient_id DESC LIMIT ".$start.",".$limit)->result();
            }
            // $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and cd.clinic_id='$clinic_id' group by cd.patient_id order by cd.patient_id DESC LIMIT ".$start.",".$limit)->result();
        }
        return $data;
    }

    public function get_search_count($clinic_id = '', $search = '', $where) 
	{
        if($clinic_id == "0")
        {
            if($where == "Doctors"){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select * from patients where mobile='".DataCrypt($search, 'encrypt')."' and patient_id!=created_by ")->num_rows();
                }
                else{
                    $data = $this->db->query("select * from patients where first_name like '%".urldecode($search)."%' or last_name like '%".urldecode($search)."%' and patient_id!=created_by")->num_rows();
                }
            }
            else if($where == "Citizens"){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select * from patients where mobile='".DataCrypt($search, 'encrypt')."' and patient_id=created_by ")->num_rows();
                }
                else{
                    $data = $this->db->query("select * from patients where first_name like '%".urldecode($search)."%' or last_name like '%".urldecode($search)."%' and patient_id=created_by ")->num_rows();
                }
            }
        }
        else
        {
            if($where == "Doctors"){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and cd.clinic_id='$clinic_id' and  p.mobile='".DataCrypt($search, 'encrypt')."' and p.patient_id!=p.created_by group by cd.patient_id ")->num_rows();
                }
                else{
                    $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and cd.clinic_id='$clinic_id' and (p.first_name like '%".urldecode($search)."%' or p.last_name like '%".urldecode($search)."%') and p.patient_id!=p.created_by group by cd.patient_id")->num_rows();
                }
            }
            else if($where == "Citizens"){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and cd.clinic_id='$clinic_id' and  p.mobile='".DataCrypt($search, 'encrypt')."' and p.patient_id=p.created_by group by cd.patient_id ")->num_rows();
                }
                else{
                    $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and cd.clinic_id='$clinic_id' and (p.first_name like '%".urldecode($search)."%' or p.last_name like '%".urldecode($search)."%') and p.patient_id=p.created_by group by cd.patient_id ")->num_rows();
                }
            }
        }
        return $data;        
    }
    
    public function getPatientsSearch($limit, $start, $clinic_id='', $search,$where) 
	{
        if($clinic_id == "0")
        {
            if($where == "Doctors"){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select * from patients where mobile='".DataCrypt($search, 'encrypt')."' and patient_id!=created_by LIMIT ".$start.",".$limit)->result();
                }
                else{
                    $data = $this->db->query("select * from patients where first_name like '%".urldecode($search)."%' or last_name like '%".urldecode($search)."%' and patient_id!=created_by LIMIT ".$start.",".$limit)->result();
                }
            }
            else if($where == "Citizens"){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select * from patients where mobile='".DataCrypt($search, 'encrypt')."' and patient_id=created_by LIMIT ".$start.",".$limit)->result();
                }
                else{
                    $data = $this->db->query("select * from patients where first_name like '%".urldecode($search)."%' or last_name like '%".urldecode($search)."%' and patient_id=created_by LIMIT ".$start.",".$limit)->result();
                }
            }
        }
        else
        {
            if($where == "Doctors"){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and cd.clinic_id='$clinic_id' and  p.mobile='".DataCrypt($search, 'encrypt')."' and p.patient_id!=p.created_by group by cd.patient_id LIMIT ".$start.",".$limit)->result();
                }
                else{
                    $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and cd.clinic_id='$clinic_id' and (p.first_name like '%".urldecode($search)."%' or p.last_name like '%".urldecode($search)."%') and p.patient_id!=p.created_by group by cd.patient_id LIMIT ".$start.",".$limit)->result();
                }
            }
            else if($where == "Citizens"){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and cd.clinic_id='$clinic_id' and  p.mobile='".DataCrypt($search, 'encrypt')."' and p.patient_id=p.created_by group by cd.patient_id LIMIT ".$start.",".$limit)->result();
                }
                else{
                    $data = $this->db->query("select p.* from patients p,clinic_doctor_patient cd where cd.patient_id=p.patient_id and cd.clinic_id='$clinic_id' and (p.first_name like '%".urldecode($search)."%' or p.last_name like '%".urldecode($search)."%') and p.patient_id=p.created_by group by cd.patient_id LIMIT ".$start.",".$limit)->result();
                }
            }
        }
        // echo $this->db->last_query();
        // return $this->db->last_query();
        return $data;
    }
}
?>