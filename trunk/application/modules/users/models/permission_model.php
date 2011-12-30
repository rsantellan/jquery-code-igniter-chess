<?php

class permission_model extends CI_Model {

	function __construct()
	{
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

	function SaveForm($form_data)
	{
		$this->db->insert('PERMISSION', $form_data);
		
		if ($this->db->affected_rows() == '1')
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function retrieveUserPermissions($permisssion_group_id)
	{
	  $this->adodb->connect();
	  $sql = "select * from permission";
	  $resultset = $this->adodb->execute($sql);
	  $this->adodb->disconnect();
	  var_dump($resultset);
	  
	}
	
	public function headers()
	{
	  $list = array();
	  $aux = array();
	  $aux["col"] = "p.permission_id";
	  $aux["name"] = "permission_id";
	  $list[] = $aux;
	  $aux["col"] = "pg.name";
	  $aux["name"] = "pg_name";
	  $list[] = $aux;
	  $aux["col"] = "p.name";
	  $aux["name"] = "name";
	  $list[] = $aux;
	  return $list;
	}
	
  public function getPermissionList($permission_group_id = null, $limit = 15, $page = 1, $order_by = false, $order = false) {

	$offset = -1;
	if ($page > 1) {
	  $offset = $limit * $page;
	}

	$sqlCount = "SELECT COUNT(p.PERMISSION_ID) FROM PERMISSION p ";

	$sql = "SELECT 
			p.permission_id, pg.name AS pg_name, p.name 
			FROM 
			PERMISSION p
			inner join PERMISSION_GROUPS pg on pg.permission_groups_id = p.permission_groups_id ";
	$sql_count_data = array();
	$sql_data = array();
	if(!is_null($permission_group_id))
	{
	  $sqlCount .= "where p.permission_groups_id = :permission_group";
	  $sql .= "where p.permission_groups_id = :permission_group";
	  $sql_count_data["permission_group"] =  $permission_group_id;
	  $sql_data["permission_group"] =  $permission_group_id;
	}
	
	if ($order_by != false) {
	  if ($order == false)
	  {
		$order = "DESC";
	  }
	  
	  $sql .= " ORDER BY " . $order_by . " " . $order;
	  //$sql_data["order_by"] =  $order_by;
	  //$sql_data["order"] =  $order;
	}
	$ci = & get_instance();
	$ci->adodb->connect();
	$resultsetCount = $ci->adodb->execute($sqlCount, $sql_count_data);
	$resultset = $ci->adodb->selectLimit($sql, $limit, $sql_data, $offset);
	$ci->adodb->disconnect();
	$salida = array();
	$aux = $resultsetCount->getArray();
	$salida["count"] = $aux[0][0];
	$salida["datos"] = $resultset;
	$salida["headers"] = $this->headers();
	return $salida;
  }
  
  public function retrieveAllPermissions($group_by_group = true)
  {
	$sql = "
	  select 
		p.permission_id, 
		p.name, 
		pg.permission_groups_id, 
		pg.name as pg_name 
	  from 
		permission p 
	  INNER JOIN permission_groups pg ON p.permission_groups_id = pg.permission_groups_id
	  order by pg.permission_groups_id";
	$ci = & get_instance();
	$ci->adodb->connect();
	$resultset = $ci->adodb->execute($sql);
	$ci->adodb->disconnect();
	$salida = array();
	foreach($resultset as $row)
	{
	  if($group_by_group)
	  {
		if(!isset($salida[$row["PERMISSION_GROUPS_ID"]]))
		{
		  $salida[$row["PERMISSION_GROUPS_ID"]] = array();
		  $salida[$row["PERMISSION_GROUPS_ID"]]["NAME"] = $row["PG_NAME"];
		  $salida[$row["PERMISSION_GROUPS_ID"]]["DATA"] = array();
		}
		
		array_push($salida[$row["PERMISSION_GROUPS_ID"]]["DATA"], $row);
	  }
	  else
	  {
		$salida[$row["PERMISSION_ID"]] = $row;
	  }
	  
	}
	return $salida;
  }
  
  public function retrieveAllPermissionGroups()
  {
	$sql = "
	  select 
		pg.permission_groups_id, 
		pg.name
	  from 
		permission_groups pg 
	  order by pg.permission_groups_id";
	$ci = & get_instance();
	$ci->adodb->connect();
	$resultset = $ci->adodb->execute($sql);
	$ci->adodb->disconnect();
	$salida = array();
	$salida[0] = lang("filtro seleccionar todos");
	foreach($resultset as $row)
	{
	  $salida[$row["PERMISSION_GROUPS_ID"]] = $row["NAME"];
	}
	return $salida;
  }
}
