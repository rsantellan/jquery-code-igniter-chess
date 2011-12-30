<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user_adm_model
 * @package models
 * @author rodrigo
 */
class user_adm_model extends CI_Model {

  function validate() {
	$this->db->where('USER_ADM_ID', $this->input->post('username'));
	$this->db->where('PASSWORD', md5($this->input->post('password')));
	$query = $this->db->get('USER_ADM');
	if ($query->num_rows == 1) {
	  $list = $query->row_array();
	  $user = new MyUser();

	  $user->setUserAdminId($list["USER_ADM_ID"]);
	  $user->setUserName($list["USER_NAME"]);
	  $user->setProfile($list["USER_PROFILE_ID"]);
	  $user->setStatus($list["STATUS"]);
	  $user->setExpireDate($list["EXPIRE_DATE"]);
	  $user->setErrorCount($list["ERRORCOUNT"]);
	  //$user->autoComplete();
	  return $user;
	}
	return null;
  }

  function getUser($user_adm_id)
  {
	$this->db->where('USER_ADM_ID', $user_adm_id);
	$query = $this->db->get('USER_ADM');
	return $query->row(0);
  }
  
  public function headers()
  {
	$list = array();
	$aux = array();
	$aux["col"] = "USER_ADM_ID";
	$aux["name"] = "USER_ADM_ID";
	$list[] = $aux;
	$aux["col"] = "USER_PROFILE_ID";
	$aux["name"] = "USER_PROFILE_ID";
	$list[] = $aux;
	$aux["col"] = "USER_NAME";
	$aux["name"] = "USER_NAME";
	$list[] = $aux;
	$aux["col"] = "STATUS";
	$aux["name"] = "STATUS";
	$list[] = $aux;
	$aux["col"] = "EXPIRE_DATE";
	$aux["name"] = "EXPIRE_DATE";
	$list[] = $aux;
	$aux["col"] = "ERRORCOUNT";
	$aux["name"] = "ERRORCOUNT";
	$list[] = $aux;
	$aux["col"] = "LAST_ACCESS";
	$aux["name"] = "LAST_ACCESS";
	$list[] = $aux;
	/*
	$aux["col"] = "EMAIL";
	$aux["name"] = "EMAIL";
	$list[] = $aux;
	$aux["col"] = "PASSWORD";
	$aux["name"] = "PASSWORD";
	$list[] = $aux;
	$aux["col"] = "DESCRIPTION";
	$aux["name"] = "DESCRIPTION";
	$list[] = $aux;
	*/
	return $list;
  }
  
  public function getUserList($limit = 15, $page = 1, $order_by = false, $order = false, $filter_user_admin_id = null) {
	
	$offset = -1;
	if($page > 1)
	{
	  $offset = $limit * $page;
	}
	
	
	$sqlCount = "SELECT COUNT(USER_ADM_ID) FROM USER_ADM";
	
	$sql = "SELECT USER_ADM_ID, P.NAME AS USER_PROFILE_ID, USER_NAME,STATUS,EXPIRE_DATE,ERRORCOUNT,LAST_ACCESS FROM USER_ADM U " .
			"INNER JOIN USER_PROFILE P ON U.USER_PROFILE_ID = P.USER_PROFILE_ID ";
	
	//$sql = "SELECT USER_ADM_ID, P.NAME AS USER_PROFILE_ID, USER_NAME,STATUS,EXPIRE_DATE,ERRORCOUNT,LAST_ACCESS, U.EMAIL, U.PASSWORD, P.DESCRIPTION FROM USER_ADM U INNER JOIN USER_PROFILE P ON U.USER_PROFILE_ID = P.USER_PROFILE_ID";
	$filters_active = false;
	$data_count = array();
	$data_query = false;
	$where_is_active = false;
	if(!is_null($filter_user_admin_id))
	{
	  if(!$where_is_active)
	  {
		$sql .= " WHERE ";
		$sqlCount .= " WHERE ";
	  }
	  $sql .= "USER_ADM_ID LIKE :user_adm_id";
	  $sqlCount .= "USER_ADM_ID LIKE :user_adm_id";
	  $data_count = array("user_adm_id" => "%".$filter_user_admin_id."%");	  
	  $data_query = array("user_adm_id" => "%".$filter_user_admin_id."%");	  
	  $filters_active = true;
	}
	
	if($order_by != false)
	{
	  if($order == false)
		$order = "DESC";
	  $sqlCount .= " ORDER BY ".$order_by. " ".$order;
	  $sql .= " ORDER BY ".$order_by. " ".$order;
	}
	$ci = & get_instance();
	$ci->adodb->connect();
	$resultsetCount = $ci->adodb->execute($sqlCount, $data_count);
	$resultset = $ci->adodb->selectLimit($sql, $limit, $data_query, $offset);
	$ci->adodb->disconnect();
	$salida = array();
	$aux = $resultsetCount->getArray();
	$salida["count"] = $aux[0][0];
	$salida["datos"] = $resultset;
	$salida["headers"] = $this->headers();
	return $salida;
	
  }

  /**
   * function SaveForm()
   *
   * insert form data
   * @param $form_data - array
   * @return Bool - TRUE or FALSE
   */
  function SaveForm($form_data) {
	$this->db->insert('USER_ADM', $form_data);

	if ($this->db->affected_rows() == '1') {
	  return TRUE;
	}

	return FALSE;
  }
  
  function updateForm($form_data) {
	if(!isset($form_data["USER_ADM_ID"]))
	{
	 throw new Exception("Exception", 150);
	}
	$userId = $form_data["USER_ADM_ID"];
	unset($form_data["USER_ADM_ID"]);
	$this->db->where("USER_ADM_ID", $userId);
	$this->db->update('USER_ADM', $form_data);

	if ($this->db->affected_rows() == '1') {
	  return TRUE;
	}

	return FALSE;
  }  
  
  function deleteUser($username)
  {
	$this->db->where("USER_ADM_ID", $username);
	$this->db->delete('USER_ADM');
	if ($this->db->affected_rows() == '1') {
	  return TRUE;
	}
	return FALSE;
  }

  public function retrieveUserAdmIdLike($query)
  {
	$salida = array();
	if(is_null($query))
	{
	  return $salida;
	}
	
	$sql = "SELECT USER_ADM_ID FROM USER_ADM WHERE USER_ADM_ID LIKE :condition";
	$data = array("condition" => "%".$query."%");
	$ci = & get_instance();
	$ci->adodb->connect();
	$resultset = $ci->adodb->execute($sql, $data);
	$ci->adodb->disconnect();
	foreach($resultset->getArray() as $row)
	{
	  $item = array();
	  $item["value"] = $row["USER_ADM_ID"];
	  $item["name"] = $row["USER_ADM_ID"];
	  $salida[] = $item;
	}
	return $salida;
	
	
	//$this->db->like("USER_ADM_ID", $query);
	//$data = $this->db->get('USER_ADM');
	//var_dump($data);
	/*foreach($data->fetchArray() as $auxData)
	{
	  //var_dump($auxData);
	}*/
  }
  
}

