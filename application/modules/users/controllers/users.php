<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of users
 *
 * @author rodrigo
 */
class users extends MY_Controller {

  function __construct() {
	parent::__construct();
	modules::run('login/is_logged_in');
	$this->loadI18n(get_class($this), $this->lenguage, FALSE, TRUE, '', strtolower(get_class($this)));
  }

  private function links() {
	$list = array();
	$list["titulo"] = lang("usuarios_titulos");
	$aux = array();
	$aux["link"] = site_url("users/userList");
	$aux["texto"] = lang("Usuarios_menu");
	$list["objectos"][] = $aux;
	$aux1 = array();
	$aux1["link"] = site_url("users/permisosList");
	$aux1["texto"] = lang("Permisos_menu");
	$list["objectos"][] = $aux1;
	$aux2 = array();
	$aux2["link"] = site_url("users/userProfileList");
	$aux2["texto"] = lang("Permisos_usuario_menu");
	$list["objectos"][] = $aux2;
	return $list;
  }

  function menu() {
	$this->data["links"] = $this->links();

	$this->load->view("menu", $this->data);
  }

  function dashboard() {
	$this->data["links"] = $this->links();
	$this->load->view("dashboard", $this->data);
  }

  /***
   * 
   * 
   * USUSARIOS
   * 
   */
  
  function userList() {
	$page = $this->input->get("page", TRUE);
	$order_by = $this->input->get("order_by", TRUE);
	$order = $this->input->get("order", TRUE);

	
	$filterString = "";
	$filter_user_admin_id = $this->input->get("user_adm_id", TRUE);
	
	if($filter_user_admin_id == false)
	{
	  $filter_user_admin_id = null;
	}
	else
	{
	  $filterString = "user_adm_id=" . $filter_user_admin_id;
	}	
	
	if ($page == false)
	  $page = 1;
	
	$addObject = true;
	if ($order != false && $order_by != false) {
	  $filterString = "order_by=" . $order_by . "&order=" . $order;
	  ;
	}
	$this->load->model('users/user_adm_model');
	$quantity = $this->config->item('list_number_rows');
	$list = $this->user_adm_model->getUserList($quantity, $page, $order_by, $order, $filter_user_admin_id);
	$this->data["content"] = 'userList';
	$this->data["listContent"] = $list["datos"];
	$pages = (int) $list["count"] / (int) $quantity;
	
	$pages = round($pages - 1, 0);
	if($pages == 0) $pages = 1;
	$this->data["pages"] = $pages;
	$this->data["page"] = $page;
	$this->data["resultQuantity"] = (int) $list["count"];
	$this->data["headers"] = $list["headers"];
	$this->data["filterString"] = $filterString;
	$this->data["order"] = $order;
	$this->data["order_by"] = $order_by;
	$this->data["addObject"] = $addObject;
	$this->data["table_key"] = "USER_ADM_ID";
	$this->data["listName"] = lang("usuarios_titulos");
	$this->data["obj_module"] = "users";
	$this->data["obj_model"] = "user_adm";
	
	$this->data["filterDataPermission"] = array();//$this->permission_model->retrieveAllPermissionGroups();
	$this->data["leftBoxOn"] = true;
	$this->data["leftBoxContent"] = "userListFilter";
	
	$this->addJavascript("jquery-ui-1.8.16.custom.min.js");
	$this->addStyleSheet("le-frog/jquery-ui-1.8.16.custom.css");
	$this->addModuleJavascript("users", "userListFilter");
	$this->addModuleStyleSheet("users", "userList.css");
	
	$this->load->view('layout', $this->data);
  }

  public function bringUsersDataUserAdmIdAutosuggest()
  {
	$q = $this->input->get("term", TRUE);
	
	$items = array();
	$data = array();
	
	if($q != false)
	{
	  $this->load->model('users/user_adm_model');
	  $data = $this->user_adm_model->retrieveUserAdmIdLike($q);
	}
	
	echo json_encode($data);
	exit(0);
  }
  
  
  private function setUserData() {

	$this->data["status_list"] = array(
		"A" => lang("usuario_activado"),
		"B" => lang("usuario_bloqueado"));

	$this->adodb->connect();
	$sql = "select user_profile_id, name from user_profile";
	$resultset = $this->adodb->execute($sql);
	$this->adodb->disconnect();
	$profile = array();
	foreach ($resultset as $row) {
	  $profile[$row["USER_PROFILE_ID"]] = $row["NAME"];
	}
	$this->data["user_profile_list"] = $profile;
  }

