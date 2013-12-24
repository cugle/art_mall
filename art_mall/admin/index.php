<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");
 
if(!empty($user_name) && !empty($password)) { 
	if(strtoupper($_SESSION['vCode']) != strtoupper($vcode) || empty($vcode))
	{  
		 $strMessage = '验证码错误,请重新登录！';
	}else
	{
		$user_name = clean_html($user_name); 
        if ($user->login($user_name, $password))
        {   
			if($_SESSION['ifmanger']!=1){exit("你不是管理员，不允许登陆后台管理<a href='./index.php'>点击返回</a>");}
			$Auser['id'] = $_SESSION['user_id'];   
		}else
		{
			$Auser['id'] = '';
		}
		
		if($Auser['id'] > 0)
		{ 
			//var_dump( $Auser );
			$user_id = $Auser['id'];
			/* 记录登录ip 及地址 */
			$last_ip = real_ip();
			$Afields=array('last_login'=>gmtime(),'last_ip'=>$last_ip); 
			$condition = 'user_id="'.$Auser['id'].'"';

			$oPub->update($pre."admin_user",$Afields,$condition); 
			/*记录登录日志 */ 
			$change_desc = real_ip().' |  '.date("m月d日 h:i").' |  domain_id:'.$Aconf['domain_id'];  
			$Alast_ip = explode('.',$last_ip);
			$last_ip  = $Alast_ip[0].'.'.$Alast_ip[1].'.'.$Alast_ip[2]; 
			if(empty($last_ip)) 
			{
			   $change_desc .= ' | '.$user_name.' 得不到你的IP地址，操作被禁止';
			   $Afields=array('user_id'=>$user_id,'type'=>'login','change_desc'=>$change_desc,'domain_id'=>$Aconf['domain_id']);
			   $oPub->install($db_table,$Afields);
			   $strMessage = $user_name.' 得不到你的IP地址，操作被禁止';
			} else  
			{
				$change_desc .= ' | '.$user_name.' 成功登陆';
				$Afields=array('user_id'=>$user_id,'type'=>'login','change_desc'=>$change_desc,'domain_id'=>$Aconf['domain_id']);
				$oPub->install($pre."account_log",$Afields);
				//记录session 
				$Auser = $oPub->getRow("SELECT user_id,user_name,action_list,articlecat_list,praid  FROM ".$pre."admin_user WHERE user_id = '".$user_id."'   and domain_id  = '".$Aconf['domain_id']."' limit 1");   
				set_admin_session($user_id,$Auser['user_name'],$Auser['action_list'],$Auser['articlecat_list'],$Auser['praid'],$Aconf['domain_url'],$Aconf['domain_id'],$Aconf['domain_user_id']);
				/* 设置cookie */
				$time = time() + 259200;  
				setcookie("OUN[auser_id]", $user_id, $time); 
			}
		}else
		{
			$strMessage = $user_name.' 用户名密码错误,请重新登录。';
		}
	 }  
}  
 
if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}
/* 缓存编号 */
$str_cache_id = $Aconf['domain_id']; 
$cache_id = sprintf('%X', crc32($str_cache_id));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {  
	$Ahome["Auser"]	  = $Auser;
	$Ahome["nowName"] = $nowName; 
	$Ahome["strMessage"] = $strMessage;  
	$Ahome["strShow"]= $Amessage["strShow"];  
	assign_template($Aconf); 
	$smarty->assign('home', $Ahome );   
	//var_dump($_SESSION);
	$smarty->assign('auser', $_SESSION );
}
$smarty->display($Aconf["displayFile"]);  
?> 