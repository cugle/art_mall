<?php
/* 广告点击统计 */
define('IN_OUN', true); 

include( "config.inc.php" );
include_once( ROOT_PATH."includes/funcomm.php");
include_once( ROOT_PATH."class/mydb.php");

$oPub = new mydb($dbhost,$dbuser,$dbpw,$dbname);
$db = $oPub; 
$dbhost = $dbuser = $dbpw = $dbname = NULL;

$ad_id = $_GET["id"] + 0;
$db_table = $pre."ad";
$sql = "SELECT ad_link FROM ".$db_table." WHERE ad_id='".$ad_id."' LIMIT 1";
$ad_link = $oPub->getOne($sql);
if($ad_link)
{

   $sql = "UPDATE " . $db_table . " SET  click_count= click_count + 1 WHERE  ad_id='".$ad_id."'";
   $oPub->query($sql);

   $ip = real_ip();
   $y   = date("Y");
   $m   = date("m");
   $d   = date("d");
   $adddate =  gmtime();

   $db_table = $pre."ad_affiche";
   $sql = "INSERT INTO " . $db_table . " (ad_id,y,m,d,adddate,ip)" .
           "VALUES ('$ad_id','$y','$m','$d','$adddate','$ip')"; 
   $oPub->query($sql);

}
else
{
   $ad_link = 'http://www.'.$Aconf['mail_url'];
}
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
echo "<SCRIPT language='javascript'>top.location='".$ad_link."';</script>";
exit;
?>