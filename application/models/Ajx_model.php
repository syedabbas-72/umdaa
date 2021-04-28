<?php

class Ajx_model extends CI_Model{ 
    function lookup($keyword){ 
        $this->db->select('*')->from('drug'); 
        $this->db->like('trade_name',$keyword,'after'); 
        $this->db->or_like('category',$keyword,'after'); 
        $query = $this->db->get();     
        return $query->result(); 
    } 
}

 ?>