  function add()
  {
	$this->setUserData();
	$this->data["content"] = 'add';
	
	$this->addJavascript("jquery-ui-1.8.16.custom.min.js");
	$this->addStyleSheet("le-frog/jquery-ui-1.8.16.custom.css");
	$this->addModuleJavascript("users", "addEditUserAdmin.js");
	
	$this->load->view('layout', $this->data);
  }
  
  function edit($username) {
	$username = urldecode($username);
	$this->load->model('users/user_adm_model');
	$user_adm = $this->user_adm_model->getUser($username);
	//die($username);
	if (!$user_adm) {
	  redirect("users/userList");
	}
	$this->data["user_adm"] = $user_adm;
	$this->setUserData();
	$this->data["content"] = 'edit';

	$this->addJavascript("jquery-ui-1.8.16.custom.min.js");
	$this->addStyleSheet("le-frog/jquery-ui-1.8.16.custom.css");
	$this->addModuleJavascript("users", "addEditUserAdmin.js");
	
	$this->load->view('layout', $this->data);
	
  }

  private function _save($is_new = true)
  {
	
	$this->load->model('users/user_adm_model');
	$this->load->library('form_validation');
	$this->form_validation->set_rules('USER_ADM_ID', 'usuario', 'required|max_length[32]');
	$this->form_validation->set_rules('USER_NAME', 'nombre', 'max_length[32]');
	$this->form_validation->set_rules('USER_PROFILE_ID', 'Perfil de usuario', '');
	$this->form_validation->set_rules('STATUS', 'status', 'max_length[1]');
	$this->form_validation->set_rules('EXPIRE_DATE', 'EXPIRE_DATE', '');
	
	$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
	if ($this->form_validation->run() == TRUE) { 
	  // build array for the model
	  $form_data = array(
		  'USER_ADM_ID' => set_value('USER_ADM_ID'),
		  'USER_NAME' => set_value('USER_NAME'),
		  'USER_PROFILE_ID' => set_value('USER_PROFILE_ID'),
		  'STATUS' => set_value('STATUS'),
		  'EXPIRE_DATE' => set_value('EXPIRE_DATE')
	  );

	  // run insert model to write data to db
	  $save_ok = false;
	  if($is_new)
	  {
		$save_ok = $this->user_adm_model->SaveForm($form_data);
	  }
	  else
	  {
		$save_ok = $this->user_adm_model->updateForm($form_data);
	  }
	  if ($save_ok) { // the information has therefore been successfully saved in the db
		
		$this->session->set_flashdata('ok_message', lang('usuario_borrado con exito'));
		$this->session->set_flashdata('ok_message_description', lang('usuario_borrado con exito'));
		
		redirect('users/userList');   // or whatever logic needs to occur
	  } else {
		$this->session->set_flashdata("form_error", "paso algo..");
		//echo 'An error occurred saving your information. Please try again later';
		// Or whatever error handling is necessary
	  }
	}
	else
	{
	  return false;
	}
	
	
  }
  
  function create()
  {
	$error = $this->_save(true);
	
	$this->setUserData();
	$this->data["content"] = 'add';
	$this->load->view('layout', $this->data);
	
  }
  
  function save() 
  {
	$this->_save(false);
  }
  
  function delete($username)
  {
	$username = urldecode($username);
	$this->load->model('users/user_adm_model');
	$response = $this->user_adm_model->deleteUser($username);
	$options = array();
	$options['ok_message'] = lang('usuario_borrado con exito');
	$options['ok_message_description'] = lang('usuario_borrado con exito');
	echo basic_json_response(true, $options);
	die(0);
  }

  /***
   * 
   * 
   * PERMISOS
   * 
   */
  
  function permisosList()
  {
	$page = $this->input->get("page", TRUE);
	$order_by = $this->input->get("order_by", TRUE);
	$order = $this->input->get("order", TRUE);

	$permission_group_id = $this->input->get("permission_group_id", TRUE);
	
	$filterString = "";
	//var_dump($permission_group_id);
	if($permission_group_id == false)
	{
	  $permission_group_id = NULL;
	}
	else
	{
	  $filterString = "permission_group_id=" . $permission_group_id;
	}
	
	if ($page == false)
	  $page = 1;
	
	$addObject = true;
	if ($order != false && $order_by != false) {
	  if(!is_null($permission_group_id))
	  {
		$filterString .= "&";
	  }
	  $filterString .= "order_by=" . $order_by . "&order=" . $order;
	  
	}
	$this->load->model('users/permission_model');
	$quantity = $this->config->item('list_number_rows');
	
	$list = $this->permission_model->getPermissionList($permission_group_id, $quantity, $page, $order_by, $order);
	$this->data["content"] = 'permissionList';
	$this->data["listContent"] = $list["datos"];
	$pages = (int) $list["count"] / (int) $quantity;
	$pages = round($pages - 1, 0);
	if($pages == 0) $pages = 1;
	$this->data["pages"] = $pages;
	$this->data["page"] = $page;
	$this->data["resultQuantity"] = (int) $list["count"];
	$this->data["headers"] = $list["headers"];
	$this->data["filterString"] = $filterString;
	$this->data['edit_disable'] = true;
	$this->data["order"] = $order;
	$this->data["order_by"] = $order_by;
	$this->data["addObject"] = $addObject;
	$this->data["table_key"] = "PERMISSION_ID";
	$this->data["listName"] = lang("usuarios_titulos");
	$this->data["obj_module"] = "users";
	$this->data["obj_model"] = "permisssions";
	
	
	$this->data["filterDataPermission"] = $this->permission_model->retrieveAllPermissionGroups();
	$this->data["leftBoxOn"] = true;
	$this->data["leftBoxContent"] = "permisosListFilter";
	
	$this->load->view('layout', $this->data);	
  }
  
  
  /****
   * 
   * 
   * USER PROFILE
   * 
   * 
   */

