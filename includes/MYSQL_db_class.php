<?php

class DB {
 	var $db_connection;
 	var $db_name;	
 	var $user;
 	var $pass;
 	var $host;
 	var $port;
   	var $socket;
   	var $err_msg;		
 	var $last_query;	
	var $query_time; 
 	function init() {
 		$this->last_query = "";
		$this->query_time = 0;
	}
 
 
	function set_db_settings($db_name, $user, $pass, $host, $port=NULL, $socket=NULL):void{		
 
		$this->db_name 	= $db_name;
		$this->host	= $host;
        $this->port	= $port;
        $this->socket = $socket;
		$this->user	= $user;
		$this->pass	= $pass;
    
	}
 
 
function connect():bool {     

        $this->db_connection = new mysqli($this->host, $this->user, $this->pass, $this->db_name, $this->port, $this->socket);

		if (mysqli_connect_errno()) {
			$this->err_msg = gettext("Failed connecting to database")." (" . mysqli_connect_errno() . ") " . mysqli_connect_error().".";
			return FALSE;
		}else{
            return TRUE;
        }
	}
 
 
function disconnect():bool {
        $status = mysqli_close($this->db_connection);
        if (!$status) {
            $this->err_msg = "Failed closing connection to database.";
			return FALSE;
        }else{
            unset($this->db_connection);
        return true;
        }		
	}
  
 
function query($query):bool {
 
 		$this->last_query = $query; 
		$this->query_time = 0;
		$timer = $this->getTime();

 		$result = mysqli_query($this->db_connection, $query );

 		$this->query_time = $this->getTime() - $timer;

		
		if($result===FALSE){
			$this->err_msg = mysqli_error($this->db_connection);
			return FALSE;		
		}else{		
			return TRUE;
		}		
	}
 
 

function select($query) {
 
		
 		$this->last_query = $query; 
		$this->query_time = 0;
		$timer = $this->getTime();

        $result = mysqli_query($this->db_connection, $query );


 		$this->query_time = $this->getTime() - $timer;
		
		if($result===FALSE || $result == NULL){
			$this->err_msg = mysqli_error($this->db_connection);
			return FALSE;		
		}
 		if( $result->num_rows==0 ){
			return FALSE;
		}

		$resArray = array();
		while ($row = $result->fetch_assoc()) {
			$resArray[] = $row;
		}		
 
		return $resArray;		
	}
  
 
	function getRow($query) {
 
	
 		$this->last_query = $query; 
		$this->query_time = 0;
		$timer = $this->getTime();

        $result = mysqli_query($this->db_connection, $query);
 
 		$this->query_time = $this->getTime() - $timer;
		
		if($result===FALSE || $result == NULL){
			$this->err_msg = mysqli_error($this->db_connection);
		return array($this->err_msg);			
		}else
 
 		return $result->fetch_assoc();
}
 
 function get_max_fieldlength($table,$field):int{
 $query="SELECT ".$field." FROM ".$table." limit 0,1";
 $result = mysqli_query($this->db_connection, $query);
 $finfo = $result->fetch_field_direct(0);
 return $finfo->length/3;
 }

 

	function insertedId():int {	
		return( mysqli_insert_id($this->db_connection));    
	}
 
 
 
	function affectedRows():int {
		return( mysqli_affected_rows($this->db_connection));
	}
 
 
	
	function escapeStr($inStr, $isGPC=FALSE):string {
		if($isGPC){
			if(get_magic_quotes_gpc()){
				$inStr = stripslashes($inStr);
			}
		}
        return mysqli_real_escape_string($this->db_connection, $inStr);
	}
 
	private function getTime(){
        	$microtime = explode(' ', microtime());
        	return $microtime[1] . substr($microtime[0], 1);
	}
 
} // End Class DB.

?>
