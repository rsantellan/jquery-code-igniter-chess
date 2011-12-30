<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Form_validation
 *
 * @author rodrigo
 */
class MY_Form_validation extends CI_Form_validation{
  //put your code here
  
  public function __construct($rules = array())
  {
	parent::__construct($rules);
  }
  
  /**
   * Translate a field name
   *
   * @access	private
   * @param	string	the field name
   * @return	string
   */
  protected function _translate_fieldname($fieldname)
  {
	  $line = $fieldname;

	  // Were we able to translate the field name?  If not we use $line
	  if (FALSE === ($fieldname = $this->CI->lang->line($line)))
	  {
		  return $line;
	  }
	  return $fieldname;
  }
}


