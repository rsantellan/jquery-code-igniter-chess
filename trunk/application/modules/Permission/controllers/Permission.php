<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Permission
 *
 * @author rodrigo
 */
class Permission extends MY_Controller {

  function __construct() {
	parent::__construct();
	$this->load->library('form_validation');
	$this->load->database();
	$this->load->helper('form');
	$this->load->helper('url');
	$this->load->model('permission_model');
  }

  function index() {
	$this->form_validation->set_rules('name', 'NAME', 'required|max_length[32]');
	$this->form_validation->set_rules('permission_groups_id', 'PERMISSION_GROUPS_ID', 'required|max_length[9]');
	$this->form_validation->set_rules('description', 'DESCRIPTION', 'required|max_length[32]');

	$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');



	if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
	  $this->load->view('permission_view');
	} else { // passed validation proceed to post success logic
	  // build array for the model
	  $form_data = array(
		  'NAME' => set_value('name'),
		  'PERMISSION_GROUPS_ID' => set_value('permission_groups_id'),
		  'DESCRIPTION' => set_value('description')
	  );

	  // run insert model to write data to db

	  if ($this->permission_model->SaveForm($form_data) == TRUE) { // the information has therefore been successfully saved in the db
		redirect('permission/success');   // or whatever logic needs to occur
	  } else {
		echo 'An error occurred saving your information. Please try again later';
		// Or whatever error handling is necessary
	  }
	}
  }

  function success() {
	echo 'this form has been successfully submitted with all validation being passed. All messages or logic here. Please note
			sessions have not been used and would need to be added in to suit your app';
  }

}