<?php

class Login extends MY_Controller {
	
  public function __construct() {
	parent::__construct();
	
	$this->loadI18n(get_class($this), $this->lenguage, FALSE, TRUE, '', strtolower(get_class($this)));
	//$this->load->library('Session');
	
  }

  private $errores = false;

  function index()
	{
		$data['main_content'] = 'login_form';
		$data["errores"] = $this->errores;
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->view('login_form', $data);		
	}
	
	function validate_credentials()
	{		
		//$this->output->enable_profiler(TRUE);
		$this->load->model('users/user_adm_model');
		$this->load->helper('url'); 
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
		$this->errores = false;
		if($this->form_validation->run() == FALSE)
		{
			$this->errores = true;
			$this->index();
		}
		else
		{
		  $user = $this->user_adm_model->validate();
		
		  if(!is_null($user)) // if the user's credentials validated...
		  {
			
			  $data = array(
				  'username' => $user->getUserName(),
				  'is_logged_in' => true
			  );
			  $this->session->set_userdata($data);
			  $user->autoComplete();
			  $this->session->set_userdata('permissions', serialize($user->getPermissions()));
			  $this->session->set_userdata('permissions_groups', serialize($user->getPermissionsGroups()));
			  redirect('welcome');
		  }
		  else // incorrect username or password
		  {
			$this->errores = true;
			$this->index();
		  }
		  
		}
		
		//var_dump('aca');die;
	}	
	
	function signup()
	{
		$data['main_content'] = 'signup_form';
		$this->load->view('includes/template', $data);
	}
	
	function create_member()
	{
		$this->load->library('form_validation');
		
		// field name, error message, validation rules
		$this->form_validation->set_rules('first_name', 'Name', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');
		
		
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('signup_form');
		}
		
		else
		{			
			$this->load->model('membership_model');
			
			if($query = $this->membership_model->create_member())
			{
				$data['main_content'] = 'signup_successful';
				$this->load->view('includes/template', $data);
			}
			else
			{
				$this->load->view('signup_form');			
			}
		}
		
	}
	
	function logout()
	{
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('is_logged_in');
		$this->session->sess_destroy();
		$this->index();
	}	
	
	function is_logged_in()
	{
		$is_logged_in = $this->session->userdata('is_logged_in');
		if(!isset($is_logged_in) || $is_logged_in != true)
		{
			redirect('login');
			$this->load->helper('url');
			
			$this->load->view("notLogged");
			die();		
		}		
		//log_message('MY_USER', $sql);
	}
	
	function cp()
	{
		if( $this->session->userdata('username') )
		{
			// load the model for this controller
			$this->load->model('membership_model');
			// Get User Details from Database
			$user = $this->membership_model->get_member_details();
			if( !$user )
			{
				// No user found
				return false;
			}
			else
			{
				// display our widget
				$this->load->view('user_widget', $user);
			}			
		}
		else
		{
			// There is no session so we return nothing
			return false;
		}
	}
	

}