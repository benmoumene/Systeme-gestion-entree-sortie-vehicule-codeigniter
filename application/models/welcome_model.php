<?php

class Welcome_model extends CI_Model {
    //put your code here
    
    public function login_check($data)
    {
	$emp_master_db = $this->load->database('emp_master', TRUE);
		
        $emp_master_db->select('*');
        $emp_master_db->from('tb_employee_master');
        $emp_master_db->where('employee_code', $data['username']);
        $emp_master_db->where('password', $data['password']);
        $query_result=$emp_master_db->get();
        $result=$query_result->row();
        
        return $result;
				
		
        /*$this->db->select('*');
        $this->db->from('tb_user');
        $this->db->where('user_name', $data['username']);
        $this->db->where('user_password', md5($data['password']));
        $query_result=$this->db->get();
        $result=$query_result->row();
        
        return $result;*/
		
    }
    
}

?>