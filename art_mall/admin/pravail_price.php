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

/* 把总站产品转换为自有产品 */
if($_GET[change] == 'yes' && $_GET[prid])
{
	$prid = $_GET[prid] + 0 ;
	if($prid )
	{
		$myuser_id   = $_SESSION['auser_id'];
	    $db_table = $pre."pravail_producttxt";
		$sql = 'INSERT INTO '. $db_table . ' ( `main_prid` , `praid`, `user_id`, `dateadd` , `domain_id` ) VALUES ("'.$prid.'","'.$_SESSION['apraid'].'","'.$myuser_id.'",  "'.gmtime().'","'.$Aconf['domain_id'].'")'; 
        $oPub->query($sql);
		$strMessage = "转换成功，\n\n在经销商产品信息里能进一步修改"; 
	}   
}
/* 取消总站产品转换 */
if($_GET[change] == 'no' && $_GET[prid])
{
	$prid = $_GET[prid] + 0 ;
	if($prid )
	{
		$myuser_id   = $_SESSION['auser_id'];
	    $db_table = $pre."pravail_producttxt";
		$sql = "DELETE FROM " . $db_table . "
		        WHERE  `main_prid` = '".$prid."' 
			    AND    `praid` = '".$_SESSION['apraid']."'"; 
        $oPub->query($sql);
		/* 删除历史报价 ?? 暂不执行*/
	}   
}
/* 查询是否为分公司 $_SESSION['auser_name'] */
$sql = 'SELECT b.praid,b.pra_name FROM '.$pre.'pravail as b   
        where  b.domain_id = "'.$Aconf['domain_id'].'" 
		AND b.praid = "'.$_SESSION['apraid'].'"';
$Aprav = $oPub->getRow($sql);
$strPrid  =  '';
$strPname =  '不是代理商，没有需要维护的产品';
if($Aprav['praid'])
{
	/* 查询分公司能修改价格的产品 */
	$strPname = '['.$_SESSION['auser_name'].'] '.$Aprav['pra_name'];
	$strPname .= '代理商产品列表:';
}