  function userProfileList()
  {
	$page = $this->input->get("page", TRUE);
	$order_by = $this->input->get("order_by", TRUE);
	$order = $this->input->get("order", TRUE);

	if ($page == false)
	  $page = 1;
	$filterString = "";
	$addObject = true;
	if ($order != false && $order_by != false) {
	  $filterString = "order_by=" . $order_by . "&order=" . $order;
	  ;
	}
	$this->load->model('users/user_profile_model');
	$quantity = $this->config->item('list_number_rows');
	$list = $this->user_profile_model->getUserProfileList($quantity, $page, $order_by, $order);
	$this->data["content"] = 'userProfileList';
	$this->data["listContent"] = $list["datos"];
	$pages = (int) $list["count"] / (int) $quantity;
	$pages = round($pages - 1, 0);
	if($pages == 0) $pages = 1;
	$this->data["pages"] = $pages;
	$this->data["page"] = $page;
	$this->data["resultQuantity"] = (int) $list["count"];
	$this->data["headers"] = $list["headers"];
	$this->data["filterString"] = $filterString;
	$this->data['edit_disable'] = true;
	$this->data["order"] = $order;
	$this->data["order_by"] = $order_by;
	$this->data["addObject"] = $addObject;
	$this->data["table_key"] = "USER_PROFILE_ID";
	$this->data["listName"] = lang("usuarios_titulos");
	$this->data["obj_module"] = "users";
	$this->data["obj_model"] = "user_profile";
	$this->load->view('layout', $this->data);	
  }
  
  
  function viewUserProfilePermissions($profile_id)
  {
	$this->addJavascript("fancybox/jquery.mousewheel-3.0.4.pack.js");
	$this->addJavascript("fancybox/jquery.fancybox-1.3.4.pack.js");
	$this->addStyleSheet("../js/fancybox/jquery.fancybox-1.3.4.css");
	$this->addModuleJavascript(get_class($this), "userProfilePermission.js");
	
	$page = $this->input->get("page", TRUE);
	$order_by = $this->input->get("order_by", TRUE);
	$order = $this->input->get("order", TRUE);

	if ($page == false)
	  $page = 1;
	$filterString = "";
	$addObject = true;
	if ($order != false && $order_by != false) {
	  $filterString = "order_by=" . $order_by . "&order=" . $order;
	  ;
	}
	$this->data["profile_id"] = $profile_id;
	$this->load->model('users/user_profile_model');
	$quantity = $this->config->item('list_number_rows');
	$list = $this->user_profile_model->getUserProfilePermissionsList($profile_id, $quantity, $page, $order_by, $order);
	$this->data["content"] = 'userProfilePermissionList';
	$this->data["listContent"] = $list["datos"];
	$pages = (int) $list["count"] / (int) $quantity;
	$pages = round($pages - 1, 0);
	if($pages == 0) $pages = 1;
	$this->data["pages"] = $pages;
	$this->data["page"] = $page;
	$this->data["resultQuantity"] = (int) $list["count"];
	$this->data["headers"] = $list["headers"];
	$this->data["filterString"] = $filterString;
	$this->data['edit_disable'] = true;
	$this->data["order"] = $order;
	$this->data["order_by"] = $order_by;
	$this->data["addObject"] = $addObject;
	$this->data["table_key"] = "permission_id";
	$this->data["listName"] = lang("usuarios_titulos");
	$this->data["obj_module"] = "users";
	$this->data["obj_model"] = "user_profile_permissions";
	$this->load->view('layout', $this->data);	
  }
  
