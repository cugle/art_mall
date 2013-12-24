<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if(!empty($Aconf['priveMessage'])) {
   echo showMessage($Aconf['priveMessage']);
   exit;
}
 

if(!isset($action)) $action=false; 
if($action == 'edit' && $atid > 0)
{
	if($top > 0)
	{
		$Afields=array('top'=>0);
		$condition = 'top = '.$top.' and domain_id="'.$Aconf['domain_id'].'"';
		$oPub->update($pre."arti_tag",$Afields,$condition);
	}
	$atid = $atid +0; 
	$Afields=array('keys'=>trim($keys),'top'=>$top);
	$condition = 'atid = '.$atid.' and domain_id="'.$Aconf['domain_id'].'"';
	$oPub->update($pre."arti_tag",$Afields,$condition); 

}

if($action == 'edit'){ 
	$Ahome['arti_tag'] = $oPub->getRow('SELECT * FROM '.$pre.'arti_tag WHERE atid = "'.$id.'" and domain_id="'.$Aconf['domain_id'].'"'); 
}

if ($action == 'del')
{
	if (isset($_POST['checkboxes']))
	{
		$count = 0;
		$strid = '';
		foreach ($_POST['checkboxes'] AS $key => $id)
		{	
			$id = $id+0;
			$condition = "atid='".$id."' and domain_id='".$Aconf['domain_id']."'";  
			$oPub->delete($pre."arti_tag",$condition); 
		}
		$tmpID = ($strid)?substr($strid,0,-1):'';
		$strMessage =  "批量删除成功!";
	}
	else if(isset($id)) {
		
		$condition = 'atid='.$id.' and domain_id="'.$Aconf['domain_id'].'"';
		$oPub->delete($pre."arti_tag",$condition);  
		$tmpID = $id;
		$strMessage =  "删除成功!";
	} else
	{
		$strMessage =  "没有选择需要删除的信息!";
		$tmpID = 0;
	}

	if(!empty($tmpID))
	{
		$db_table = $pre.'account_log';
		$change_desc = real_ip().' |  '.date("m月d日 h:i").' |  domain_id:'.$Aconf['domain_id'];
		$change_desc .= ' | '.$_SESSION['auser_name'].' 关键词删除:'.$tmpID;
		$Afields=array('user_id'=>$_SESSION['auser_id'],'type'=>'artiDel','change_desc'=>$change_desc,'domain_id'=>$Aconf['domain_id']);
		$oPub->install($db_table,$Afields);
	}
}
 
 
$StrtypeAll ='';
$db_table = $pre."arti_tag";
$where    = ' where domain_id="'.$Aconf['domain_id'].'"';
$sql = "SELECT COUNT(*) as count FROM ".$db_table.$where ;
$row = $oPub->getRow($sql);  
$filter['record_count'] = $row["count"];
unset($row);
$page = new ShowPage;
$page->PageSize = $Aconf['set_pagenum'];
$page->PHP_SELF = PHP_SELF;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array();  

$strOffSet = $page->OffSet();


$sql = "SELECT * FROM ".$db_table.$where ." order by top desc,atid asc  LIMIT ". $strOffSet;
$row = $oPub->select($sql);
if($row) {
   $n = 0 ;
   foreach ($row AS $key=>$val) { 
		$tmpstr = ($n % 2 == 0)?"even":"odd";
		$n ++ ;
		$StrtypeAll .= '<TR class='.$tmpstr.'>';

		$StrtypeAll .= '<TD align=left>';
		$StrtypeAll .= '<input type="checkbox" name="checkboxes['.$val["atid"].']" value="'.$val["atid"].'" />';
		$StrtypeAll .= '</TD>';

		$StrtypeAll .= '<TD align=left>';
		$StrtypeAll .= ($val['art_pro_type'] < 1)?'新闻':'商品';
		$StrtypeAll .= '</TD>';

		$StrtypeAll .= '<TD align=left>';
		$StrtypeAll .= $val['keys'];
		$StrtypeAll .= '</TD>';

		$StrtypeAll .= '<TD align=left>';
		$StrtypeAll .= $val['top'];
		$StrtypeAll .= '</TD>';

		$sysrow = $oPub->getRow("SELECT main_domin,header_title,states FROM ".$pre."sysconfig where scid=".$val["domain_id"]." limit 1");  
		//$StrtypeAll .= '<TD align=left>';
		if($sysrow['states']==2)
		{
			$states =  '(VIP)';
		}elseif($sysrow['states']==1)
		{
			$states =  '(删除)';
		}else
		{
			$states = '';
		}
		
		$http = 'http://'.$sysrow['main_domin'].'/'.$SUBPATH;
			//$StrtypeAll .= '<A HREF="'.$http.'" target="_blank">'.$sysrow['header_title'].'</A>'.$states;
		//$StrtypeAll .= '</TD>';

		//arid art_pro_type domain_id
		if($val['art_pro_type'] < 1)
		{
			$trow = $oPub->getRow("SELECT name FROM  ".$pre."artitxt where arid='".$val['arid']."' limit 1");  
			$http = '<A HREF="'.$http.'article.php?id='.$val['arid'].'" target="_blank">'.$trow['name'].'</A>';
		}else
		{
			$trow = $oPub->getRow("SELECT name FROM  ".$pre."producttxt where prid='".$val['arid']."' limit 1"); 
			$http = '<A HREF="'.$http.'product.php?id='.$val['arid'].'" target="_blank">'.$trow['name'].'</A>';
		} 
		$StrtypeAll .= '<TD align=left>';
		$StrtypeAll .=$http ;
		$StrtypeAll .= '</TD>'; 

		$StrtypeAll .= '<TD align=left>';  
		$StrtypeAll .= ' <a href="'.$PHP_SELF.'?id='.$val["atid"].'&action=edit" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a>';
		$StrtypeAll .= ' _ <a href="'.$PHP_SELF.'?id='.$val["atid"].'&action=del" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a> ';  
		$StrtypeAll .= '</TD>';

		$StrtypeAll .= '</TR>'; 
   } 
}  

$Ahome["showpage"]      = $page->ShowLink();
$Ahome["page"]			= $pagenew;
$Ahome["StrtypeAll"]    = $StrtypeAll; 
$Ahome["nowName"]       = $nowName; 
$Ahome["strMessage"]    = $strMessage;  
   
assign_template($Aconf); 
$smarty->assign('home', $Ahome );  
$smarty->display($Aconf["displayFile"]);
?>
 
