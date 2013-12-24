<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
//include_once($ROOT_PATH.'includes/ckeditor/ckeditor.php') 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
} 
 
 

$db_table = $pre."vote_poll";
$where = "domain_id=".$Aconf['domain_id'];
if($vtid > 0)
{
	$where .= ' and vtid="'.$vtid.'"';
}
$sql = "SELECT COUNT(*) as count FROM ".$db_table."  WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array('vtid'=>$_REQUEST[vtid]); 
$strOffSet = $page->OffSet();
//name tel add_time
$sql = "SELECT * FROM ".$db_table. " WHERE  $where ". " ORDER BY vipid desc LIMIT ". $strOffSet;
$row = $oPub->select($sql); 
$StrtypeAll = '';
$n = 0;
if($row)
foreach ($row AS $key=>$val)
{
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';  
 
	   $StrtypeAll .= '<TD align=left>'.$val["ip"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["user_name"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["computer"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.date("Y-m-d H:i",$val["add_time"]).'</TD>';
 
       $StrtypeAll .= '</TR>';    
}


/* 找到所有的vote_title 到select start*/
$db_table = $pre."vote_title"; 
$sql = "SELECT vtid,vt_name FROM ".$db_table." where states = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY vtid DESC";
$AnormAll = $oPub->select($sql);
$Stropt = '<SELECT NAME="vtid" onchange="chkSearch(this.options[this.options.selectedIndex].value)">';
$tmp = ($_REQUEST['vtid'] == 0)?'SELECTED':'';
$Stropt .= '<OPTION VALUE="0" '.$tmp.'>调查选项</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($_REQUEST['vtid'] == $value["vtid"])? 'SELECTED':'';
       $Stropt .= '<OPTION VALUE="'.$value["vtid"].'" '.$selected.' >'.$value["vt_name"].'</OPTION>';   
}
$Stropt .= '</SELECT>';
/* 找到所有的分类到select end*/ 
?>
<?php
include_once( "header.php"); 
if ($strMessage != '') {
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?> 
<table width="100%" border="0" cellspacing="1" cellpadding="1" class="button">
  <TR class=bg1>
    <TD align=left colspan="9">
	   <span style="float: left">       
	   <?php echo $Stropt;?>
       </span> 
	</TD> 
  </TR>
</table>

<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
<TABLE width="100%" border=0>  
	<TR class=bg5>
		
		<TD align=left>IP</TD>
		<TD align=left>帐号</TD>
		<TD align=left>环境</TD>
		<TD align=left>时间</TD>
 
	</TR>

	<?php echo $StrtypeAll;?>

	<TR class=bg5>
		<TD  align=right colspan="4">
		<span style="float: left">
		全选删除:<input onclick=selectAll() type="checkbox" name="check_all"/>
		<INPUT TYPE="submit" name="submit" value="确认删除" style="background-color: #FF9900">
		<INPUT TYPE="reset" name="reset" value="恢复" style="background-color: #CCFF99"> 
		<input type="hidden" name="vtid" value="<?php echo $_REQUEST['vtid'];?>" />
		<INPUT TYPE="hidden" name="action" value="del"> 
		</span>
		<span style="float: right">
		<?php echo $showpage = $page->ShowLink();?>
		</span>
		</TD>
	</TR> 
</table>
</form>
<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
<script type="text/javascript" language="JavaScript"> 
	function selectAll(){
		xx = listForm.check_all.checked
		for(var i=0;i<listForm.length;i++)
		{
			if(listForm.elements[i].type=="checkbox")
				listForm.elements[i].checked=xx;
		}
	}

	function chkSearch(obj)
	{ 
		location="<?php echo $_SERVER["PHP_SELF"];?>?vtid=" + obj; 
	} 
</script>
<?php
include_once( "footer.php");
?>