  public function addUserProfilePermission($user_profile_id)
  {
	
	$this->load->model('users/user_profile_model');
	$this->load->model('users/permission_model');
	$permission_list = $this->permission_model->retrieveAllPermissions();
	$permission_used_list = $this->user_profile_model->retrieveAllUsedProfilesIds($user_profile_id);
	$this->data["permission_group_list"] = $permission_list;
	$this->data["user_profile_id"] = $user_profile_id;
	$this->data["permission_used_list"] = $permission_used_list;
	
	$this->load->view('addUserProfilePermission', $this->data);
  }
  
  public function changePermissionOfUserProfile()
  {
	$permissionId = $this->input->get("permissionId", TRUE);
	$userProfileId = $this->input->get("userProfileId", TRUE);
	$selected = $this->input->get("selected", TRUE);
	
	$result = false;
	$data = array(
		  'PERMISSION_ID' => $permissionId,
		  'USER_PROFILE_ID' => $userProfileId
	  );
	$this->load->model('users/user_profile_permission_model');
	if($selected == "true")
	{
	  
	  $result = $this->user_profile_permission_model->save($data);
	}
	else
	{
	  $result = $this->user_profile_permission_model->delete($data);
	}
	$options = array();
	echo basic_json_response($result, $options);
	die(0);	
  }
  
  public function addUserProfile()
  {
	$this->data["content"] = 'addUserProfile';
	$this->load->view('layout', $this->data);	
  }
 
  function deleteUserProfile($id)
  {
	$this->load->model('users/user_profile_model');
	$response = $this->user_profile_model->delete($id);
	$options = array();
	$options['ok_message'] = lang('usuario_perfil de usuario borrado con exito');
	$options['ok_message_description'] = lang('usuario_perfil de usuario borrado con exito descripcion');
	echo basic_json_response(true, $options);
	die(0);
  }
  
  public function createUserProfile()
  {
	$error = $this->_saveUserProfile(true);
	$this->data["content"] = 'addUserProfile';
	$this->load->view('layout', $this->data);	
  }
  
  public function editUserProfile($id)
  {
	$this->load->model('users/user_profile_model');
	$user_profile = $this->user_profile_model->getUserProfile($id);
	if (!$user_profile) {
	  redirect("users/userProfileList");
	}
	$this->data["user_profile"] = $user_profile;
	$this->data["content"] = 'editUserProfile';
	$this->load->view('layout', $this->data);	
  }
  
  function saveUserProfile() 
  {
	$result = $this->_saveUserProfile(false);
	if(!$result)
	{
	  $this->load->model('users/user_profile_model');
	  $user_profile = $this->user_profile_model->getUserProfile(set_value('USER_PROFILE_ID'));
	  if (!$user_profile) {
		redirect("users/userProfileList");
	  }
	  $this->data["user_profile"] = $user_profile;
	  $this->data["content"] = 'editUserProfile';
	  $this->load->view('layout', $this->data);
	}
	
  }
  
  private function _saveUserProfile($is_new = true)
  {
	
	$this->load->model('users/user_profile_model');
	$this->load->library('form_validation');
	if(!$is_new)
	{
	  $this->form_validation->set_rules('USER_PROFILE_ID', 'user_profile_id', 'required|max_length[7]');			
	}
	$this->form_validation->set_rules('NAME', 'name', 'max_length[16]');			
	$this->form_validation->set_rules('DESCRIPTION', 'description', 'max_length[32]');

	
	$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
	if ($this->form_validation->run() == TRUE) { 
	  // build array for the model
	  $form_data = array(
		  'USER_PROFILE_ID' => set_value('USER_PROFILE_ID'),
		  'NAME' => set_value('NAME'),
		  'DESCRIPTION' => set_value('DESCRIPTION')
	  );

	  // run insert model to write data to db
	  $save_ok = false;
	  if($is_new)
	  {
		$save_ok = $this->user_profile_model->SaveForm($form_data);
	  }
	  else
	  {
		$save_ok = $this->user_profile_model->updateForm($form_data);
	  }
	  if ($save_ok != 0) { // the information has therefore been successfully saved in the db
		
		$this->session->set_flashdata('ok_message', lang('usuario_perfil de usuario creado con exito'));
		$this->session->set_flashdata('ok_message_description', lang('usuario_perfil de usuario creado con exito descripcion'));
		
		redirect('users/viewUserProfilePermissions/'.$save_ok);   // or whatever logic needs to occur
	  } else {
		$this->session->set_flashdata("form_error", "paso algo..");
		//echo 'An error occurred saving your information. Please try again later';
		// Or whatever error handling is necessary
	  }
	}
	else
	{
	  return false;
	}
	
	
  }
  
    
}

