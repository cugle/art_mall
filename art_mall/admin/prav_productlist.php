<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
} 
 
if( $_SESSION['apraid'] < 1)
{
   $strMessage = '此账号没有绑定经销商，不能操作！请通过管理员设置.<br/><br/><a href="adminuser.php">多管理员权限->管理员权限设置 ->指定管理经销商</a>';
   echo  showMessage($strMessage);
   exit;
}

 $praid = $oPub->getOne("SELECT praid FROM ".$pre."pravail WHERE praid = ".$_SESSION['apraid']." ORDER BY praid ASC LIMIT 1"); 
 if( $praid < 1)
 {
	$strMessage = '此经销商已不存在，不能操作！请通过管理员设置.<br/><br/><a href="adminuser.php">多管理员权限->管理员权限设置 ->指定管理经销商</a>';
	echo  showMessage($strMessage);
	exit;
}
/*------------------------------------------------------ */
//-- 批量删除产品记录
/*------------------------------------------------------ */

if ($_REQUEST['action'] == 'del') {
    if (isset($checkboxes)) {
		$prid = $prid +0;
        $count = 0;		
        foreach ($checkboxes AS $key => $id)
		{
			$id = $id +0; 
			$oPub->query("UPDATE ".$pre."pravail_producttxt SET states='1'   WHERE prid=$id AND domain_id='".$Aconf['domain_id']."'");  
			$oPub->query("UPDATE ".$pre."pravail_product SET states='1'  WHERE prid=$id AND domain_id='".$Aconf['domain_id']."'"); 
        }
        $strMessage =  "批量删除成功!";
		$tmpID = implode(",",$checkboxes);
   }  else if(isset($prid)) {
        $id = $prid; 
        $oPub->query("UPDATE ".$pre."pravail_producttxt SET states='1'  WHERE prid=$id AND domain_id='".$Aconf['domain_id']."'");  
        $oPub->query("UPDATE ".$pre."pravail_product SET states='1'   WHERE prid=$id AND domain_id='".$Aconf['domain_id']."'"); 
		$tmpID = $id;

		$strMessage =  "删除成功!";
   }  else {
      $strMessage =  "没有选择需要删除的信息!";
	  $tmpID = 0;
   }

}

/* 查询条件 */
$db_table = $pre."pravail_producttxt";
$filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'dateadd' : trim($_REQUEST['sort_by']);
$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
if($_REQUEST[prapcid]) {
	$where = "prapcid='$_REQUEST[prapcid]' and  praid='".$_SESSION['apraid']."' and states=0 AND  domain_id = '".$Aconf['domain_id']."'";
} else {
     $where = "states=0 and  praid='".$_SESSION['apraid']."' AND domain_id = '".$Aconf['domain_id']."'";
}


