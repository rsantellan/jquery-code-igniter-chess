<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Basic Json Response - Is the basic json response so that all can be the same
 *
 * @access	public
 * @param	boolean response  The response is true or false
 * @param	array options an array of the options to pass
 * @return	json	json array
 */
if ( ! function_exists('basic_json_response'))
{
	function basic_json_response($response, $options)
	{
		return json_encode(array(
            "response" => ($response ? "OK" : "ERROR"),
            "options" => $options
        ));
	}
}