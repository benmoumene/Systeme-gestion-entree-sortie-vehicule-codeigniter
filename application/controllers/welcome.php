<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start();
class Welcome extends CI_Controller {

   public function __construct() 
        {
            parent::__construct();

            $employee_code=$this->session->userdata('employee_code');  
			$employee_name=$this->session->userdata('employee_name');
			$employee_email=$this->session->userdata('employee_email');
			$department=$this->session->userdata('department');
			
            if($employee_code != NULL && $employee_name != NULL && $employee_email != NULL && $department != NULL)
            {
                    redirect('access', 'refresh');
            }
       }

	 
	public function index()
	{
		$data['title']='Viyellatex';
		$this->load->view('login');
	}
	
	public function login()
	{
		$data['username'] = $this->input->post('username');
		$data['password'] = $this->input->post('password');
		
		$result=$this->welcome_model->login_check($data);
		
	       if($result)
               {
                   $data1['employee_code']=$result->employee_code;
                   $data1['employee_name']=$result->employee_name;
                   $data1['employee_email']=$result->employee_email;
		           $data1['department']=$result->department;
                   $this->session->set_userdata($data1);
				   
                   redirect('access','refresh');
                   
               }
               else{
                   $data['exception']='Your User Name/Password is Invalid!';
                   $this->session->set_userdata($data);

                   redirect('welcome', 'refresh');
               }
			   
			   
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */