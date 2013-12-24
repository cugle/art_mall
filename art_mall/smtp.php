<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");  

//if(empty($u_mid)){
	//smindex('未登录或登录超时，请重新登录','login.html','error',"$ajax");
//	$url='login.html';
//	$smindex='smindex';
//	$messagetype='error';
//	$message='未登录或登录超时，请重新登录';
//	include template($smindex);
//	exit();
//} 
if($op == "usercheck" && $id > 0)
{
	$allowemail = false;
	//发送邮箱验证
	$row = $oPub->getRow('SELECT user_name,email FROM '.$pre.'users WHERE  id="'.$id.'"  limit 1'); 
	$user_name = $row['user_name'];
	$email = $row['email'];
	unset($row);
	if(!empty($email))
	{
		$Urow = $oPub->getRow('SELECT user_id,estats FROM '.$pre.'usersverify WHERE  user_id="'.$id.'"  limit 1'); 
		if($Urow['user_id'] > 0)
		{
			if($Urow['estats'] < 1 )
			{
				$allowemail = true; 
			}else
			{
				$strMessages = '已通过验证，不需要重复！';
			}
		}else
		{
			//插入验证表
			$Afields=array('user_id'=>$id,'email'=>$email,'domain_id'=>$Aconf['domain_id']);
			$id = $oPub->install($pre.'usersverify',$Afields);
			$allowemail = true; 
		} 

		if($allowemail)
		{
			$mailsubject    = "行业之星用户注册验证码";//邮件主题 
			$mailbody="<h4>您好，你注册帐号为：".$user_name."</h4>";
			$mailbody.="<p>行业之星已收到了您的注册信息，现在请激活用户名，以便您能登录行业之星！</p>";
			$mailbody.="<p><a href='".$Aconf['domain_url']."user.php?o=emailcheck&id=".$id."&check=".md5($email)."' targe/t='_blank'>点击这里，立即认证您的邮箱</a></p>";
			$mailbody.="<p>若您无法直接点击链接，也可复制以上地址到浏览器地址栏中：".$Aconf['domain_url']."user.php?o=emailcheck&id=".$id."&check=".md5($email)."</p>";
			$mailbody.="<p>欢迎您使用行业之星！</p>"; 
		}

	}else
	{
		$strMessages = '帐号没绑定邮箱！';
	}
}
//op=findpass&id='.$id.'&p='.$password.'
if($op == "findpass" && $id > 0 && !empty($p))
{
		$row = $oPub->getRow('SELECT id,email,password FROM '.$pre.'users WHERE   id="'.$id.'" limit 1'); 
		if($row['id'] > 0 )
		{   
			$oPub->query( 'delete from '.$pre.'userfindpass where user_id="'.$id.'"' );
			$row = $oPub->getRow('SELECT id,email,user_name,password FROM '.$pre.'users WHERE   id="'.$id.'" limit 1'); 
			$t = $row['password'].$id;
			$password = mkmd5($t); 
 
			if($p == $password)
			{    
				//$checkmd5 = md5($password);
				$oPub->query( 'INSERT INTO '.$pre.'userfindpass(user_id,findpass)VALUES ("'.$id.'","'.$password.'")' ); //用于找回密码的验证 
				$email = $row['email'];
				$mailsubject    = "行业之星用户找回密码";//邮件主题 
				
				$mailbody="<h4>您好：".$row['user_name']."</h4>";
				$mailbody.="<p>行业之星已收到了您的找回密码需求！</p>";
				$mailbody.="<p><a href='".$Aconf['domain_url']."user.php?o=findpass&id=".$id."&fd=".$password."' targe/t='_blank'>点击这里，修改你的新密码</a></p>";
				$mailbody.="<p>若您无法直接点击链接，也可复制以上地址到浏览器地址栏中：".$Aconf['domain_url']."user.php?o=findpass&id=".$id."&fd=".$password."</p>";
				$mailbody.="<p>欢迎您使用行业之星！</p>"; 

				$allowemail = true;

			}else
			{
				$strMessages = '错误验证。不能修改密码！';
				$allowemail = false;
			}
		}else
		{
				$strMessages = '传递错误！';
				$allowemail = false;
		}
}

if($allowemail)
{ 
	$allowemail = false;
	//取得发送邮箱的 smtp
	$Rsyssmtp = $oPub->getRow('SELECT * FROM '.$pre.'syssmtp WHERE  domain_id="'.$Aconf['domain_id'].'"  limit 1');
	if($Rsyssmtp['id'] > 0)
	{
		//smtpusermail	 smtppass	 smtpserver	 smtpport
		$allowemail		= true;
		$smtpserver		= $Rsyssmtp['smtpserver'];//SMTP服务器 
		$smtpserverport = $Rsyssmtp['smtpport'];  //SMTP服务器端口 
		$smtpusermail	= $Rsyssmtp['smtpusermail'];//SMTP服务器的用户邮箱  
		$smtpuser       = $Rsyssmtp['smtpusermail'];//SMTP服务器的用户帐号 
		$smtppass       = $Rsyssmtp['smtppass'];//SMTP服务器的用户密码 
		//$mailsubject    = "行业之星用户注册验证码";//邮件主题  
		$mailsubject    = "=?UTF-8?B?".base64_encode($mailsubject)."?=";  //解决标题乱码
		$smtpemailto    = "$email";//发送给谁 
		
	}else
	{//没有设置smtp,不能发送邮件 
		$strMessages = '超级用户没有设置SMTP,不能发送邮件';
		$allowemail  = false; 
	} 
}else
{
	$strMessages = '不正确来路，不能发送邮件';
	$allowemail  = false; 
}
 
if($allowemail)
{ 
	@header('Content-Type: text/html; charset=UTF-8');
	require("includes/smtp.php");
}else
{
	die("未知错误，请联系<A HREF=".$Aconf['domain_id'].">管理员</A>");
}

########################################## 
/*
$smtpserver = "mail.tdhuan.net";//SMTP服务器 
$smtpserverport =25;//SMTP服务器端口 
$smtpusermail = "mail@mail.tdhuan.net";//SMTP服务器的用户邮箱 
$smtpemailto = "$email";//发送给谁 
$smtpuser = "mail";//SMTP服务器的用户帐号 
$smtppass = "mail.tdhuan.net";//SMTP服务器的用户密码 
$mailsubject = "交换网用户邮箱认证";//邮件主题

$mailbody="<h4>您好：".$user_name."</h4>";
$mailbody.="<p>行业之星已收到了您的注册信息，现在请激活用户名，以便您能登录行业之星！</p>";
$mailbody.="<p><a href='".$Aconf['domain_url']."user.php?o=emailcheck&id=".$id."&check=".md5($email)."' targe/t='_blank'>点击这里，立即认证您的邮箱</a></p>";
$mailbody.="<p>若您无法直接点击链接，也可复制以上地址到浏览器地址栏中：".$Aconf['domain_url']."user.php?o=emailcheck&id=".$id."&check=".md5($email)."</p>";
$mailbody.="<p>欢迎您使用行业之星！</p>";
*/ 
//$mailbody = "许永自建邮件服务器测试 smtp 程序发送".$smtpusermail.'_____to___'.$smtpemailto.'  '.date("Y-m-d H:i:s");//邮件内容

$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件

##########################################

$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.

$smtp->debug = false;//是否显示发送的调试信息

if(!$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype)){
	$strMessages = '邮件发送失败，请重试！';  
}else
{
	$strMessages = '邮件发送成功，请检查你的邮箱：'.$email;  	
}
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
echo $strMessages;
?>








