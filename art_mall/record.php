<?php
define('IN_OUN', true);
include_once( "data/config.inc.php");
include_once( "class/mydb.php");
$oPub = new mydb($dbhost,$dbuser,$dbpw,$dbname); 
if($_GET[main_url]) {
	function gmtime() {
       return (time() - date("Z"));
    }  
 
	//$id = $oPub->getOne("SELECT id FROM ".$pre."urlrecord WHERE  mainurl like '$_GET[main_url]' LIMIT 1"); 
	//if($id < 1 ){
		//$Afields=array('mainurl'=>$_GET[main_url],'adddate'=>gmtime());
		//$oPub->install($pre."urlrecord",$Afields);
	}else{
		//echo date("Y-m-d H:i:s");
	}
}
?>