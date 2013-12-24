<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."prattri";
//post
if( $_POST['action'] == 'add'  )
{
	$Afields=array('pacid'=>$_POST['pacid'],'attr_name'=>$_POST['attr_name'],'attr_input_type'=>$_POST['attr_input_type'],'attr_values'=>$_POST['attr_values'],'sort_order'=>$_POST['sort_order'],'domain_id'=>$Aconf['domain_id']);
    $tpaid = $oPub->install($db_table,$Afields);
}

if( $_POST['action'] == 'edit'){
	    $_POST['paid'] = $_POST['paid']+0;
		$Afields=array('pacid'=>$_POST['pacid'],'attr_name'=>$_POST['attr_name'],'attr_input_type'=>$_POST['attr_input_type'],'attr_values'=>$_POST['attr_values'],'sort_order'=>$_POST['sort_order']);
	  $condition = "paid = ".$_POST['paid']." AND domain_id=".$Aconf['domain_id'];
	  $oPub->update($db_table,$Afields,$condition);
	 unset($_GET);
}

//get
$db_table = $pre."prattri";
if( $_GET['action'] == 'edit'){
	$_GET['paid'] = $_GET['paid'] + 0;
	$sql = "SELECT * FROM ".$db_table." where paid = ".$_GET['paid']." AND domain_id=".$Aconf['domain_id'];
	$Anorm = $oPub->getRow($sql);
}

if( $_GET['action'] == 'del'){
	$_GET['paid'] = $_GET['paid'] + 0;
	$condition = 'paid='.$_GET['paid'].' AND domain_id='.$Aconf['domain_id'];
	$db_table = $pre."prattri";
	$oPub->delete($db_table,$condition);
}


/* 查询条件 */
$db_table = $pre."prattri";
if($pacid)
{
	$pacid = $pacid +0;
	$where = " WHERE 1 AND pacid='$pacid'";
} else
{
     $where = "";
}
$sql = "SELECT COUNT(*) as count FROM ".$db_table." AS a ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array('pacid'=>$pacid); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table." 
        $where
		ORDER BY sort_order,paid ASC  
		LIMIT ". $strOffSet;
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>';
       if($value["domain_id"] == $Aconf['domain_id'])
	   {
	       $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$value["paid"].']" value="'.$value["paid"].'" />';
	   }
	   $StrtypeAll .= '</TD>';
       $StrtypeAll .= '<TD align=left>'.$value["attr_name"].'</TD>';
       
	   $db_table = $pre."prattcat";
       $sql = "SELECT paname FROM ".$db_table." WHERE pacid =".$value["pacid"];
       $row = $oPub->getRow($sql);

	   $StrtypeAll .= '<TD align=left>'.$row[paname].'</TD>';
	   $tmp = (!$value["attr_input_type"])?'手动录入':'从列表中选择';
	   $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';
       $attr_values = str_replace("\n", ", ",$value["attr_values"]);
	   $StrtypeAll .= '<TD align=left>'.$attr_values.'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["sort_order"].'</TD><TD align=left>';

       if($value["domain_id"] == $Aconf['domain_id'])
	   {
           $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?paid='.$value["paid"].'&pacid='.$value["pacid"].'&action=edit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> ';
	       $StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?paid='.$value["paid"].'&pacid='.$value["pacid"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
       }
       $StrtypeAll .= '</TD></TR>';  	   
}

/* 是否显示 */
$attr_input_type_1 = ($Anorm[attr_input_type])? 'SELECTED':'';
$attr_input_type_0 = (!$Anorm[attr_input_type])? 'SELECTED':'';

$Strattr_input_typeopt = '<SELECT name="attr_input_type">';
$Strattr_input_typeopt .= '<OPTION VALUE="0" '.$attr_input_type_0.'>手动录入</OPTION>';
$Strattr_input_typeopt .= '<OPTION VALUE="1" '.$attr_input_type_1.'>从列表中选择</OPTION>';
$Strattr_input_typeopt .= '</SELECT>';

