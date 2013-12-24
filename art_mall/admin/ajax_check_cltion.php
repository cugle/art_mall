<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
//header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
$strKeys = '<span style="color:#CC0000;margin-left: 30px">关联文章，编辑添加</span><br/>';

/* 得到关联文章的arid 数组 */ 
$art_pro_type = 0;
$arid = $arid +0;
$whereArid = ($arid > 0)?" AND arid <> $arid ":' ';
$Aarid = array();
if(!empty($keys0)) {
	 $keys = getUtf8( "$keys0");
     $row = $oPub->select("SELECT arid FROM ".$pre."arti_tag 
             WHERE `keys` LIKE '%$keys%'  AND art_pro_type = '$art_pro_type'  AND domain_id = ".$Aconf['domain_id']. $whereArid." ORDER BY atid DESC  LIMIT 5");  
     while( @list( $k, $v) = @each( $row) ) {
		 $arid = $v["arid"];
		 if (!in_array($arid, $Aarid)) {
			 array_push($Aarid, $arid);    
         }	   
     }
}
if(!empty($keys1)) {
	 $keys = getUtf8( "$keys1");
     $row = $oPub->select("SELECT arid FROM ".$pre."arti_tag 
             WHERE `keys` LIKE '%$keys%'   AND art_pro_type = '$art_pro_type'  AND domain_id = ".$Aconf['domain_id']. $whereArid."  ORDER BY atid DESC LIMIT 5"); 
     while( @list( $k, $v) = @each( $row) ) {
		 $arid = $v["arid"];
		 if (!in_array($arid, $Aarid)) {
			 array_push($Aarid, $arid);    
         }	   
     } 
}
if(!empty($keys2)) {
	 $keys = getUtf8( "$keys2");
     $row = $oPub->select("SELECT arid FROM ".$pre."arti_tag 
             WHERE `keys` LIKE '%$keys%' AND art_pro_type = '$art_pro_type'  AND domain_id = ".$Aconf['domain_id']. $whereArid." ORDER BY atid DESC LIMIT 5"); 
     while( @list( $k, $v) = @each( $row) ) {
		 $arid = $v["arid"];
		 if (!in_array($arid, $Aarid)) {
			 array_push($Aarid, $arid);    
         }	   
     }
}
/* 查询关联文章 */
$strArid = implode(',',$Aarid);
$cltionnum = 10;
$n = 0; 
if($strArid  != '')
{ 
   $row = $oPub->select("SELECT arid,name FROM ".$pre."artitxt 
          WHERE  domain_id = ".$Aconf['domain_id']." AND arid in($strArid)  ORDER BY arid DESC"); 
	while( @list( $k, $v) = @each( $row ) ) 
	{
		$arid = $v["arid"];
		$strKeys .= '标题：<input type="text" name="keysname[]" value="'.$v["name"].'" size="50"/>';
		$str = "";
		if($Aconf['rewrite'])
		{
			$strUrl = "article-".$arid."-0.html";
		}else{
			$strUrl = "article.php?id=".$arid;
		}
		$strKeys .= '网址：<input type="text" name="keyshttp[]" value="'.$strUrl.'" size="50"/>';
		$strKeys .= '<A HREF="../'.$strUrl.'" target="_blank"> 详情>> </A><br/>';
		$n ++ ;
		if($n >= $cltionnum)
		{
			break;
		}
	}
}

for($n; $n < $cltionnum; $n++) {
    $strKeys .= '标题：<input type="text" name="keysname[]" value="" size="50"/>';
    $strKeys .= '网址：<input type="text" name="keyshttp[]" value="" size="50"/><br/>';
}

echo $strKeys;
?>