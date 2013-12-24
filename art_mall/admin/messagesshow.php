<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//回复
if($messagesid > 0  && !empty($redescs)){
	$sql = "INSERT INTO ".$pre."messagesre ( `messagesid` ,  `touser_id` ,  `tousername` ,  `descs` ,  `dateadd`,domain_id )VALUES ('".$messagesid."','".$_SESSION['auser_id']."', '".$_SESSION['auser_name']."','".$redescs."','".gmtime()."','".$Aconf['domain_id']."')";  
	$oPub->query($sql);
	$sql = "update ".$pre."messages set restates=1 where id='".$messagesid."' limit 1";
	$oPub->query($sql);
}

if($id > 0)
{
	$showstrform = '';
	$id = $_GET["id"] + 0;
	$sql = "SELECT *   FROM ".$pre."messages where id='".$id."' limit 1";
	$row = $oPub->getRow($sql); 
	$str = $row["descs"];
	if($row ){
		if($row[type] < 1) {
			if($row[restates] ==1){ 
				$sql = "SELECT *   FROM ".$pre."messagesre where messagesid=$id and   touser_id='".$_SESSION['auser_id']."'";
				$AnormAll = $oPub->select($sql);
				while( @list( $key, $value ) = @each( $AnormAll ) ) {
					$str .= '<div style="border-bottom: 1px solid #3399FF;"></div>';
					$str .= '['.date("Y-n-j H:i",$value["dateadd"]).'] '.$value[descs];
				}
			}

			$sql = "update ".$pre."messages set states=1 where id='".$id."' limit 1";
			$oPub->query($sql);
			$showstrform =  '<div style="border-bottom: 1px solid #3399FF;"></div>';
			$showstrform .=  '<FORM METHOD=POST ACTION="" style="margin: 0 0 0 20px"><b>回复给管理员：</b>';
			$showstrform .=  '<br/>';
			$showstrform .=  '<TEXTAREA NAME="redescs" ROWS="3" COLS="50"></TEXTAREA>';
			$showstrform .=  '<br/>';
			$showstrform .=  '<INPUT TYPE="hidden" NAME="messagesid" value="'.$id.'">';
			$showstrform .=  '<INPUT TYPE="hidden" NAME="id" value="'.$id.'">';
			$showstrform .=  '<INPUT TYPE="submit" value="确定回复" ></FORM>'; 
		}else{
			//记录到已读数据表
			$sql = "delete FROM ".$pre."messagesread where messagesid ='".$id."' and  user_id='".$_SESSION['auser_id']."'";
			$oPub->query($sql);

			$sql = "INSERT INTO ".$pre."messagesread ( user_id,messagesid )VALUES ('".$_SESSION['auser_id']."', '".$id."')"; 
			$oPub->query($sql);
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
body{ 
	line-height: 9px
	font-size: 9px;
}
-->
</style>
</head>

<body>
 
 <?php echo $showstr; ?>

</body>
</html>
