<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//op=" + edit_price + "&arid=" + arid 

$arid = $_GET['arid'] + 0;
$edit_val = getUtf8( $edit_val);
$str = '';
if($arid)
{
	$db_table = $pre."artitxt";
        /* 置顶 */
        if($_GET['op'] == 'top')
       {
           
	       $edit_val = ($edit_val == 1)?0:1;
           $sql = "UPDATE " . $pre."artitxt SET 
			      `top`='".$edit_val."' 
	               WHERE `arid` =".$arid." and `domain_id`=".$Aconf['domain_id'];
           $oPub->query($sql);
           
		   $tmpstr = ($edit_val)?'是':'否';
		   $str = '<span style="cursor:pointer" onmousedown="return art_list_edit(\'top\',\''.$arid.'\','.$edit_val.')">'.$tmpstr.'</span>';
       }elseif($_GET['op'] == 'focus'){
	       $edit_val = ($edit_val == 1)?0:1;
           $sql = "UPDATE " . $pre."artitxt SET 
			      `focus`='".$edit_val."' 
	               WHERE `arid` =".$arid." and `domain_id`=".$Aconf['domain_id'];
           $oPub->query($sql);
           
		   $tmpstr = ($edit_val)?'是':'否';
		   $str = '<span style="cursor:pointer" onmousedown="return art_list_edit(\'focus\',\''.$arid.'\','.$edit_val.')">'.$tmpstr.'</span>';
       }elseif($_GET['op'] == 'trundle'){
	       $edit_val = ($edit_val == 1)?0:1;
           $sql = "UPDATE " . $pre."artitxt SET 
			      `trundle`='".$edit_val."' 
	               WHERE `arid` =".$arid." and `domain_id`=".$Aconf['domain_id'];
           $oPub->query($sql);
           
		   $tmpstr = ($edit_val)?'是':'否';
		   $str = '<span style="cursor:pointer" onmousedown="return art_list_edit(\'trundle\',\''.$arid.'\','.$edit_val.')">'.$tmpstr.'</span>';
	   }elseif($_GET['op'] == 'ifpic'){
	       $edit_val = ($edit_val == 1)?0:1;
           $sql = "UPDATE " . $pre."artitxt SET 
			      `ifpic`='".$edit_val."' 
	               WHERE `arid` =".$arid." and `domain_id`=".$Aconf['domain_id'];
           $oPub->query($sql);
           
		   $tmpstr = ($edit_val)?'是':'否';
		   $str = '<span style="cursor:pointer" onmousedown="return art_list_edit(\'ifpic\',\''.$arid.'\','.$edit_val.')">'.$tmpstr.'</span>';
	   }elseif($_GET['op'] == 'states')
	   {
		   $Astates = array(0=>'未审',1=>'隐藏',2=>'已审');

	       $edit_val = ($edit_val > 0)?$edit_val:0;
           $sql = "UPDATE " . $pre."artitxt SET 
			      `states`='".$edit_val."' 
	               WHERE `arid` =".$arid." and `domain_id`=".$Aconf['domain_id'];
           $oPub->query($sql);
			$Stropt = '<SELECT NAME="states" onchange="art_list_edit(\'states\',\''.$arid.'\',this.options[this.options.selectedIndex].value)">';   
			foreach ($Astates AS $k=>$value)
			{
				$selected = ($edit_val == $k)? 'SELECTED':'';
				$Stropt .= '<OPTION VALUE="'.$k.'" '.$selected.' >'.$value.'</OPTION>'; 
			}
			$Stropt .= '</SELECT>';  
			$str = $Stropt;
	 
	   }
	   else{
		 $_GET['op'] = trim($_GET['op']);
          if($_GET['op'])
         {
           $sql = "UPDATE " . $pre."artitxt SET 
			      `".$_GET['op']."`='".$edit_val."' 
	               WHERE `arid` =".$arid." and `domain_id`=".$Aconf['domain_id'];
           $oPub->query($sql);

		   $str = '<INPUT TYPE="text" value="'.$edit_val.'" size="3" onDblClick=art_list_edit(\''.$_GET['op'].'\',\''.$arid.'\','.$edit_val.') />';
	     }
	  }

}
echo $str;
?>