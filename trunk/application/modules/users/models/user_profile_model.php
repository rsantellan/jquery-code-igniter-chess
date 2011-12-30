<?php

class User_profile_model extends CI_Model {

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
	
	$query = $this->db->query("select SEQ_PERMISSION_GROUPS.NEXTVAL from dual");
	if ($query->num_rows() == 0)
	{
	  throw new Exception("Error en la base de datos", 1500);
	}
	$query_array = array_pop($query->result_array());
	$new_id = (int)$query_array["NEXTVAL"];
	$form_data['USER_PROFILE_ID'] = $new_id;
	
	$this->db->insert('USER_PROFILE', $form_data);
	if ($this->db->affected_rows() == '1') {
	  return $new_id;
	}

	return 0;
  }
  
  function updateForm($form_data) {
	if(!isset($form_data["USER_PROFILE_ID"]))
	{
	 throw new Exception("Exception de datos", 150);
	}
	$id = $form_data["USER_PROFILE_ID"];
	unset($form_data["USER_PROFILE_ID"]);
	$this->db->where("USER_PROFILE_ID", $id);
	$this->db->update('USER_PROFILE', $form_data);

	if ($this->db->affected_rows() == '1') {
	  return $id;
	}

	return 0;
  }
  
  function delete($id)
  {
	
	$this->db->trans_start();
	$this->db->where("USER_PROFILE_ID", $id);
	$this->db->delete('USER_PROFILE_PERMISSION');
	
	$this->db->where("USER_PROFILE_ID", $id);
	$this->db->delete('USER_PROFILE');
	$trans_ok = $this->db->trans_complete();
	return $trans_ok;
	
	if ($this->db->affected_rows() == '1') {
	  return TRUE;
	}
	return FALSE;
  }

  public function getUserProfileList($limit = 15, $page = 1, $order_by = false, $order = false) {

	$offset = -1;
	if ($page > 1) {
	  $offset = $limit * $page;
	}

	$sqlCount = "select count(user_profile_id) from user_profile";

	$sql = "select 
			  up.user_profile_id, 
			  up.name, 
			  up.description, 
			  (SELECT COUNT(*) FROM USER_PROFILE_PERMISSION WHERE USER_PROFILE_ID = UP.USER_PROFILE_ID) AS PERMISSION_COUNT,
			  (SELECT COUNT(*) FROM USER_ADM WHERE USER_PROFILE_ID = UP.USER_PROFILE_ID) AS USER_COUNT
			from 
			  user_profile up ";
	if ($order_by != false) {
	  if ($order == false)
		$order = "DESC";
	  $sql .= " ORDER BY " . $order_by . " " . $order;
	}
	$ci = & get_instance();
	$ci->adodb->connect();
	$resultsetCount = $ci->adodb->execute($sqlCount);
	$resultset = $ci->adodb->selectLimit($sql, $limit, false, $offset);
	$ci->adodb->disconnect();
	$salida = array();
	$aux = $resultsetCount->getArray();
	$salida["count"] = $aux[0][0];
	$salida["datos"] = $resultset;
	$salida["headers"] = $this->headers();
	return $salida;
  }

  public function headers()
  {
	$list = array();
	$aux = array();
	$aux["col"] = "user_profile_id";
	$aux["name"] = "user_profile_id";
	$list[] = $aux;
	$aux["col"] = "name";
	$aux["name"] = "name";
	$list[] = $aux;
	$aux["col"] = "description";
	$aux["name"] = "description";
	$list[] = $aux;
	$aux["col"] = "PERMISSION_COUNT";
	$aux["name"] = "PERMISSION_COUNT";
	$list[] = $aux;
	$aux["col"] = "USER_COUNT";
	$aux["name"] = "USER_COUNT";
	$list[] = $aux;
	return $list;
  }
  
  public function getUserProfilePermissionsList($user_profile_id, $limit = 15, $page = 1, $order_by = false, $order = false) {

	$offset = -1;
	if ($page > 1) {
	  $offset = $limit * $page;
	}

	$sqlCount = "
	  select 
		count(p.permission_id)
	  from 
		permission p, user_profile_permission up 
	  where 
		p.permission_id = up.permission_id and up.user_profile_id = :id";

	$sql = "
	  select 
		p.permission_id, 
		p.name, 
		p.description 
	  from 
		permission p, user_profile_permission up 
	  where 
		p.permission_id = up.permission_id and up.user_profile_id = :id";
	if ($order_by != false) {
	  if ($order == false)
		$order = "DESC";
	  $sql .= " ORDER BY " . $order_by . " " . $order;
	}
	$data = array("id" => $user_profile_id);
	$ci = & get_instance();
	$ci->adodb->connect();
	$resultsetCount = $ci->adodb->execute($sqlCount, $data );
	$resultset = $ci->adodb->selectLimit($sql, $limit, $data, $offset);
	$ci->adodb->disconnect();
	$salida = array();
	$aux = $resultsetCount->getArray();
	$salida["count"] = $aux[0][0];
	$salida["datos"] = $resultset;
	$salida["headers"] = $this->userProfilePermissionsHeaders();
	return $salida;
  }
  
  
  
  private function userProfilePermissionsHeaders()
  {
	$list = array();
	$aux = array();
	$aux["col"] = "permission_id";
	$aux["name"] = "permission_id";
	$list[] = $aux;
	$aux["col"] = "name";
	$aux["name"] = "name";
	$list[] = $aux;
	$aux["col"] = "description";
	$aux["name"] = "description";
	$list[] = $aux;
	return $list;	
  }
  
  public function retrieveAllUsedProfilesIds($user_profile_id)
  {
	
	$sql = "select permission_id from user_profile_permission where user_profile_id = :id";
	$data = array("id" => $user_profile_id);
	$ci = & get_instance();
	$ci->adodb->connect();
	$resultset = $ci->adodb->execute($sql, $data);
	$ci->adodb->disconnect();
	$salida = array();
	foreach($resultset as $row)
	{
	  $num = (int) $row["PERMISSION_ID"];
	  $salida[$num] = $num;
	}
	return $salida;
  }
  
  
  function getUserProfile($id)
  {
	$this->db->where('USER_PROFILE_ID', $id);
	$query = $this->db->get('USER_PROFILE');
	return $query->row(0);
  }  
}