$db_table = $pre."pravail_producttxt";
$sql = "SELECT COUNT(*) as count FROM ".$db_table." AS a WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array('prapcid'=>$_REQUEST[prapcid],'sort_by'=>$filter['sort_by']); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table.
       " WHERE  $where ".
       " ORDER BY top DESC,".$filter['sort_by']." ".$filter['sort_order'].
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
if($row ) { 
	$db_table = $pre."pravail_productcat";
    foreach ($row AS $key=>$val)
    {
	   /* 查询是否为总站商品 */
	   if($val[main_prid] > 0 )
	   {
           /* 找到所有的分类到select start*/
           $db_table = $pre."pravail_productcat"; 
           $sql = "SELECT * FROM ".$db_table." where praid='".$_SESSION['apraid']."' AND domain_id=".$Aconf['domain_id']." ORDER BY prapcid ASC";
           $AnormAll = $oPub->select($sql);
           $Stropt = '<span id="prapcid_'.$val["prid"].'">';
           $Stropt .= '<SELECT NAME="prapcid" onchange="pro_list_edit(\'prapcid\',\''.$val["prid"].'\',this.options[this.options.selectedIndex].value)">';
           $tmp = ($val['prapcid'] == 0)?'SELECTED':'';
           $Stropt .= '<OPTION VALUE="0" '.$tmp.'>选择分类</OPTION>';
           while( @list( $k, $value ) = @each( $AnormAll) )
           {
              $selected = ($val['prapcid'] == $value["prapcid"])? 'SELECTED':'';
              $Stropt .= '<OPTION VALUE="'.$value["prapcid"].'" '.$selected.' >'.$value["name"].'</OPTION>'; 
           }
           $Stropt .= '</SELECT>';
		   $Stropt .= '</span>';
          /* 找到所有的分类到select end*/
          $row[$key]['acname'] = $Stropt;
	      /* 读取分公司价格 */
           $db_table = $pre."producttxt";
           $where = " prid = '".$val[main_prid]."' AND states=0 AND domain_id = '".$Aconf['domain_id']."'";
           $sql = "SELECT name,shop_price FROM ".$db_table." AS a WHERE 1 AND ". $where.' LIMIT 1';
           $rowtmp = $oPub->getRow($sql);
		   $row[$key]['name'] = $rowtmp[name];
           $row[$key]['shop_price'] = $rowtmp[shop_price];
          /*取得本公司报价 */
	       $db_table = $pre."price_history";
	       $sql = "SELECT shop_price    
	           FROM ".$db_table." 
			   where  prid = '".$val[main_prid]."'
			   AND praid = '".$Aprav['praid']."'
			   AND domain_id = '".$Aconf['domain_id']."' 
			   ORDER BY  dateadd DESC 
			   LIMIT 1";			
           $shop_price = $oPub->getOne($sql);
	       $row[$key]['shop_price'] = ($shop_price)?$shop_price:$row[$key]['shop_price'];		  

	   }  else {
		   $db_table = $pre."pravail_productcat";
            $sql = "SELECT name FROM ".$db_table." WHERE prapcid=$val[prapcid]";
		    $row2 = $oPub->getRow($sql);
		    $row[$key]['acname'] = $row2[name];
		}
        $row[$key]['dateadd']  = ($val['dateadd'])?date("m月d日 h:i", $val['dateadd']):'';

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
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$val["prid"].']" value="'.$val["prid"].'" />';
	   $StrtypeAll .= '</TD>';
       $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<span id="name_'.$val["prid"].'">';
	   $StrtypeAll .= $val["name"];
	   $StrtypeAll .= '</TD><TD align=left>';
	   $StrtypeAll .= $val['shop_price'];
	   $StrtypeAll .= '</TD><TD align=left>'.$val["acname"].'</TD>';

   
       $tmpstr = ($val["top"])?'是':'<font color="CCCCCC">否</font>';
	   $StrtypeAll .= '<TD align=left id="top_'.$val["prid"].'"><span style="cursor:pointer" onmousedown="return pro_list_edit(\'top\',\''.$val["prid"].'\','.$val["top"].')">'.$tmpstr.'</span></TD>';	  

	   $StrtypeAll .= '<TD align=left>'.$val["dateadd"].'</TD>';
       $StrtypeAll .= '<TD align=center>';
	   $StrtypeAll .= '<a href="../shop_ht.php?id='.$_SESSION['apraid'].'&prid='.$val["prid"].'" target="_blank"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="阅读"></a>';
       if($val["main_prid"] == 0)
	   {
	      $StrtypeAll .= ' _ <a href="prav_productsend.php?prid='.$val["prid"].'&action=edit&page='.$pagenew.'" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	      $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?prid='.$val["prid"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
       }
	   $StrtypeAll .= '</TD></TR>';    
}



/* 找到所有的分类到select start*/
$db_table = $pre."pravail_productcat"; 
$sql = "SELECT * FROM ".$db_table." where praid='".$_SESSION['apraid']."' AND domain_id=".$Aconf['domain_id']." ORDER BY prapcid ASC";
$AnormAll = $oPub->select($sql);

$Stropt = '<SELECT NAME="prapcid" onchange="chkSearch(this.options[this.options.selectedIndex].value)">';
$tmp = ($filter['prapcid'] == 0)?'SELECTED':'';
$Stropt .= '<OPTION VALUE="0" '.$tmp.'>所有分类</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($_GET['prapcid'] == $value["prapcid"])? 'SELECTED':'';
       $Stropt .= '<OPTION VALUE="'.$value["prapcid"].'" '.$selected.' >'.$value["name"].'</OPTION>'; 
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
 
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="button">
<tr>
  <td align="middle">
	   <span style="float: left"><a href="prav_productlist.php"> [经销商产品列表]</a>        
	   <?php echo $Stropt;?>
       </span>
	   
	   <span style="float: right"><a href="prav_productsend.php" style="color:#FF0000"> [经销商添加新产品]</a> </span>
 </td>
</tr>
</table>

<TABLE width="100%" border=0>
  <TR class=bg1>
    <TD align=center colspan="7">
	<span>[注：总站报价产品，只能浏览不能编辑但可以修改经销商自己的分类及置顶状态]</span>
	</TD> 
  </TR>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
  <TR class=bg5>
    <TD width="5%" align=left>序号</TD>
    <TD width="35%" align=left>标题</TD>
	<TD width="10%" align=left>价格</TD>
	<TD width="10%" align=left>分类</TD>
	<TD width="5%" align=left>
	<a href="prav_productlist.php?prapcid=<?php echo $_REQUEST['prapcid'].'&sort_by=top&page='.$pagenew.'"';?> target="main">
	置顶
	</a>
	</TD>
	<TD width="15%" align=left>时间</TD>
	<TD width="10%" align=center>操作</TD>
  </TR>

 <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=right colspan="7">
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
 </table>
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
	 
   location="<?php echo $_SERVER["PHP_SELF"];?>?prapcid=" + obj;

}

function pro_list_edit(edit,prid,edit_val)
{
     obj = edit + "_" + prid;
     var strTemp = "ajax_prav_pro_list_edit.php?op=" + edit + "&prid=" + prid + "&edit_val=" + escape(edit_val);
	 //alert(strTemp);
	 send_request(strTemp);
}
</script>


<?php
include_once( "footer.php");
?>
