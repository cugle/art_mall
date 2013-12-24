<?php
define('IN_OUN', true);
include_once( "./includes/command.php");

$sql = "SELECT * FROM ".$pre."sernet  WHERE stats=1 and domain_id=".$Aconf['domain_id']."  order by py asc";
$AnormAll = $oPub->select($sql);
$rssstr = '<data>'; 
while( @list( $key, $value ) = @each( $AnormAll) ) {
	 $rssstr .= '<area id="'.$value['py'].'" title="'.$value['name'].'" value="'.$value['name_desc'].'" url="'.$value['url'].'" target="_blank"/>'; 
}
$rssstr .= '</data>';
 
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header('Content-Type: text/xml');
echo  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";  
echo $rssstr;
exit;
?> 