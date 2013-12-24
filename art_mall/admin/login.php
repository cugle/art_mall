<?php
define('IN_OUN', true);
include_once( "../includes/command.php");

$smarty->template_dir   .= $Aconf['manage_dir']; 
$Aconf["template_path"]  = "../".$Aconf["template_path"]."admin/"; 

$un_domain_user_id =  'domain_user_id'; 
$un_action_list    =  'action_list';
$un_user_rank      =  'user_rank'; 
$un_auser_id       =  'auser_id'; 
if($_SESSION['auser_id'] > 0)
{
	 
}else
{

	if($_POST['user_name'] != '' && $_POST['password'] != '') {   

		if(strtoupper($_SESSION['vCode']) != strtoupper($vcode) || empty($vcode)){ 
			 echo "<SCRIPT language='javascript'>\nalert('验证码错误!!');top.location='index.php';</script>";
			 exit;		
		} 

		$db_table = $pre."admin_user";
		$user_name = clean_html(trim($_POST['user_name']));
		$password  = md5(trim($_POST['password']));
	 
		$Auser = $oPub->getRow('SELECT * FROM '.$pre.'admin_user WHERE `user_name` LIKE "'.$user_name.'" AND `password` LIKE "'.$password.'" AND `domain_id` = "'.$Aconf['domain_id'].'" limit 1');  
		if($Auser['user_id'] > 0) { 
		   /* 记录登录ip 及地址 */
		   set_admin_session($Auser['user_id'],$Auser['user_name'],$Auser['action_list'],$Auser['articlecat_list'],$Auser['praid'],$Aconf['domain_url'],$Aconf['domain_id'],$Aconf['domain_user_id']);
	 
		   $last_ip = real_ip();
		   $Afields=array('last_login'=>gmtime(),'last_ip'=>$last_ip);
		   $condition = 'user_id="'.$_SESSION['auser_id'].'"';
		   $oPub->update($pre."admin_user",$Afields,$condition); 
		   /*记录登录日志 */ 
		   $change_desc = real_ip().' |  '.date("m月d日 h:i").' |  domain_id:'.$Aconf['domain_id']; 
		   /* 记录后台登录地址ip 到 session   如果取得的用户ip 与记录的IP地址不符合则自动退出 */
		   $Alast_ip = explode('.',$last_ip);
		   $last_ip  = $Alast_ip[0].'.'.$Alast_ip[1].'.'.$Alast_ip[2];
		   $_SESSION['admin_login_ip'] = $last_ip; 
		   if(empty($last_ip)) {
			   $change_desc .= ' | '.$user_name.' 得不到你的IP地址，操作被禁止';
			   $Afields=array('user_id'=>$Auser['user_id'],'type'=>'login','change_desc'=>$change_desc,'domain_id'=>$Aconf['domain_id']);
			   $oPub->install($db_table,$Afields);
			   echo "<SCRIPT language='javascript'>\nalert('得不到你的IP地址，操作被禁止');top.location='/';</script>";
			   exit;
			} else {
			   $change_desc .= ' | '.$user_name.' 成功登陆';
			   $Afields=array('user_id'=>$Auser['user_id'],'type'=>'login','change_desc'=>$change_desc,'domain_id'=>$Aconf['domain_id']);
			   $oPub->install($pre."account_log",$Afields);
			}
	 
			//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			//header("Cache-Control: no-cache, must-revalidate");
			//header("Pragma: no-cache"); 
			//header("Location: index.php"); 
			//echo "<SCRIPT language='javascript'>top.location='index.php';</script>";
			//exit;
		} else {
		   echo "<SCRIPT language='javascript'>\nalert('登录失败!!\\n用户名与密码错误');top.location='login.php';</script>";
		   exit;
		}
		unset($Auser); 
	}
} 
$Ahome["Auser"]	  = $Auser;
$Ahome["nowName"] = $nowName; 
$Ahome["strMessage"] = $strMessage;  
assign_template($Aconf); 
$smarty->assign('home', $Ahome );
$smarty->assign('auser', $_SESSION );
$smarty->display($Aconf["displayFile"]); 
?> 
