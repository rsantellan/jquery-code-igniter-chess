<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Statistics
 *
 * @author rodrigo
 */
class Statistics {

  public function log_activity() {
        /*
		// We need an instance of CI as we will be using some CI classes
        $CI =& get_instance();
 
        // Start off with the session stuff we know
        $data = array();
        $data['account_id'] = $CI->session->userdata('account_id');
        $data['project_id'] = $CI->session->userdata('project_id');
        $data['user_id'] = $CI->session->userdata('user_id');
 
        // Next up, we want to know what page we're on, use the router class
        $data['section'] = $CI->router->class;
        $data['action'] = $CI->router->method;
 
        // Lastly, we need to know when this is happening
        $data['when'] = time();
 
        // We don't need it, but we'll log the URI just in case
        $data['uri'] = uri_string();
 
        // And write it to the database
        //$CI->db->insert('statistics', $data);
		var_dump($data);
		
		$dbs = array();

		// Let's determine which databases are currently connected to
		foreach (get_object_vars($CI) as $CI_object)
		{
			if (is_object($CI_object) && is_subclass_of(get_class($CI_object), 'CI_DB') )
			{
				$dbs[] = $CI_object;
			}
		}
		
		$count = 0;
		$output = "";
		foreach ($dbs as $db)
		{
			$count++;

			//$hide_queries = (count($db->queries) > $this->_query_toggle_count) ? ' display:none' : '';

			$show_hide_js = '(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_queries_db_'.$count.'\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.$CI->lang->line('profiler_section_hide').'\'?\''.$CI->lang->line('profiler_section_show').'\':\''.$CI->lang->line('profiler_section_hide').'\';">'.$CI->lang->line('profiler_section_hide').'</span>)';

			
			$output .= '<fieldset style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
			$output .= "\n";
			$output .= '<legend style="color:#0000FF;">&nbsp;&nbsp;'.$CI->lang->line('profiler_database').':&nbsp; '.$db->database.'&nbsp;&nbsp;&nbsp;'.$CI->lang->line('profiler_queries').': '.count($db->queries).'&nbsp;&nbsp;'.$show_hide_js.'</legend>';
			$output .= "\n";
			

			if (count($db->queries) == 0)
			{
				$output .= "<tr><td style='width:100%;color:#0000FF;font-weight:normal;background-color:#eee;padding:5px;'>".$CI->lang->line('profiler_no_queries')."</td></tr>\n";
			}
			else
			{
				foreach ($db->queries as $key => $val)
				{
					$time = number_format($db->query_times[$key], 4);

					$val = highlight_code($val, ENT_QUOTES);
					var_dump($val);
				}
			}
		}
		echo $output;
		var_dump($CI->db);
		var_dump($CI->db->queries);
		foreach($CI->db->queries as $query)
		{
		   var_dump($query);
		} 

		*/
    }
}