if(true)
{
  /* 查询条件 */
  $db_table = $pre."producttxt";
  $where = "states=0 AND domain_id = '".$Aconf['domain_id']."'";

  $sql = "SELECT COUNT(*) as count FROM ".$db_table." AS a WHERE 1 AND ". $where;
  $row = $oPub->getRow($sql);
  $filter['record_count'] = $row[count];
  unset($row);
  $page = new ShowPage;
  $page->PageSize = 30;
  $page->Total = $filter['record_count'];
  $pagenew = $page->PageNum();
  $page->LinkAry = array(); 
  $strOffSet = $page->OffSet();
  $showpage = $page->ShowLink();

  $sql = "SELECT * FROM ".$db_table.
       " WHERE  $where ".
       " ORDER BY top DESC,dateadd DESC ".
       " LIMIT ". $strOffSet;
  $row = $oPub->select($sql);
  if($row )
  { 
	  $db_table = $pre."productcat";
      foreach ($row AS $key=>$val)
      {
          $row[$key]['dateadd']  = ($val['dateadd'])?date("m月d日 h:i", $val['dateadd']):'';
          $sql = "SELECT name FROM ".$db_table." WHERE pcid=$val[pcid]";
		  $row2 = $oPub->getRow($sql);
		  $row[$key]['acname'] = $row2[name];
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
	   $StrtypeAll .= $val["name"];
	   $StrtypeAll .= '</TD><TD align=left>';
	   /* 读取分公司价格 */
	   $db_table = $pre."price_history";
	   $sql = "SELECT shop_price    
	        FROM ".$db_table." 
			where  prid = '".$val["prid"]."'
			AND praid = '".$Aprav['praid']."'
			AND domain_id = '".$Aconf['domain_id']."' 
			ORDER BY  dateadd DESC 
			LIMIT 1";			
       $rowph = $oPub->getRow($sql);
	   $shop_price =($rowph['shop_price'])?$rowph['shop_price']:$val['shop_price'];

	   $StrtypeAll .= '<span id="price_'.$val["prid"].'">';
	   $StrtypeAll .= '<INPUT TYPE="text" value="'.$shop_price.'" size="10" onDblClick=pro_list_edit(\'price\',\''.$val["prid"].'\',this.value,\''.$Aprav['praid'].'\') />';
	   $StrtypeAll .= '</TD><TD align=left>'.$val["acname"].'</TD>';

	   $StrtypeAll .= '<TD align=left>'.$val["dateadd"].'</TD>';
       $StrtypeAll .= '<TD align=center>';
	   $StrtypeAll .= '<a href="../product.php?id='.$val["prid"].'" target="_blank"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="阅读"></a> ';
	   /*查找是否已转换*/
	   $db_table = $pre."pravail_producttxt";
	   $sql = "SELECT main_prid FROM ".$db_table." where  main_prid = '".$val["prid"]."'
			AND praid = '".$_SESSION['apraid']."' AND domain_id = '".$Aconf['domain_id']."' 
			LIMIT 1";
	   $main_prid = $oPub->getOne($sql);
	   if($main_prid > 0 )
	   {
          $StrtypeAll .= ' _ &nbsp;&nbsp;<A HREF="'.$_SERVER["PHP_SELF"].'?prid='.$val["prid"].'&change=no"&page='.$pagenew.' style="color:#FF3300" onclick="return(confirm(\'确定删除?\'))">取消</A>';
	   }
	   else
	   {
	      $StrtypeAll .= ' _ &nbsp;&nbsp;<A HREF="'.$_SERVER["PHP_SELF"].'?prid='.$val["prid"].'&change=yes"&page='.$pagenew.'>转换</A>';
	   }	   
	   
       $StrtypeAll .= '</TD></TR>';    
   }
}



?>
<?php
include_once( "header.php");
if ($strMessage != '') {
	echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="button">
<tr>
  <td align="left">
<?php echo $strPname ;?>
 </td>
</tr>
</table>

<TABLE width="100%" border=0>
 
<?php if($StrtypeAll) { ?>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
  <TR class=bg5>
    <TD width="25%" align=left>标题[价格修改后双击鼠标,自动修改]</TD>
	<TD width="10%" align=left>价格</TD>
	<TD width="10%" align=left>分类</TD>
	<TD width="20%" align=left>时间</TD>
	<TD width="15%" align=center>浏览/转换为自有产品</TD>
  </TR>

 <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=right colspan="5">
	<span style="float: right">
	<?php echo $showpage;?>
	</span>
	</TD>
  </TR>
  </form>
 <?php } ?>
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
	 
   location="<?php echo $_SERVER["PHP_SELF"];?>?pcid=" + obj;

}

  function pro_list_edit(edit,prid,edit_val,praid)
  {
     obj = edit + "_" + prid;
     var strTemp = "ajax_pro_list_edit.php?op=" + edit + "&prid=" + prid + "&edit_val=" + escape(edit_val) + "&praid=" + praid;
	 //alert(strTemp);
	 send_request(strTemp);
  }

</script>
<?php
/* OPTION 递归 */
function get_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre;
   $db_table = $pre.'productcat';
   $Agrad = explode(',',$next_node);
   $Stropt = '';
   if(count($Agrad) > 0 )
	{
	   $tn = 0;
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		   $sql = "SELECT * FROM ".$db_table." where pcid = $v";
           $Anorm = $oPub->getRow($sql);
		   if( $Anorm["name"] != ''){
			   $tn ++;
			   $selected = ($fid == $v)? 'SELECTED':'';
		      $Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$Anorm["name"].'</OPTION>';
              $Stropt .= get_next_node($Anorm["next_node"],$fid,$str .= '　');
		      $str = '　';
		   }
		   
	   }
	}
	return $Stropt;
}
?>
<?php
include_once( "footer.php");
?>
