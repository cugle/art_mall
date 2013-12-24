<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//op=" + edit_price + "&prid=" + prid 

$prid = $_GET['prid'] + 0;
$edit_val = getUtf8( "$_GET[edit_val]");
$str = '';
if($prid)
{
	   $db_table = $pre."pravail_producttxt";
        /* 置顶 */
        if($_GET['op'] == 'top')
       {
   
	       $edit_val = ($edit_val == 1)?0:1;
           $sql = "UPDATE " . $db_table . " SET 
			      `top`='".$edit_val."' 
	               WHERE `prid` =".$prid." and `domain_id`=".$Aconf['domain_id'];
           $oPub->query($sql);
           
		   $tmpstr = ($edit_val)?'是':'否';
		   $str = '<span style="cursor:pointer" onmousedown="return pro_list_edit(\'top\',\''.$prid.'\','.$edit_val.')">'.$tmpstr.'</span>';
       }
        if($_GET['op'] == 'prapcid')
       {
   
	       if($edit_val)
		   {
               $sql = "UPDATE " . $db_table . " SET 
			      `prapcid`='".$edit_val."' 
	               WHERE `prid` =".$prid." and `domain_id`=".$Aconf['domain_id'];
               $oPub->query($sql);
           }

           /* 找到所有的分类到select start*/
           $db_table = $pre."pravail_productcat"; 
           $sql = "SELECT * FROM ".$db_table." where praid='".$_SESSION['apraid']."' AND domain_id=".$Aconf['domain_id']." ORDER BY prapcid ASC";
           $AnormAll = $oPub->select($sql);
           $Stropt = '<span id="prapcid_'.$prid.'">';
           $Stropt .= '<SELECT NAME="prapcid" onchange="pro_list_edit(\'prapcid\',\''.$prid.'\',this.options[this.options.selectedIndex].value)">';
           $tmp = ($edit_val == 0)?'SELECTED':'';
           $Stropt .= '<OPTION VALUE="0" '.$tmp.'>选择分类</OPTION>';
           while( @list( $k, $value ) = @each( $AnormAll) )
           {
              $selected = ($edit_val == $value["prapcid"])? 'SELECTED':'';
              $Stropt .= '<OPTION VALUE="'.$value["prapcid"].'" '.$selected.' >'.$value["name"].'</OPTION>'; 
           }
           $Stropt .= '</SELECT>';
		   $Stropt .= '</span>';

		   $str = $Stropt;
       }
}
echo $str;
?>
