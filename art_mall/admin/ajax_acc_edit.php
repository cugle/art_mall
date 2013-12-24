<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
 
$edit_val = getUtf8( $edit_val);
$str = '';
if($acid)
{ 
 
	   if($_GET['op'] == 'utid')
	   {
		   

	       $edit_val = ($edit_val > 0)?$edit_val:0;
           $oPub->query("UPDATE " . $pre."articat SET `utid`='".$edit_val."' WHERE `acid` =".$acid." and `domain_id`=".$Aconf['domain_id']); 

			/* 找到所有的分类到select start*/ 
			$Auserstype = $oPub->select("SELECT name,orders FROM ".$pre."userstype where  domain_id=".$Aconf['domain_id']." ORDER BY orders ASC"); 

			$Stroptx = '<SELECT NAME="utid" onchange="acc_edit(\'utid\',\''.$acid.'\',this.options[this.options.selectedIndex].value)">';  
			$tmp = $edit_val < 1 ?'selected':'';
			$Stroptx .= '<OPTION VALUE="0" '.$tmp.' >游客浏览</OPTION>';
			if($Auserstype)
			{
				foreach ($Auserstype AS $k=>$v)
				{
					$selected = ($v['orders'] == $edit_val)? 'SELECTED':'';
					$Stroptx .= '<OPTION VALUE="'.$v['orders'].'" '.$selected.' >'.$v['name'].'</OPTION>'; 
				}
			}
			$Stroptx .= '</SELECT>';
			/* 找到所有的分类到select end*/ 
			$str = $Stroptx; 
	   }

}
echo $str;
?>