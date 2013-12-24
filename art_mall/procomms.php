<?php
define('IN_OUN', true);
include_once( "./includes/command.php"); 

include_once( ROOT_PATH."ads.php");

$prid = $id  +0; 
/* 得到文章标题 */ 
$rowproducttxt = $oPub->getRow('SELECT prid,pcid,name  FROM '.$pre.'producttxt 
       WHERE prid="'.$prid.'" AND states <> 1 AND  domain_id = "'.$Aconf['domain_id'].'"  limit 1'); 
if( !$rowproducttxt ){
   $strMessage = '此产品已删除';
   echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='products.php';</script>";
   exit;
}else{
	if($Aconf['rewrite']){
		$rowproducttxt['product_url'] = 'product-'.$prid.'.html';
	}else{
		$rowproducttxt['product_url'] = 'product.php?id='.$prid;
	}
}
/* 评论提交 */
$db_table = $pre."product_comms";
$strMessage = '';

if($_POST[act] == 'install') { 

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
    if($strMessage == ''){
	  $nowdatedd = gmtime(); 
      $row = $oPub-> getRow('SELECT dateadd  FROM  '.$pre.'product_comms  WHERE ip="'.$ip.'" order by dateadd desc limit 1'); 
	  $dateadd = ($row)?$nowdatedd - $row['dateadd']:31;
	  if($dateadd > 30){
			$sql = 'INSERT INTO '. $pre.'product_comms (prid, descs ,  ip , email ,name, dateadd , states , `domain_id` ) VALUES ("'.$prid.'","'.$descs.'","'.real_ip().'","'.$email.'","'.$name.'","'.gmtime().'",0,"'.$Aconf['domain_id'].'")'; 
			$oPub-> query($sql); 
			 
			$oPub-> query("UPDATE " . $pre."producttxt SET comms =comms + 1 WHERE domain_id = ".$Aconf['domain_id']." and prid = ".$prid); 
			$strMessage =  "评论添加成功!";
			/* 如果是登陆用户，测记录用户文章 id coms_type = 1/2/3/4 */
			if ($_SESSION['user_id']) {
				$tmp = $oPub->getOne('SELECT users_id  FROM '.$pre.'users_comms WHERE users_id="'.$_SESSION['user_id'].'" AND coms_type = 2 AND arid = "'.$prid.'"'); 
				if ( !$tmp ) {				  
					$oPub->query('INSERT INTO '.$pre.'users_comms (users_id, coms_type ,arid,dateadd,domain_id) VALUES ("'.$_SESSION['user_id'].'",2, "'.$prid.'","'.gmtime().'","'.$Aconf['domain_id'].'")');  
				} else
				{
					$oPub->query('UPDATE '.$pre.'users_comms  SET dateadd = "'.gmtime().'"  WHERE users_id = "'.$tmp.'"'); 
				}
			}//$_SESSION['user_id']  
		}else
		{
          $strMessage = '30秒后才能继续添加!';
		}
	}
	unset($_POST);
}
//评论结束
if(!empty($strMessage)){  
	if($Aconf['rewrite']){ 
	 $strtmp  =  'procomms-'.$prid.'-0.html';  
	}else{
	 $strtmp  =  'procomms.php?id='.$prid; 
	} 
	echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='".$strtmp."';</script>";
	exit;
} 



if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$prid));
if (!$smarty->is_cached($Aconf['displayFile'], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php");
	/* 当前位置导航 */ 
	$row = $oPub->getRow('SELECT name,fid FROM '.$pre.'productcat where  pcid = "'.$rowproducttxt['pcid'].'" AND domain_id="'.$Aconf['domain_id'].'"  LIMIT 1'); 
	$fid = $row[fid];  
	if($Aconf['rewrite']){
		$nowcatname =  '<a href="products-'.$rowproducttxt['pcid'].'-0-0-0-0.html">'.$row['name'].'</a> '.$Aconf['nav_symbol'];
		$products = 'products.html';
	}else{
		$nowcatname =  '<a href="products.php?id='.$rowproducttxt['pcid'].'">'.$row['name'].'</a> '.$Aconf['nav_symbol'];
		$products = 'products.php';
	}
	$idtype = "pcid";
	$strPrenave = pre_node($fid,$pre."productcat",$idtype,$products,true);
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li><A HREF="'.$products.'">'.$Aweb_url["products"][0].'</a> '.$Aconf['nav_symbol'].'</li>'.$strPrenave.'<li>'.$nowcatname.'</li><li><a href="'.$rowproducttxt['product_url'].'">'.$rowproducttxt['name'].'</a> '.$Aconf['nav_symbol'].'</li><li>咨询</li>'; 
	$Aconf["header_title"] = $rowproducttxt['name'].'咨询|'.$row['name'].'|'.$Aconf['header_title'];   

	/* 评论列表 */
	$db_table = $pre.'product_comms';
	$strWhere = ($Aconf['support'])?' states = 3 ':' states <> 1 ';
	$strWhere = ' WHERE '.$strWhere.' AND prid='.$prid.' AND domain_id='.$Aconf['domain_id'];
	$sql = "SELECT count( * ) AS count FROM ".$db_table.$strWhere;
	$row = $oPub-> getRow($sql);
	$count = $row['count'];
	$rowproducttxt[count] = $row['count']; //传送留言总数

	$page = new ShowPage;  
	$page->PageSize = $Aconf['set_pagenum'];
	$page->Total = $count; 
	$pagenew = $page->PageNum(); 
	$page->PHP_SELF = PHP_SELF;
	$page->LinkAry = array('id'=>$prid); 
	$strOffSet = $page->OffSet();
	/* 翻页 */
	$rowproducttxt["showpage"] = ($row[count]  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 

	$sql = 'SELECT email,ip,descs,name,dateadd  FROM '.$db_table.$strWhere.' ORDER BY prcid asc limit '.$strOffSet;
	$AsppAll = $oPub-> select($sql);
	$n = ($_REQUEST["page"] > 1)? $Aconf['set_pagenum'] * ($_REQUEST["page"] - 1):0; 
	while( @list( $k, $v ) = @each( $AsppAll) ) { 
		$n ++ ; 
		$AsppAll[$k]['dateadd'] = date("y年n月j日h:i", $v['dateadd']);
		$Aip = explode(".",$v[ip]);
		$AsppAll[$k]["ip"] = '第'.$n.'楼 IP:'.$Aip[0].'.'.$Aip[1].'.*';
		$AsppAll[$k]["name"] = empty($v['name'])?'匿名':$v['name'];
		$AsppAll[$k]["descs"] =  clean_html($v[descs]);  
	}
	$rowproducttxt["pro_comms"] = $AsppAll;  

	$Ahome["procomms"] = $rowproducttxt;
	assign_template($Aconf); 
	$smarty->assign('home', $Ahome );
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id);

?>
