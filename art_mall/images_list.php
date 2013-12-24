<?php
define('IN_OUN', true);
include_once( './includes/command.php');
include_once( ROOT_PATH."includes/item_set.php"); 
/* 产品图片列表 */
$show_img_list = false;
$strMessage = '无对应图库';
$id = $id + 0;
 
if($op == 1){
	$prid = $id;
}

if($prid)
{
   $prid = $prid + 0;  
   $rowproduct = $oPub->getRow('SELECT  a.prid,a.pcid,a.name FROM  '.$pre.'producttxt as a WHERE  a.states <> 1 AND a.domain_id="'.$Aconf['domain_id'].'" AND a.prid="'.$prid.'" limit 1'); 
   if($rowproduct)
	{
		/* 当前位置导航 */ 
		$row = $oPub->getRow('SELECT name,fid FROM '.$pre.'productcat where  pcid = "'.$rowproduct["pcid"].'" AND domain_id="'.$Aconf['domain_id'].'" LIMIT 1'); 
		$fid = $row['fid'];  
		if($Aconf['rewrite']){
			$nowcatname =  '<a href="products-'.$rowproduct["pcid"].'-0-0-0-0.html">'.$row["name"].'</a>'; 
			$nowproname =  '<a href="product-'.$prid.'.html">'.$rowproduct["name"].'</a>';
			$products = 'products.html';
		}else
		{
			$nowcatname =  '<a href="products.php?id='.$rowproduct["pcid"].'">'.$row["name"].'</a>';
			$nowproname =  '<a href="product.php?id='.$prid.'">'.$rowproduct["name"].'</a>';
			$products = 'products.php';
		}
		$rowproduct["nowproname"] = $nowproname;
		$idtype = "pcid";
		$strPrenave = pre_node($fid,$pre."productcat",$idtype,$products,true);

		$Aconf['header_title'] = $Aweb_url['images_list'][0]."|".$Aconf["web_title"]; 
		$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A></li><li><A HREF="'.$products.'">'.$Aweb_url["products"][0].'</a></li>'.$strPrenave.' <li>'.$nowcatname.'</li><li>'.$nowproname.' </li>'; 
		$Aconf["header_title"] = '图库：'.$rowproduct['name'].'|'.$row['name'].'|'.$Aconf['header_title'];  
		/* 当前位置导航负值结束 */ 
		/* 像册列表 */ 
		$row =  $oPub-> select('SELECT * FROM '. $pre.'product_file WHERE prid = "'.$prid.'"'); 
		if($row)
		{
			$i= 1;
			while( @list( $k, $v) = @each( $row ) ) {
				if($i==1){
					$rowproduct["first_img"] = $v["filename"];	
					break;
				}
			} 
		}
		$rowproduct["img_list"] = $row;
		$Ahome["producttxt"] = $rowproduct;
	} 
} elseif($arid)
{ 
	$arid = $arid + 0;
	$strid = 'arid'.$arid;
	$name = $oPub-> getOne('SELECT  a.name FROM '.$pre.'artitxt  as a WHERE  a.states <> 1 AND a.domain_id="'.$Aconf['domain_id'].'" AND a.arid="'.$arid.'"');
	if($name)
	{
		/* 像册列表 */ 
		$row =  $oPub-> select('SELECT thumb_url,filename,descs FROM '.$pre.'arti_file WHERE type="" and  arid ="'.$arid.'"'); 
		if($row)
		{ 
			$show_img_list = true;
			$strMessage = $name.' 图片列表';
		}
	}
   
}else
{
   $strMessage = '对应图库不存在！';
   $show_img_list = false;
} 

$strCache_id = $Aconf['domain_id']; 
$cache_id = sprintf('%X', crc32($str_cache_id));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {
    assign_template($Aconf); 
    $smarty->assign('home', $Ahome ); 
	$smarty->assign('images_list',  $row );
	$smarty->assign('ifshowimg',$show_img_list);
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome);  
}
$smarty->display($Aconf["displayFile"], $cache_id);  
?>
