<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * AdoDB Class
 *
 * @package    CodeIgniter
 * @subpackage    Libraries
 * @category    AdoDB
 * @author    Kepler Gelotte
 */

require_once( BASEPATH.'libraries/adodb/adodb.inc.php' );
require_once( BASEPATH.'libraries/adodb/adodb-active-record.inc.php' );

class CI_AdoDB {

    var $conn = false;

    function CI_AdoDB()
    {
        log_message('debug', "AdoDB Class Initialized");
    }

    function connect( $name_space = '' )
    {
        include(APPPATH.'config/database'.EXT);

        $group = ($name_space == '') ? $active_group : $name_space;
        
        if ( ! isset($db[$group]))
        {
            show_error('You have specified an invalid database connection group: '.$group);
        }
        
        
        $params = $db[$group];
        
        $this->conn = ADONewConnection($params["dbdriver"]);
        $this->conn->connect(false, $params["username"], $params["password"], $params["database"]);
        log_message('QUERIES', "Base adodb conectada");
		if($params["dbdriver"] == "oci8")
		{
		  //$this->execute("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
		  $this->execute("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		  $this->execute("ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '. '");
		}
		
        //$this->conn = &ADONewConnection;( $params['dbdriver'].'://'.$params['username'].':'.$params['password'].'@'.$params['hostname'].'/'.$params['database'] );
        if ( ! $this->conn ) die( "Connection failed to database " . $db );
		ADOdb_Active_Record::SetDatabaseAdapter($this->conn);
		
    }

    /**
     * Execute SQL 
     *
     * @param sql		SQL statement to execute, or possibly an array holding prepared statement ($sql[0] will hold sql text)
     * @param [inputarr]	holds the input data to bind to. Null elements will be set to null.
     * @return 		RecordSet or false
     */
    function execute( $statement, $inputarr=false)
    {
        $recordSet = $this->conn->Execute( $statement , $inputarr);
		return $recordSet;
    }

	/**
     * Execute select SQL with limit
     *
     * @param [statement]		SQL statement to execute, or possibly an array holding prepared statement ($sql[0] will hold sql text)
     * @param [nrows]		is the number of rows to get
	 * @param [inputarr]	holds the input data to bind to. Null elements will be set to null.
     * @param [offset]	is the row to start calculations from (1-based)
	 * @return 		RecordSet or false
	 * @author Rodrigo Santellan
     */
	function selectLimit($statement, $quantity = 15, $inputarr = false, $offset = -1)
	{
	  $recordSet = $this->conn->SelectLimit($statement, $quantity, $offset, $inputarr);
	  return $recordSet;
	}
	
    function replace( $table, $fields, $keys, $autoQuote = false )
    {
        $rc = $this->conn->Replace( $table, $fields, $keys, $autoQuote );
        return $rc;
    }

    function startTrans( )
    {
        $rc = $this->conn->StartTrans( );
        return $rc;
    }

    function failTrans( )
    {
        $rc = $this->conn->FailTrans( );
        return $rc;
    }

    function completeTrans( )
    {
        $rc = $this->conn->CompleteTrans( );
        return $rc;
    }

    function getErrorMsg()
    {

        return $this->conn->ErrorMsg();
    }

    function disconnect()
    {
        // $recordSet->Close(); # optional
        $this->conn->Close();
    }

}
// END AdoDB Class
