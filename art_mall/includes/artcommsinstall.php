<?php
/* 评论提交 用于文章品论*/
$strMessage = '';
if($act == 'install' && $arid > 0 ) {

	if(strtoupper($_SESSION['vCode']) != strtoupper($vcode) || empty($vcode)){ 
		$strMessage  ='验证码错误！';		
	} 

	$descs = clean_html($_POST["descs"]);
	$strMessage .= ($descs == '')?' 评论内容不能为空':'';
	$ip = real_ip();
	$strMessage .= ($ip == '')?'\n ERROR 1,系统错误，不能添加评论':'';
    /* 读取过滤的关键词 start */ 

	if(empty($strMessage)) { 
		$descs = filter($descs); 
		if(empty($descs)){
			$strMessage = '禁止的IP';
		}
	}

	if($Aconf['reg_support']>0 && $_SESSION['user_id']< 1)
	{
		$strMessage .= '必须登录后才能留言！';
	}
 
    /* 读取过滤的关键词 end */ 

    if($strMessage == '')
	{
	  $nowdatedd = gmtime();
      $row = $oPub->getRow('SELECT dateadd  FROM '.$pre.'arti_comms WHERE ip="'.$ip.'" and arid="'.$arid.'" order by dateadd desc limit 1'); 
	  $dateadd = ($row)?$nowdatedd - $row["dateadd"]:31;
	  if($dateadd > 30)
	   {
			//coms_type = 1/2/3/4 文章/产品/专题/对商家留言*/
			$oPub->query('INSERT INTO '.$pre.'arti_comms  (arid,acid,coms_type,name,descs,  ip , email ,  dateadd , states , `domain_id` )VALUES ("'.$arid.'","'.$acid.'",1,"'.$_POST[name].'","'.$descs.'","'.real_ip().'","'.$_POST["email"].'","'.gmtime().'", 0,"'.$Aconf['domain_id'].'")');  
			$oPub->query("UPDATE ".$pre."artitxt SET comms=comms + 1  WHERE domain_id = ".$Aconf['domain_id']." and arid = ".$arid); 
			$rowarticle['comms'] = $rowarticle['comms'] + 1;

			$strMessage =  "评论添加成功!";
			/* 如果是登陆用户，测记录用户文章 id coms_type = 1/2/3/4 */
			if ($_SESSION['user_id']) {
				$tmp = $oPub->getOne('SELECT users_id  FROM '.$pre.'users_comms WHERE users_id="'.$_SESSION['user_id'].'" AND coms_type = 1 AND arid = "'.$arid.'"'); 
				if ( !$tmp ) {				  
					$oPub->query('INSERT INTO '.$pre.'users_comms (users_id, coms_type ,arid,dateadd,domain_id) VALUES ("'.$_SESSION['user_id'].'",1, "'.$arid.'","'.gmtime().'","'.$Aconf['domain_id'].'")');  
				} else {
					$oPub->query('UPDATE '.$pre.'users_comms  SET dateadd = "'.gmtime().'"  WHERE users_id = "'.$tmp.'"'); 
				}
			}//$_SESSION['user_id']                                      
		} else {
          $strMessage = '30秒后才能继续添加评论!';
		}
	}
	unset($_POST);  
}
//评论结束
if(!empty($strMessage))
{ 
	$pnid = $pnid?$pnid:0;
	if($Aconf['rewrite'])
	{ 
		$strtmp  =  'article-'.$arid.'-'.$pnid.'.html';  
	}else{
		$strtmp  =  'article.php?id='.$arid.'&pnid='.$pnid; 
	} 
	echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='".$strtmp."';</script>";
	//echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');</script>";
	//exit;
} 
?>
