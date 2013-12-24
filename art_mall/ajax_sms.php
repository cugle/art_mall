<?php
define('IN_OUN', true);
include_once( "./includes/command.php");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
  
$str = '';
$tel = $tel + 0;
if(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$tel))
{ 
	$oPub->query("DELETE FROM ".$pre."vote_sns WHERE tel='".$tel."' and domain_id = '".$Aconf['domain_id']."'"); 
	$sns = msground(6);
	$oPub->query('INSERT INTO '. $pre.'vote_sns(tel,sns,add_time,domain_id)VALUES ("'.$tel.'","'.$sns.'","'.time().'","'.$Aconf['domain_id'].'")'); 
	$str = '请在60秒输入您收到的验证码！';
	$pszMsg = '最喜爱的银行投票验证码:'.$sns;
	smstousermobie($tel,$pszMsg);
}else
{  
	$str = '手机号码错误，请重新输入';
} 
echo $str;
?>