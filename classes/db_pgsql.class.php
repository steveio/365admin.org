<?php

/* 	
	db_pgsql.class.php
	PostgreSQL Database Wrapper
			  
*/

class db {

    public function __construct($dsn = null,$debug = false) {
        $this->db($dsn,$debug);
    }

	// constructor
	function db($dsn = null,$debug = false) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		

		$this->dbname 	= $dsn['dbname'];
		$this->port     = $dsn['dbport'];
		$this->user     = $dsn['dbuser'];
		$this->pass		= $dsn['dbpass'];

		// -------------------------------------------

		$this->conn_str = "port=$this->port dbname=$this->dbname user=$this->user password=$this->pass";
		
		//if (DEBUG) Logger::Msg($this->conn_str);
		
		$this->db = null;
		// automatically call connect method
		$this->connect();
		$this->setDateStyle();
	} // end constructor


	// connect to database
	function connect() {
	
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		if(null === $this->db) {
			$this->db=@pg_connect($this->conn_str);
		}
			
		if (!$this->db) {
			$this->halt(); // call error handler
		}
	}

	function last_error() {
		return pg_last_error($this->db);
	}

	function close() {
	
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		pg_close($this->db);
	}

	// set postgres date style
	function setDateStyle(){
	
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		$res = pg_Exec($this->db, "set DATESTYLE to 'European'");
	}

	// error handler
	function halt() {
	
		global $_CONFIG;

		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header("Location: /back_soon.php");
	
	}

	// get first row
	function getFirstRow($query) {
	
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		if (DEBUG) Logger::Msg("<span style='font-size: 11px;'>".$query."</span>");
		
		if(strlen($query = trim($query))) {
			$result = pg_exec($this->db, $query);
			$count = pg_numrows($result);
			if($count >= 1) {
				return pg_fetch_array($result);
			} else {
				// email failed queries for trace analysis
		        //$this->errorMail($query);
		        Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$query);
				return false; // query failed
			}
		} else {
			print("<b>getFirstRow() Warning:</b><br />No query supplied.<br />");
			return false;
		}
	}
	
	// get first cell
	function getFirstCell($query) {

	    if(DEBUG) Logger::Msg($query);

		if(strlen($query = trim($query))) {
			if($result = pg_exec($this->db, $query)) {
				$row = pg_fetch_array($result);
				return $row[0];
			} else {
	    	    Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$query);
				return false; // query failed
			}
		} else {
			print("<b>getFirstCell() Warning:</b><br />No query supplied.<br />");
			return false;
		}
	}


	// execute a query
	function query($query) {

		if(DEBUG) Logger::Msg($query);

		if(strlen($query = trim($query))) {
		    $this->last_result = pg_exec($this->db, $query);
		    if($this->last_result) {
				return true;
			} else {
			    ob_start();
			    debug_print_backtrace();
			    $trace = ob_get_contents();
			    ob_end_clean();
				$this->last_result = false;
				Logger::DB(1,get_class($this)."::".__FUNCTION__."()",pg_last_error($this->db)."\n".$query."\n".$trace);
				return false;
			}
		} else {
			print("<b>Query() Warning:</b><br />No query supplied.<br />");
			return false;
		}
	}


	// Get a row (as an array) from the last query:
	function getRow($fetchmode = PGSQL_BOTH,$rowIdx = NULL) {
		if($this->last_result) {
			return pg_fetch_array($this->last_result,$rowIdx,$fetchmode);
		} else {
			return false; // last_result is not a valid result set
		}
	}

    function getRows($fetchmode = PGSQL_ASSOC) {
		for ($i = 0; $i < $this->getNumRows(); $i++) {
			$arr[$i] = pg_fetch_array($this->last_result,NULL,$fetchmode);
		}
		return $arr;
	}

	function getRowsNum($fetchmode = PGSQL_NUM) {
		$arr = array();
		for ($i = 0; $i < $this->getNumRows(); $i++) {
			$tmp = pg_fetch_array($this->last_result,NULL,$fetchmode);
			$arr[]= (is_numeric($tmp[0])) ? (int) $tmp[0] : $tmp[0]; 
		}
		return $arr;
	}
	


	// Get number of rows from last query:
	function getNumRows() {
		if($this->last_result) {
			return pg_numrows($this->last_result);
		} else {
			return 0; // last_result is not a valid result set
		}
	}

	// Get number of rows affected by last query:
	function getAffectedRows() {
		if($this->last_result) {
			return pg_affected_rows($this->last_result);
		} else {
			return 0; // last_result is not a valid result set
		}
	}

	// Get oid of last-inserted row:
	function getLastOid() {
		if($this->last_result) {
			return pg_last_oid($this->last_result);
		} else {
			return false;
		}
	}

	// Get row (as an object) from the last query
	function getObject() {
		if($this->last_result) {
			return pg_fetch_object($this->last_result);
		} else {
			return false; // last_result is not a valid result set
		}
	}

    function getObjects() {
		for ($i = 0; $i < $this->getNumRows(); $i++) {
			$arr[$i] = pg_fetch_object($this->last_result,$i);
		}
		return $arr;
	}

	function getAll() {
		return pg_fetch_all($this->last_result);
	}

	function query_assoc_paged ($sql, $limit=0, $offset=0) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		$this->num_rows = false;
	
		if (!$this->query($sql)) return false;
		
		$result = $this->last_result;
				
		if (!$result) return (false);
	
		// save the number of rows we are working with
		$this->num_rows = @pg_num_rows($result);
		
		// moves the internal row pointer of the result to point to our
		// desired offset. The next call to pg_fetch_assoc() would return
		// that row.
		if (! empty($offset)) {
			if (! @pg_result_seek($result, $offset)) {
				return (array());
			}
		}
	
		// gather the results together in an array of arrays...
		$data = array();
		
		while (($row = pg_fetch_assoc($result)) !== false) {
			
			$data[] = $row;			
			
			// After reading N rows from this result set, free our memory
			// and return the rows we fetched...
			if (! empty($limit) && count($data) >= $limit) {
				pg_free_result($result);
				return ($data);
			} 
		}
		pg_free_result($result);
		return($data);
	}

	function logError($query) {
	}

	/**
	 * @see ./dbal/lib/Doctrine/DBAL/Platforms/PostgreSqlPlatform.php
	 * @return array
	 */
	public function getTableList()
	{
	    $sql = "SELECT quote_ident(table_name) AS table_name,
                       table_schema AS schema_name
                FROM   information_schema.tables
                WHERE  table_schema NOT LIKE 'pg\_%'
                AND    table_schema != 'information_schema'
                AND    table_name != 'geometry_columns'
                AND    table_name != 'spatial_ref_sys'
                AND    table_type != 'VIEW'";
	    
	    $this->query($sql);

	    if ($this->getNumRows() >= 1)
	    {
	        return $this->getRows();
	    }

	}

	/**
	 * @see ./dbal/lib/Doctrine/DBAL/Platforms/PostgreSqlPlatform.php
	 * @param string $strTable
	 * @return array
	 */
	public function getTableSchema($strTable)
	{
	    $sql = "
            SELECT
            a.attnum,
            quote_ident(a.attname) AS field,
            t.typname AS type,
            format_type(a.atttypid, a.atttypmod) AS complete_type,
            --(SELECT t1.typname FROM pg_catalog.pg_type t1 WHERE t1.oid = t.typbasetype) AS domain_type,
            --(SELECT format_type(t2.typbasetype, t2.typtypmod) FROM
            --pg_catalog.pg_type t2 WHERE t2.typtype = 'd' AND t2.oid = a.atttypid) AS domain_complete_type,
            a.attnotnull AS isnotnull,
            --(SELECT 't'
            --FROM pg_index
            --WHERE c.oid = pg_index.indrelid
            --AND pg_index.indkey[0] = a.attnum
            --AND pg_index.indisprimary = 't'
            --) AS pri,
            (SELECT pg_get_expr(adbin, adrelid)
            FROM pg_attrdef
            WHERE c.oid = pg_attrdef.adrelid
            AND pg_attrdef.adnum=a.attnum
            ) AS default,
            (SELECT pg_description.description
            FROM pg_description WHERE pg_description.objoid = c.oid AND a.attnum = pg_description.objsubid
            ) AS comment
            FROM pg_attribute a, pg_class c, pg_type t, pg_namespace n
            WHERE 1=1
            AND c.relname = '".$strTable."' 
            AND a.attnum > 0
            AND a.attrelid = c.oid
            AND a.atttypid = t.oid
            AND n.oid = c.relnamespace
            ORDER BY a.attnum
            ";
	    
	    $this->query($sql);
	    
	    if ($this->getNumRows() >= 1)
	    {
	        return $this->getRows();
	    }
	}

} // end db class

?>
