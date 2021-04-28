<?php
//error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar_blocking extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $clinic_id = $this->session->userdata('clinic_id');
        $condition = '';
        if ($clinic_id != 0)
        //$condition = "b.clinic_id=".$clinic_id;
            $data['cb_list'] = $this->Generic_model->getAllRecords('calendar_blocking', $condition = '', $order = '');
        //$data['doctor_list']=$this->Generic_model->getAllRecords('doctors', $condition='', $order='');
        $data['view'] = 'calendar_blocking/cb_list';
        $this->load->view('layout', $data);
        
        
    }
    
    public function add()
    {
        
        if ($this->input->post('submit')) {
            $dates = $this->input->post('blockdates');
            
            $data['dates'] = trim($dates);
            
            
            $data['doctor_id'] = $this->input->post('doctor_id');
            $data['clinic_id'] = $this->input->post('clinic_name');
            
            
            $this->Generic_model->insertData('calendar_blocking', $data);
            
            redirect('calendar_blocking');
            
        } else {
            $clinic_id = $this->session->userdata('clinic_id');
            $condition = '';
            if ($clinic_id != 0)
            //$condition = "b.clinic_id=".$clinic_id;
                $data['clinic_list'] = $this->Generic_model->getAllRecords('clinics', $condition, $order = '');
            //$data['doctor_list']=$this->Generic_model->getAllRecords('doctors', $condition='', $order='');
            $data['view'] = 'calendar_blocking/cb_add';
            $this->load->view('layout', $data);
        }
        
    }
    
    public function calendar_blocking()
    {
        $data['clinics'] = $this->db->select('clinic_id,clinic_name')->from('clinics')->order_by('clinic_name')->get()->result();
        $result          = $this->db->select('*')->from('clinic_doctor_weekday_slots')->get()->result_array();
        foreach ($result as $row) {
            $data[] = array(
                'id' => $row["clinic_doctor_weekday_slot_id"],
                'title' => date('h:i A', strtotime($row["from_time"])) . ' - ' . date('h:i A', strtotime($row["to_time"])),
                'dow' => '[' . $row["clinic_doctor_weekday_id"] . ']'
            );
        }
        
        $data['json'] = $data;
        
        $data['view'] = 'clinic_doctor/clinic_doctor_calendar';
        $this->load->view('layout', $data);
    }
    
    
    
    
    function index2()
    {
        $data['clinics'] = $this->db->select('clinic_id,clinic_name')->from('clinics')->order_by('clinic_name')->result();
        $result          = $this->db->select('*')->from('clinic_doctor_weekday_slots')->get()->result_array();
        foreach ($result as $row) {
            $data[] = array(
                'id' => $row["clinic_doctor_weekday_slot_id"],
                'title' => date('h:i A', strtotime($row["from_time"])) . ' - ' . date('h:i A', strtotime($row["to_time"])),
                'dow' => '[' . $row["clinic_doctor_weekday_id"] . ']'
            );
        }
        
        $data['json'] = $data;
        
        $data['view'] = 'clinic_doctor/clinic_doctor_calendar';
        $this->load->view('layout', $data);
        
    }
    
    // public function getDoctors() {
    //        $clinic_id = $_POST['clinic_id'];
    //        $clinic_doctor = $this->db->query("SELECT cd.*,c.clinic_name,d.first_name,d.doctor_id,d.last_name FROM clinic_doctor cd 
    //     left join clinics c on c.clinic_id=cd.clinic_id 
    //     left join doctors d on d.doctor_id=cd.doctor_id
    //     WHERE cd.clinic_id='" . $clinic_id . "' group by cd.doctor_id ")->result();
    //        $docters_list = '<div class="row col-md-12">
    //       <div class="col-md-12">
    //         <div class="form-group">
    //           <label for="pincode" class="col-form-label">SELECT DOCTOR</label>';
    //        $docters_list .= '<select name="doctor_id" id="doctor_id" class="form-control">
    //                         <option value=""> SELECT DOCTOR </option>';
    //        foreach ($clinic_doctor as $key => $value) {
    //            $docters_list .= '<option value="' . $value->doctor_id . '">' . $value->first_name .' '.$value->last_name. '</option>';
    //        }
    //        $docters_list .= '</select></div>';
    //        $docters_list .= '</div>';
    //        echo $docters_list;
    //    }
    
    // public function clinic_doctor_update($id){
    // $clinic_id = $this->session->userdata('clinic_id');
    // $cond = '';
    // if($clinic_id!=0){
    //     //$cond = "clinic_id=".$clinic_id;        
    // }
    //     if($this->input->post('submit')){
    //         $data['clinic_id']=$this->input->post('clinic_name');
    //     $data['doctor_id']=$this->input->post('doctor_name');
    //     $data['modified_date_time']=date('Y-m-d H:i:s');
    //         $this->Generic_model->updateData('doctor_clinics', $data, array('clinic_doctor_id'=>$id));
    //         redirect('clinic_doctor');
    //     }else{
    //      $data['clinic_doctor_list']=$this->db->query('select * from doctor_clinics where clinic_doctor_id='.$id)->row();
    //      $data['clinic_list']=$this->Generic_model->getAllRecords('clinics', $cond, $order='');
    //     $data['doctor_list']=$this->Generic_model->getAllRecords('doctors', $cond, $order='');
    //     $data['view'] = 'clinic_doctor/clinic_doctor_edit';
    //     $this->load->view('layout', $data);
    //    }
    // }
    
    
    public function delete()
    {
        $this->db->query('delete from clinic_doctor_weekday_slots where clinic_doctor_weekday_slot_id = ' . $this->input->post('id'));
    }
    
    
    
    
}
?>