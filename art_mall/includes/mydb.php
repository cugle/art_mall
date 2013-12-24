<?php
if (!defined('IN_OUN'))
{
    die('Hacking attempt');
}
class mydb 
{  

	var $link_id    = NULL;
	var $version    = '';
	var $error_message  = array();

    function __construct($dbhost,$dbuser,$dbpw,$dbname='',$charset  = 'UTF8')
    {
        $this->mydb($dbhost, $dbuser, $dbpw, $dbname, $charset);
    }

    function mydb($dbhost,$dbuser,$dbpw,$dbname='',$charset  = 'UTF8')
	{
       //$this->link_id = mysql_connect($db_host,$db_user,$db_pass) or die("connect db error!!");

        if (PHP_VERSION >= '4.2') {
              $this->link_id = @mysql_connect($dbhost, $dbuser, $dbpw, true);
        } else {
              $this->link_id = @mysql_connect($dbhost, $dbuser, $dbpw); 
              mt_srand((double)microtime() * 1000000); 
        }
		if (!$this->link_id){
			if (!$quiet){
				  $this->ErrorMsg("Can't Connect MySQL Server($dbhost)!");
			}
			return false;
		}
		$this->version = mysql_get_server_info($this->link_id); 
        if ($this->version > '4.1') {
            if ($charset != 'latin1') {
                mysql_unbuffered_query ("SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary", $this->link_id);
            }
            if ($this->version > '5.0.1') {
                mysql_unbuffered_query ("SET sql_mode=''", $this->link_id);
            }
        }

        if ($dbname) {
            if (mysql_select_db($dbname, $this->link_id) === false ) {
                if (!$quiet) {
                    $this->ErrorMsg("Can't select MySQL database($dbname)!");
                } 
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function select_database($dbname) {
        return mysql_select_db($dbname, $this->link_id);
    }

    function query($sql) {  
        if (!($query = mysql_query($sql, $this->link_id)) ) {
            $this->error_message[]['message'] = 'MySQL Query Error';
            $this->error_message[]['sql'] = $sql;
            $this->error_message[]['error'] = mysql_error($this->link_id);
            $this->error_message[]['errno'] = mysql_errno($this->link_id); 
            $this->ErrorMsg(); 
            return false;
        }else{
			return $query;
		}
        
    }

    function set_mysql_charset($charset) { 
        if ($this->version > '4.1'){
            if (in_array(strtolower($charset), array('gbk', 'big5', 'utf-8', 'utf8'))) {
                $charset = str_replace('-', '', $charset);
            }
            if ($charset != 'latin1')  {
                mysql_unbuffered_query ("SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary", $this->link_id);
            }
        }
    }

    function escape_string($unescaped_string)  {
        if (PHP_VERSION >= '4.3') {
            return mysql_real_escape_string($unescaped_string);
        }  else {
            return mysql_escape_string($unescaped_string);
        }
    }
    function getOne($sql, $limited = false) {
        if ($limited == true) {
            $sql = trim($sql . ' LIMIT 1');
        } 
        $res = $this->query($sql);
        if ($res !== false) {
            $row = mysql_fetch_row($res); 
            if ($row !== false)  {
                return $row[0];
            } else {
                return '';
            }
        } else {
            return false;
        }
    }

    function getRow($sql, $limited = false)
    {
        if ($limited == true)
        {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
        if ($res !== false)
        {
            return mysql_fetch_assoc($res);
        } else {
            return false;
        }
    }

    function select($sql='')
	{ 
	   if( $sql == "" )
	   { 
		   return false;
	   } 
 	   $result=@mysql_unbuffered_query ($sql,$this->link_id) or $this->ErrorMsg($sql); 
	   $i=0;
	   while($row=@mysql_fetch_assoc($result)) {
	     $i++;
	     $result_array[$i]=$row;
	   } 
	   return ($i > 0)?$result_array:false; 
     }  
 
    function select_affected_rows($sql='')
	{ 
	   if( $sql == "" )
	   { 
		   return false;
	   } 
 	   $result = @mysql_query ($sql,$this->link_id) or $this->ErrorMsg($sql); 
	   $row    =  @mysql_num_rows($result);		
	   return $row; 
    }  

    function update($db_table,$Afields=array(),$condition='')
	{
	   $sql = '';
	   if( $db_table == "" )
	   { 
		   return false;
	   } 
	   if( count($Afields)  ==  0 )
	   {
	        return false;
	   }else{
	        foreach ($Afields as $k=>$v)
			{
	            $sql .= "`$k`='".$this->escape_string($v)."',";
	        }
	   }

	   if(strlen($sql) > 0)
	   {
	       $sql = substr($sql,0,strlen($sql)-1);
	   }	 
	   $sql = "UPDATE  $db_table SET ".$sql.' where '.$condition; 
 	   $result=@mysql_unbuffered_query ($sql,$this->link_id) or $this->ErrorMsg($sql);
	}

    function insert_id()
    {
        return mysql_insert_id($this->link_id);
    }

    function install($db_table,$fields=array())
	{
	   if( $db_table == "" )
	   { 
		   return false;
	   }else{
           $db_table = $db_table;
	   }

	   if( count($fields)  ==  0 )
	   {
	        return false;
	   }else{
		    $sql1 =$sql2 = '';
	        foreach ($fields as $k=>$v) { 
	            $sql1 .= "`$k`,";
				$sql2 .= "'".$this->escape_string($v)."',";
	        }
	   }

	   if(strlen($sql1) > 0 and strlen($sql2) > 0 )
	   {
	       $sql1 = substr($sql1,0,strlen($sql1)-1);
		   $sql2 = substr($sql2,0,strlen($sql2)-1);
	   }	 
	   $sql = "INSERT INTO $db_table ($sql1) VALUES ($sql2);"; 
 	   $result=@mysql_unbuffered_query ($sql,$this->link_id)  or $this->ErrorMsg($sql); 
	   return mysql_insert_id($this->link_id);
	}
    
	function delete($db_table,$condition='')
	{
	   if(( $db_table == "" ) or ($condition == ''))
	   { 
		   return false;
	   }

      $sql = "delete from $db_table where ".$condition;
	  $result=@mysql_unbuffered_query ($sql,$this->link_id) or $this->ErrorMsg($sql);	
  	}

    function getAll($sql)
    {
        $res = $this->query($sql);
        if ($res !== false)
        {
            $arr = array();
            while ($row = mysql_fetch_assoc($res))
            {
                $arr[] = $row;
            }

            return $arr;
        } else {
            return false;
        }
    }
    function autoReplace($table, $field_values, $update_values, $where = '', $querymode = '')
    {
        $field_descs = $this->getAll('DESC ' . $table);

        $primary_keys = array();
        foreach ($field_descs AS $value)
        {
            $field_names[] = $value['Field'];
            if ($value['Key'] == 'PRI')
            {
                $primary_keys[] = $value['Field'];
            }
        }

        $fields = $values = array();
        foreach ($field_names AS $value)
        {
            if (array_key_exists($value, $field_values) == true)
            {
                $fields[] = $value;
                $values[] = "'" . $field_values[$value] . "'";
            }
        }

        $sets = array();
        foreach ($update_values AS $key => $value)
        {
            if (array_key_exists($key, $field_values) == true)
            {
                if (is_int($value) || is_float($value))
                {
                    $sets[] = $key . ' = ' . $key . ' + ' . $value;
                } else {
                    $sets[] = $key . " = '" . $value . "'";
                }
            }
        }

        $sql = '';
        if (empty($primary_keys))
        {
            if (!empty($fields))
            {
                $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            }
        }
        else
        {
            if ($this->version() >= '4.1')
            {
                if (!empty($fields))
                {
                    $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
                    if (!empty($sets))
                    {
                        $sql .=  'ON DUPLICATE KEY UPDATE ' . implode(', ', $sets);
                    }
                }
            }
            else
            {
                if (empty($where))
                {
                    $where = array();
                    foreach ($primary_keys AS $value)
                    {
                        if (is_numeric($value))
                        {
                            $where[] = $value . ' = ' . $field_values[$value];
                        } else {
                            $where[] = $value . " = '" . $field_values[$value] . "'";
                        }
                    }
                    $where = implode(' AND ', $where);
                }

                if ($where && (!empty($sets) || !empty($fields)))
                {
                    if (intval($this->getOne("SELECT COUNT(*) FROM $table WHERE $where")) > 0)
                    {
                        if (!empty($sets))
                        {
                            $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
                        }
                    } else {
                        if (!empty($fields))
                        {
                            $sql = 'REPLACE INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
                        }
                    }
                }
            }
        }

        if ($sql)
        {
            return $this->query($sql, $querymode);
        } else {
            return false;
        }
    }

    function version()
    {
        return $this->version;
    }

    function close()
    {
        return mysql_close($this->link_id);
    }

    function ErrorMsg($message = '')
    {
        if ($message) { 
            echo "XY58(Power by cugle, QQ:452275147 ) info:$message \n\n"; 
        } else {
            echo "<b>MySQL server error report:";
            print_r($this->error_message);
        }
        exit;
    }
} 
?>
