<?php

class permission_model extends CI_Model {

  function __construct() {
	parent::__construct();
  }

  // --------------------------------------------------------------------

  /**
   * function SaveForm()
   *
   * insert form data
   * @param $form_data - array
   * @return Bool - TRUE or FALSE
   */
  function SaveForm($form_data) {
	$this->db->insert('PERMISSION', $form_data);

	if ($this->db->affected_rows() == '1') {
	  return TRUE;
	}

	return FALSE;
  }

  public function retrieveUserPermissions($permisssion_group_id) {
	$this->adodb->connect();
	$sql = "select * from permission";
	$resultset = $this->adodb->execute($sql);
	$this->adodb->disconnect();
	var_dump($resultset);
  }

  


}
