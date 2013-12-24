<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
if($_GET["id"] > 0)
{
	$showstrform = '';
	$id = $_GET["id"] + 0;
	$sql = "SELECT *   FROM ".$pre."messages where id='".$id."' limit 1";
	$row = $oPub->getRow($sql); 
 
	$str =  str_replace('\"','"',$row[descs]);
	//$str .= '<div style="margin: 10px 10px 10px 200px">'.date("Y/n/j H:i:s",$row["dateadd"]).'</div>';
	if($row ){
		if($row[type] < 1)
		{
			if($row[restates] ==1){

				$sql = "SELECT *   FROM ".$pre."messagesre where messagesid=$id";
				$AnormAll = $oPub->select($sql);
				while( @list( $key, $value ) = @each( $AnormAll ) ) {
					$str .= '<div style="border-bottom: 1px solid #3399FF;"></div>';
					$str .= '['.date("Y-n-j H:i",$value["dateadd"]).'] '.$value[descs];
				}
			}
		}  

	}

}else{
	$str = $showstrform = '';
}
$showstr = "<div style='width:760px; margin:0 auto; padding:5px;background-color:#FFFFFF;border:1px solid #330000;color: #000000'>";
$showstr .=  $str;
$showstr .=  $showstrform;
$showstr .=  "</div>";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>站内短信详情</title>
<style type="text/css">
<!--
*{
margin:5px;
padding:5px;}  
-->
</style>
</head>

<body>
 
 <?php echo $showstr; ?>

</body>
</html>