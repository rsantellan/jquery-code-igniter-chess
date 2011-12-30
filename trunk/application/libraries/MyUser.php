<?php

/**
 * Description of MyUser
 *
 * @author rodrigo
 */
class MyUser {
  //put your code here
  private $userAdminId = "";
  private $userName = "";
  private $profile = "";
  private $status = "";
  private $expireDate = "";
  private $errorCount = "";
  private $initilize = false;
  
  private $permissions = array();
  
  private $permissionsGroups = array();
  
  public function getUserAdminId() {
	return $this->userAdminId;
  }

  public function setUserAdminId($userAdminId) {
	$this->userAdminId = $userAdminId;
  }

  public function getUserName() {
	return $this->userName;
  }

  public function setUserName($userName) {
	$this->userName = $userName;
  }

  public function getProfile() {
	return $this->profile;
  }

  public function setProfile($profile) {
	$this->profile = $profile;
  }

  public function getStatus() {
	return $this->status;
  }

  public function setStatus($status) {
	$this->status = $status;
  }

  public function getExpireDate() {
	return $this->expireDate;
  }

  public function setExpireDate($expireDate) {
	$this->expireDate = $expireDate;
  }

  public function getErrorCount() {
	return $this->errorCount;
  }

  public function setErrorCount($errorCount) {
	$this->errorCount = $errorCount;
  }

  private function getInitilize() {
	return $this->initilize;
  }

  private function setInitilize($initilize) {
	$this->initilize = $initilize;
  }

  public function getPermissions() {
	return $this->permissions;
  }

  public function getPermissionsGroups() {
	return $this->permissionsGroups;
  }

 
  
  public function autoComplete()
  {
	if(!$this->getInitilize())
	{
	  $ci = & get_instance();
	  $ci->adodb->connect();
	  $sql = "select p.* from permission p, user_profile_permission up where p.permission_id = up.permission_id and up.user_profile_id = :id";
	  $resultset = $ci->adodb->execute($sql, array('id' => $this->getProfile()));
	  $ci->adodb->disconnect();
	  $permission = array();
	  foreach($resultset as $row)
	  {
		$this->permissions[$row["NAME"]] = $row["NAME"];
		$this->permissionsGroups[$row["PERMISSION_GROUPS_ID"]] = $row["PERMISSION_GROUPS_ID"];
	  }
	  $this->setInitilize(true);
	}
  }



}

