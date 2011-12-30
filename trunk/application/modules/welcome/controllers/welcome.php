<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller {

  
  
  function  __construct()  {
	parent::__construct();
	//modules::run('login/is_logged_in'); 
	$this->loadI18n(get_class($this), $this->lenguage, FALSE, TRUE, '', strtolower(get_class($this)));
	//$this->data['myLang'] = array('a', 'b');
  }
		
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
	  
	   //$this->load->model('user_adm_model');
	   
	   //$this->data['resultados'] = $this->user_adm_model->getUserAdm();
	   /*
		$this->load->model("permission_model");	
	  
	   $this->adodb->connect();

	   $permission = new Permission();
	   var_dump($permission);
		$sql = "select * from permission";
		$resultset = $this->adodb->execute($sql);


		$this->adodb->disconnect();
		//var_dump($resultset);
		*/
	    $this->data["content"] = 'welcome';
		$this->data["dashboard"] = true;
		$this->load->view('layout', $this->data);
		
	}
	
	public function newForm()
	{
	  
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
