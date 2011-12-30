<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user_profile_permission_model
 *
 * @author rodrigo
 */
class user_profile_permission_model extends CI_Model 
{

  function __construct()
  {
	  parent::__construct();
  }
  
  function save($form_data)
  {
	  $this->db->insert('USER_PROFILE_PERMISSION', $form_data);

	  if ($this->db->affected_rows() == '1')
	  {
		  return TRUE;
	  }

	  return FALSE;
  }

  function delete($form_data)
  {
	  $this->db->delete('USER_PROFILE_PERMISSION', $form_data);

	  if ($this->db->affected_rows() == '1')
	  {
		  return TRUE;
	  }

	  return FALSE;
  }
  
}