/* 添加编辑的分类列表 */
$db_table = $pre."prattcat"; 
$sql = "SELECT * FROM ".$db_table." where  domain_id=".$Aconf['domain_id']." ORDER BY pacid ASC";
$Aprattcat = $oPub->select($sql);
$Strprattcatnorm = '<SELECT NAME="pacid">';
$tmp = ($Anorm['pacid'] == 0)?'SELECTED':'';
$Strprattcatnorm .= '<OPTION VALUE="0" '.$tmp.'>所有分类</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $Aprattcat) ) {
	   $n ++;
       $selected = ($Anorm['pacid'] == $value["pacid"])? 'SELECTED':'';
       $Strprattcatnorm .= '<OPTION VALUE="'.$value["pacid"].'" '.$selected.' >'.$value["paname"].'</OPTION>';

}
$Strprattcatnorm .= '</SELECT>';

/* 找到所有的分类到select start*/
$db_table = $pre."prattcat"; 
$sql = "SELECT * FROM ".$db_table." where  domain_id=".$Aconf['domain_id']." ORDER BY pacid ASC";
$Aprattcat = $oPub->select($sql);
$Strprattcat = '<SELECT NAME="pacid" onchange="chkSearch(this.options[this.options.selectedIndex].value)">';
$tmp = ($_REQUEST['pacid'] == 0)?'SELECTED':'';
$Strprattcat .= '<OPTION VALUE="0" '.$tmp.'>所有分类</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $Aprattcat) ) {
	   $n ++;
       $selected = ($_REQUEST['pacid'] == $value["pacid"])? 'SELECTED':'';
       $Strprattcat .= '<OPTION VALUE="'.$value["pacid"].'" '.$selected.' >'.$value["paname"].'</OPTION>';

}
$Strprattcat .= '</SELECT>';
/* 找到所有的分类到select end*/

?>

<?php
   include_once( "header.php");
	if ($strMessage != '')
	{
		 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
	} 
?>
<DIV class=content>
<TABLE width="100%" border=0>
  <TR>
   
    <TD width="13%" align="left" colspan="7">
	<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="form1" target="_self">
		<span style="float:right;"><a href="product_attrib.php"> [属性分类列表]</a> </span>
			<span style="font-weight: bold">属性名:</span>
			<input name="attr_name" type="text" value="<?php echo ($Anorm['paid'])?$Anorm['attr_name']:''?>" size="10" />
			<span style="font-weight: bold">分类:</span>
			<?php echo $Strprattcatnorm;?>
			<span style="font-weight: bold">排序:</span>
			<input name="sort_order" type="text" value="<?php echo ($Anorm['paid'])?$Anorm['sort_order']:'0'?>" size="3" />
			<br/>
			<span style="font-weight: bold">属性值录入方式:</span>
			<?php echo $Strattr_input_typeopt;?> (注：产品录入时，属性值的录入方式)
			<br/>
			<span style="font-weight: bold">可选值列表:</span>(注：每个选项为一行)
			<br/>
			<TEXTAREA NAME="attr_values" ROWS="4" COLS="5"><?php echo ($Anorm['paid'])?$Anorm['attr_values']:''?></TEXTAREA>

			<br/>
			<input type="hidden" name="action" value="<?php echo ($Anorm['paid'])?'edit':'add'?>" />
			<input type="submit" name="Submit" value="<?php echo ($Anorm['paid'])?'编辑':'增加' ?>" style="background-color: #FFCC66"/>
			<input type="hidden" name="paid" value="<?php echo ($Anorm['paid'])?$Anorm['paid']:'0'?>" />  
	 </form>
    </TD>
    
  </TR>  	
  <TR class=bg5>
    <TD width="5%" align=left>编号</TD>
	<TD width="15%" align=left>属性名称</TD>
	<TD width="10%" align=left><?php echo $Strprattcat;?></TD>
	<TD width="10%" align=left>属性值的录入方式</TD>
	<TD width="30%" align=left>可选值列表</TD>
	<TD width="5%" align=left>排序</TD>
    <TD width="5%" align=left>操作</TD>
  </TR>
  <form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="7" align=right>
	<span style="float: left">
	全选删除:<input onclick=selectAll() type="checkbox" name="check_all"/>
	<INPUT TYPE="submit" name="submit" value="确认删除" style="background-color: #FF9900">
	<INPUT TYPE="reset" name="reset" value="恢复" style="background-color: #CCFF99"> 
	<INPUT TYPE="hidden" name="action" value="del"> 
    </span>
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>	
	</TD>	
  </TR>
  </form>
</TABLE>
 
</DIV>
<BR/> 
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
	 
   location="<?php echo $_SERVER[PHP_SELF];?>?pacid=" + obj;

}
</script>

<?php
include_once( "footer.php");
?>