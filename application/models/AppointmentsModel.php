<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class AppointmentsModel extends CI_Model{

    public function get_app_count($clinic_id = '') 
	{
        if($clinic_id == "0")
        {
            return $this->db->query("select * from appointments where appointment_date='".date('Y-m-d')."'")->num_rows();
        }
        else
        {
             return $this->db->query("select * from appointments where clinic_id='$clinic_id' and appointment_date='".date('Y-m-d')."'")->num_rows();
        }
        
    }

    public function getTodayAppointments($limit, $start, $clinic_id='') 
	{
        if($clinic_id == "0")
        {
            $data = $this->db->query("select * from appointments where appointment_date='".date('Y-m-d')."' order by appointment_id DESC  LIMIT ".$start.",".$limit)->result();
        }
        else
        {
            $data = $this->db->query("select * from appointments where clinic_id='$clinic_id' and appointment_date='".date('Y-m-d')."' order by appointment_id DESC LIMIT ".$start.",".$limit)->result();
        }
        return $data;
    }

    public function get_search_count($clinic_id = '', $search = '', $filters = '') 
	{
        if($clinic_id == "0")
        {
            if($filters == ""){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and p.mobile='".DataCrypt($search,'encrypt')."' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->num_rows();
                }
                else{
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and concat(p.first_name,' ',p.last_name) like '%".urldecode($search)."%' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->num_rows();
                }
            }
            else{
                $bookingType = implode(',',$search);
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and a.booking_type IN (".$search.") and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->num_rows();
                }
                else{
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and a.booking_type IN (".$search.") and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->num_rows();
                }
            }
            return $data;
        }
        else
        {
            if($filters == ""){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and p.mobile='".DataCrypt($search,'encrypt')."' and a.clinic_id='".$clinic_id."' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->num_rows();
                }
                else{
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and concat(p.first_name,' ',p.last_name) like '%".urldecode($search)."%' and a.clinic_id='".$clinic_id."' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->num_rows();
                }
            }
            else{
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and a.booking_type IN (".$search.") and a.clinic_id='".$clinic_id."' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->num_rows();
                }
                else{
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and a.booking_type IN (".$search.") and a.clinic_id='".$clinic_id."' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->num_rows();
                }
            }
            return $data;
        }
        
    }

    public function getAppointmentsSearch($limit, $start, $clinic_id='', $search, $filters = '') 
	{
        if($clinic_id == "0")
        {
            if($filters == ""){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and p.mobile='".DataCrypt($search,'encrypt')."' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->result();
                }
                else{
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and concat(p.first_name,' ',p.last_name) like '%".urldecode($search)."%' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->result();
                }
            }
            else{
                $bookingType = implode(',',$search);
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and a.booking_type IN (".$search.") and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->result();
                }
                else{
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and a.booking_type IN (".$search.") and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->result();
                }
            }
            return $data;
        }
        else
        {
            if($filters == ""){
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and p.mobile='".DataCrypt($search,'encrypt')."' and a.clinic_id='".$clinic_id."' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->result();
                }
                else{
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and concat(p.first_name,' ',p.last_name) like '%".urldecode($search)."%' and a.clinic_id='".$clinic_id."' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->result();
                }
            }
            else{
                if(preg_match('/^[0-9]{10}+$/', $search)){
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and a.booking_type IN (".$search.") and a.clinic_id='".$clinic_id."' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->result();
                }
                else{
                    $data = $this->db->query("select a.* from appointments a,patients p where a.patient_id=p.patient_id and a.booking_type IN (".$search.") and a.clinic_id='".$clinic_id."' and a.appointment_date='".date('Y-m-d')."' order by appointment_id DESC ")->result();
                }
            }
            return $data;
        }
    }
}
?>