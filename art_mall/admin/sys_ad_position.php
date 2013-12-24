<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}
 
/*------------------------------------------------------ */
//-- 批量删除产品记录
/*------------------------------------------------------ */

if ($_REQUEST['action'] == 'del')
{
    if(isset($_REQUEST['position_id']))
   {
         $id = $_REQUEST['position_id'];
		 $db_table = $pre."ad_position";
         if($_SESSION['auser_name'] == 'admin')
         {
	        $sql = "delete from  ".$db_table."  WHERE position_id='".$id."'";
         }
		 else
	     {
           $sql = "delete from  ".$db_table."  WHERE position_id='".$id."' and domain_id = '".$Aconf['domain_id']."'";
	     }         
         $oPub->query($sql);

		$strMessage =  "删除成功!";
   } else {
      $strMessage =  "没有选择需要删除的信息!";
	  $tmpID = 0;
   } 
} 
/* 查询条件 */
$db_table = $pre."ad_position";
$where = "";
if($_SESSION['auser_name'] == 'admin') {
	$where = "";
}

$sql = "SELECT COUNT(*) as count FROM ".$db_table." AS a WHERE 1". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table.
       " WHERE 1 $where ".
       " ORDER BY position_id ASC " .
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
if($row )
{ 
	$db_table = $pre."sysconfig";
    foreach ($row AS $key=>$val)
    {
        $sql = "SELECT main_domin,header_title FROM ".$db_table." WHERE scid=$val[domain_id]";
		$row2 = $oPub->getRow($sql);
		$row[$key]['main_domin'] = $row2[main_domin];
		$row[$key]['header_title'] = ($row2[header_title])?$row2[header_title]:$row2[main_domin];
    }

}
$StrtypeAll = '';
$n = 0;

if($row)
foreach ($row AS $key=>$val)
{
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left> ['.$n.'] '.$val["position_name"].'</TD>'; 
	   $StrtypeAll .= '<TD align=left>'.$val["ad_width"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["ad_height"].'</TD>';
	   $StrtypeAll .= '<TD align=left>{insert name="exe" ads="ads_'.$val["ad_width"].'_'.$val["ad_height"].'_'.$val["position_id"].'"}</TD>';
       $StrtypeAll .= '<TD align=center>';
	   $StrtypeAll .= '<a href="sys_ad_position_send.php?position_id='.$val["position_id"].'&action=edit&page='.$pagenew.'" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?position_id='.$val["position_id"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
       $StrtypeAll .= '</TD></TR>';    
}
?>
<?php
include_once( "header.php");
if ($strMessage != '')
{
 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
	
<TABLE width="99%" border=0>
 
  <TR class=bg5>
    <TD width="25%" align=left>区块编号/广告位名称</TD> 
	<TD width="10%" align=left>位置宽度</TD>
	<TD width="10%" align=left>位置高度</TD>
	<TD width="45%" align=left>模版直接调用方法</TD>
	<TD width="10%" align=left>操作</TD>
  </TR>

 <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=right colspan="5">
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR>
  </form>
 </table>
<?php
include_once( "footer.php");
?>
