<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

//删除订单 start
if($so == 'del' && $id > 0)
{
	$ddid = $oPub->getOne('SELECT ddid FROM  '.$pre.'dds where id="'.$id.'" and stats < 1 and domain_id = "'.$Aconf['domain_id'].'" limit 1'); 
	if(!empty($ddid))
	{
		$oPub->query('delete from '.$pre.'dds where  id="'.$id.'"' );
		$oPub->query('delete from '.$pre.'ddscarts where   ddid="'.$ddid.'" and domain_id = "'.$Aconf['domain_id'].'"' ); 
	} 
}
//删除订单 end
//确认订单，发货
if($ding_ok == 'yes' && $id > 0)
{ 
	//检测totalmoney wlpay
	$Rdds = $oPub->getROW('SELECT users_id,ddid,totalmoney,wlpay FROM  '.$pre.'dds where id="'.$id.'" and stats = 1 limit 1');
	$totalmoney = $Rdds['totalmoney'] + $Rdds['wlpay'];  
	$totalprice = $oPub->getOne('SELECT sum(totalprice) as totalprice FROM '.$pre.'ddscarts WHERE ddid="'.$Rdds['ddid'].'"');  
	if($totalmoney == $totalprice && !empty($wlname) && !empty($wlsn))
	{ 
		//发货确认 wlname wlsn

		$oPub->query('UPDATE '.$pre.'dds set stats=2,wlname="'.$wlname.'",wlsn="'.$wlsn.'" WHERE  id="'.$id.'" limit 1'); 
		//记录发货信息 
		$strMessages = '确认已发货！'; 
	}else
	{
		$strMessages = '金额不匹配，不能确认发货！';
	}
	$so = false; $id = false;

}

//显示订单详情 start 
if($so == 'show' && $id > 0 && $ding_ok <> 'yes')
{
	$Rdds = $oPub->getRow('SELECT * FROM  '.$pre.'dds where id="'.$id.'" and domain_id = "'.$Aconf['domain_id'].'" limit 1'); 
	if($Rdds['id'] > 0)
	{
		//订单列表
		//$oPub->query( 'select * from '.$pre.'ddscarts where  ddid="'.$ddid.'" 
		$row = $oPub->select('SELECT *  FROM '.$pre.'ddscarts WHERE ddid="'.$Rdds['ddid'].'"');  
		while( @list( $k, $v ) = @each( $row ) ) {  
			$x = $oPub->getRow('SELECT prid,name,shop_sn,shop_number,shop_price,shop_thumb FROM '.$pre.'producttxt WHERE prid = '.$v['prid']);
			$row[$k]['dateadd'] = date("Y-m-d h:i",$v['dateadd']);
			$row[$k]['shop_number']  = $x['shop_number'];
			$row[$k]['shop_price']  = $x['shop_price'];
			$row[$k]['shop_thumb']  = $x['shop_thumb'];
			$row[$k]['name']        = $x['name']; 
			$row[$k]['shop_sn']        = $x['shop_sn'];
			if($Aconf['rewrite']){
				$row[$k]["product_url"] = "product-".$x["prid"].".html";   
			}else{ 
				$row[$k]["product_url"] = "product.php?id=".$x["prid"];  
			} 
		} 
		$Rdds['ddscarts'] = $row; unset($row); 
		
		if($Rdds['stats']<1)
		{
			$Rdds['statsMessages'] = '未付款'; 
		}elseif($Rdds['stats']==1)
		{
			$Rdds['statsMessages'] = '已付款';
		}elseif($Rdds['stats']==2)
		{
			$Rdds['statsMessages'] = '已发货';
		}elseif($Rdds['stats']==3)
		{
			$Rdds['statsMessages'] = '已到货';
		}
	}
	$Ahome['showdds'] = $Rdds; 
	//显示订单详情 end
} else
{
	
	$where = '  domain_id = "'.$Aconf['domain_id'].'"'; 
	$Ahome['ding_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'dds WHERE  '. $where);  
	$page = new ShowPage;  
	$page->PageSize = $Aconf['set_pagenum'];
	$page->PHP_SELF = PHP_SELF;
	$page->Total = $Ahome['ding_count'];
	$pagenew = $page->PageNum();
	$page->LinkAry = array('o'=>'ding'); 
	$strOffSet = $page->OffSet(); 
	$Ahome['showpage'] = ($Ahome['ding_count']  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
	$row = $oPub->select('SELECT id,ddid,stats,pronums,totalmoney, time,sh_name,sh_address,sh_zip,sh_phone  FROM '.$pre.'dds WHERE '.$where.' order by id desc limit '.$strOffSet); 
	//ddid,stats,pronums,totalmoney, time,sh_name,sh_address,sh_zip,sh_phone
	while( @list( $k, $v ) = @each( $row ) )
	{ 
		if($v['stats']<1)
		{
			$row[$k]['statsMessages'] = '未付款'; 
		}elseif($v['stats']==1)
		{
			$row[$k]['statsMessages'] = '已付款';
		}elseif($v['stats']==2)
		{
			$row[$k]['statsMessages'] = '已发货';
		}elseif($v['stats']==3)
		{
			$row[$k]['statsMessages'] = '已到货';
		} 
		$row[$k]['time'] = date("Y-m-d H:i",$v['time']); 
	}//while( @list( $k, $v ) = @each( $row ) )
	$Ahome['ddid'] = $row;
}
//订单列表 end
$Ahome["nowName"]       = $nowName; 
$Ahome["strMessage"]    = $strMessage;  
assign_template($Aconf); 
$smarty->assign('home', $Ahome );  
$smarty->display($Aconf["displayFile"]); 
 
?> 