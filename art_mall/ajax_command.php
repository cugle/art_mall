<?php
define('IN_OUN', true);
include_once( "./includes/command.php");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//新网站，注册检查 start
if($op == 'reg_check' )
{
       /* 检查是否为英文数字 */ 
       $user_name = clean_html(trim($user_name));
       $Auser = $oPub->getRow('SELECT id as user_id FROM '.$pre.'users  WHERE `user_name` LIKE "'.$user_name.'"');  
       $str = ($Auser['user_id'] > 0)?'<IMG SRC="images/command/li_err.gif" WIDTH="16" HEIGHT="16" BORDER="0">已经被使用，不能注册':'<IMG SRC="images/command/li_ok.gif" WIDTH="16" HEIGHT="16" BORDER="0">允许注册。请牢记域名：'.$user_name.'.'.$Aconf['mail_url'].' 后台管理帐号：'.$user_name;
} elseif($op == 'help_reg')
{ 
	   $user_name   = ($user_name)?$user_name:'用户名';
	   $str .= '?</span><br/><span style="font-size:18px;color: #CCCCCC;background-color: #333333;" >';
	   $str .= '如：www.xy58.com你将拥有独立的企业网站。<br/>';
	   $str .= '1.填写此项，需要把你备案过的域名指向："'.$_SERVER["SERVER_ADDR"].'".<br/>';
	   $str .= '2.也可以通过：http://'.$user_name.'.'.$Aconf['mail_url'].' 访问你的网站.<br/>';
	   $str .= '3.如果没有独立域名，你可与我们客户QQ联系帮你申请.<br/>';
	   $str .= '</span><span>';
} 
//新网站，注册检查 end

//商品 上一个，下一个调用 start
if($op == 'prepro' && $prid > 0){
	//上一个
	$pre_row = $oPub->getRow("SELECT prid,name  FROM  ".$pre."producttxt  WHERE  states <> 1   AND domain_id=".$Aconf['domain_id'].
		" and prid > ".$prid." order by prid asc  LIMIT 1"); 
	if($pre_row["prid"] > 0){
		$rowproduct["pre_name"] = $pre_row["name"];  
		if($Aconf['rewrite']){
			$rowproduct["pre_url"] = "product-".$pre_row["prid"].".html";   
		}else{ 
			$rowproduct["pre_url"] = "product.php?id=".$pre_row["prid"];  
		}
		$str = '<a href="javascript:;" onclick="cpro(\'prepro\',\''.$pre_row["prid"].'\');" class="prepro">↑上一个商品</a>：<a href="'.$rowproduct["pre_url"].'" class="otherpro">'.$rowproduct["pre_name"].'</a>';
	}else{
		$str = "已经没有上一个商品";
	}
	
}
if($op == 'nextpro' && $prid > 0){
	//下一个 
	$next_row = $oPub->getRow("SELECT prid,name  FROM  ".$pre."producttxt  WHERE  states <> 1   AND domain_id=".$Aconf['domain_id']. " and prid < ".$prid." order by prid desc  LIMIT 1");
	if($next_row["prid"] >0 ){
		$rowproduct["next_name"] = $next_row["name"]; 	
		if($Aconf['rewrite']){ 
			$rowproduct["next_url"] = "product-".$next_row["prid"].".html";  
		}else{  
			$rowproduct["next_url"] = "product.php?id=".$next_row["prid"]; 
		}
		$str = '<a href="javascript:;" onclick="cpro(\'nextpro\',\''.$next_row["prid"].'\');" class="prepro">↓下一个商品</a>：<a href="'.$rowproduct["next_url"].'" class="otherpro">'.$rowproduct["next_name"].'</a>';
	}else{
		$str = "已经没有下一个商品";
	}
}
//商品 上一个，下一个调用 end

//网站用户注册 start
if($op == 'userreg')
{
   $user_name = getUtf8(clean_html(trim($user_name)));
   $Auser = $oPub->getRow('SELECT id FROM '.$pre.'users  WHERE `user_name` LIKE "'.$user_name.'"');  
   $str = ($Auser['id'] > 0)?'<IMG SRC="images/command/li_err.gif" WIDTH="16" HEIGHT="16" BORDER="0">已经被使用，不能注册':'<IMG SRC="images/command/li_ok.gif" WIDTH="16" HEIGHT="16" BORDER="0">允许注册';	 
} 

if($op == 'useremail')
{
   $email =  trim($email);
   $Auser = $oPub->getRow('SELECT id FROM '.$pre.'users  WHERE `email` LIKE "'.$email.'"');  
   $str = ($Auser['id'] > 0)?'<IMG SRC="images/command/li_err.gif" WIDTH="16" HEIGHT="16" BORDER="0">已经被使用，不能使用':'<IMG SRC="images/command/li_ok.gif" WIDTH="16" HEIGHT="16" BORDER="0">允许使用';	  
} 
//网站用户注册 end
//删除用户地址 start
if($op == 'uaddrsdel' || $op == 'addrstype')
{
	if($id > 0 && $_SESSION['user_id'] > 0)
	{
		if($op == 'uaddrsdel')
		{
			$oPub->query( 'delete from '.$pre.'uaddrs where users_id="'.$_SESSION['user_id'].'" and id="'.$id.'"' );
			$str = '';
		}

		if($op == 'addrstype')
		{
			$oPub->query( 'update '.$pre.'uaddrs set type=0 where users_id="'.$_SESSION['user_id'].'"' );
			$oPub->query( 'update '.$pre.'uaddrs set type=1 where users_id="'.$_SESSION['user_id'].'" and id="'.$id.'"' ); 
			$str = '<span style="color:#00bbff;background-color: #FF3300">已设置</span>';
		} 
	}
}
//删除用户地址 end
//删除购物车 start
if($op == 'carsdel')
{
	if($id > 0 && $_SESSION['user_id'] > 0)
	{
		if($op == 'carsdel')
		{
			$oPub->query( 'delete from '.$pre.'carts where users_id="'.$_SESSION['user_id'].'" and id="'.$id.'"' );
			$str = '';
		} 
 
	}
}
//删除购物车 end
//删除收藏 start
if($op == 'scdel')
{
	if($id > 0 && $_SESSION['user_id'] > 0)
	{
		if($op == 'scdel')
		{
			$oPub->query( 'delete from '.$pre.'ufv where users_id="'.$_SESSION['user_id'].'" and prid="'.$id.'"' );
			$str = '';
		} 
 
	}
}
//删除购物车 end
if(!empty($str)){ 
	echo $str;
}
?>