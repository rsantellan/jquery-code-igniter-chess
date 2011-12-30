<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of menu
 *
 * @author rodrigo
 */
class menu extends MY_Controller{
  
  function  __construct()  {
	parent::__construct();
  }
  
  
  function menu()
  {
	$this->load->view("menu", $this->data);
  }
  
}

