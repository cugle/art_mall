<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  
/* 编辑个人资料 */
if($Aconf['states'] == 2)
{
	$Aconf['states_type'] = 'VIP';
}else
{
	$Aconf['states_type'] = '普通';
} 

$user_url = $Aconf['user_name'].'.'.$Aconf['mail_url'];
if( $Aconf['main_domin'] <> $user_url)
{
	$Aconf['user_url'] = $Aconf['main_domin'].'<br/>';	
	$Aconf['user_url'] .= $user_url;	
}else
{
	$Aconf['user_url'] = $Aconf['main_domin'];
}
 
$Amessage["strShow"] ='';
//总站公告
$row = $oPub->getRow('SELECT notices FROM '.$pre.'sysnotice order by id desc limit 1');	
if($row)
{
	$Amessage["strShow"] .='<div class="line"></div>';
	$Amessage["strShow"] .= '<div style="font-weight: bold;font-size:18px;color:#00EE00;background-color:#F0E6FF">站长公告:</div>';
	$Amessage["strShow"] .='<div class="line"></div>';
	$Amessage["strShow"] .= $row['notices']; 
	$Amessage["strShow"] .='<div class="line"></div>';
}

//服务器基本信息 
$Amessage["strShow"] .= '<div style="font-weight: bold;font-size: 18px;color:#00EE00;background-color:#F0E6FF">系统基础环境:</div>';
$Amessage["strShow"] .='<div class="line"></div>';
$Amessage["strShow"] .= '行业之星当前版本：'.$Aconf['OSUNIT_VERSION'].'&nbsp;&nbsp;&nbsp;<A HREF="http://www.osunit.com/article-57-0.html" target="_blank">查看最新版本</A>';
$Amessage["strShow"] .='<div class="line"></div>';
$Amessage["strShow"] .= PHP_OS.' / PHP v'.PHP_VERSION;
$Amessage["strShow"] .= @ini_get('safe_mode') ? ' Safe Mode' : NULL;
$Amessage["strShow"] .='<div class="line"></div>';
$Amessage["strShow"] .= $_SERVER['SERVER_SOFTWARE'];
$Amessage["strShow"] .='<div class="line"></div>';

if(@ini_get('file_uploads')) {
	$Amessage["strShow"] .= 'PHP文件上传限制：'.ini_get('upload_max_filesize');
} 
$Amessage["strShow"] .='<div class="line"></div>';

$Amessage["strShow"] .= 'Mysql版本：'.($oPub->version());
//显示登录信息
$sql = 'SELECT * FROM '.$pre.'admin_user  WHERE user_id = "'.$_SESSION['auser_id'].'" limit 1';		   
$Auser = $oPub->getRow($sql); 
$Auser["add_time"]   = date("Y-m-d H:i:s",$Auser["add_time"]);
$Auser["last_login"] = date("Y-m-d H:i:s",$Auser["last_login"]); 
$Ahome["Auser"]	     = $Auser;
$Ahome["nowName"]    = $nowName; 
$Ahome["strMessage"] = $strMessage;  
$Ahome["strShow"]    = $Amessage["strShow"]; 
assign_template($Aconf); 
$smarty->assign('home', $Ahome );
$smarty->display($Aconf["displayFile"]); 
?>
 
