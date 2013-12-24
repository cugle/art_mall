<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");

if($Aconf['priveMessage'] != '') {
   echo showMessage($Aconf['priveMessage']);
   exit;
}
 
$praid  = $oPub->getOne("SELECT praid FROM ".$pre."admin_user where user_id='".$_SESSION['auser_id']."' limit 1"); 
 
 
if($_POST['act'] == 'insert' || $_POST['act'] == 'update' ) {
 

 
	$sql = "SELECT pra_name  FROM ".$pre."pravail WHERE  praid=".$praid; 
	$pra_name = $oPub->getOne($sql); 
 
	$profukandate = local_strtotime($_POST["profukandate"]); 
	$Afields=array('praid'=>$praid,'pra_name'=>$pra_name,'user_id'=>$_SESSION['auser_id'],'user_name'=>$_SESSION[$un_auser_name],'proname'=>$_POST["proname"],'proguige'=>$_POST["proguige"],'promoney'=>$_POST["promoney"],'ifhanshui'=>$ifhanshui,'ifhanyunfei'=>$ifhanyunfei,'profukandate'=>$profukandate,'descs'=>$_POST["descs"],'khname'=>$_POST["khname"],'khaddress'=>$_POST["khaddress"],'linksnames'=>$_POST["linksnames"],'tel'=>$_POST["tel"],'nums'=>$_POST["nums"],'shoukuanmoshi'=>$_POST["shoukuanmoshi"],'year'=>date("Y"),'month'=>date("n"),'dateadd'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
 
	if($_POST['act'] == 'insert'){
		$oPub->install($pre."shenbao",$Afields);
		$strMessage = '成功添加！';
	}
	elseif($_POST['act'] == 'update' && $_POST["id"] > 0 ){ 
		$condition = 'id='.$_POST["id"].' and shenhestats < 1 AND domain_id = '.$Aconf['domain_id']; 
		$oPub->update($pre."shenbao",$Afields,$condition);
		$strMessage = '成功修改！';
	}else{
		$strMessage = '没有执行，请重试！';	
	}
} 

if( $_GET['action'] == 'edit' || $_GET['action'] == 'show'){ 
	$id = $_REQUEST[id] + 0;
	$sql = "SELECT a.*     
	        FROM ".$pre."shenbao  as a 
			where  a.id = '$id'
			AND a.domain_id=".$Aconf['domain_id'];
    $work = $oPub->getRow($sql); 
 
	$work['nowend']  = $work['dateadd'] + 1800;  
 
}
if( $_GET[action] == 'del') {  
	//if(in_array($_SESSION['auser_id'],$Adminconf['allowManage'])){
		//$condition = 'id='.$_GET['id'] ;
	//}else{
		//$condition = 'id='.$_GET['id'].' and shenhestats<1 and user_id='.$_SESSION['auser_id'];
	//}
	$condition = 'id='.$_GET['id'].' and shenhestats<1 and user_id='.$_SESSION['auser_id']; 
    $oPub->delete($pre."shenbao",$condition); 
	$change_desc = real_ip().' |  '.date("m月d日 h:i");
	$change_desc .= ' | '.$_SESSION[$un_auser_name].' 删除shenbao :ID.'.$_GET['id'];
	$Afields=array('user_id'=>$_SESSION['auser_id'],'type'=>'shenbao','change_desc'=>$change_desc,'domain_id'=>$Aconf['domain_id']);
    $oPub->install($pre.'account_log',$Afields);

}
 
//列表
$where = " domain_id = '".$Aconf['domain_id']."' and user_id=".$_SESSION['auser_id']; 
$sql = "SELECT COUNT(*) as count FROM ".$pre."shenbao  AS a WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = $Aconf['set_pagenum'];
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$pre."shenbao  WHERE  $where ".
       " ORDER BY dateadd DESC ".
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
if($row )
{ 
    foreach ($row AS $key=>$val)
    { 
        $row[$key]['dateadd_show']  = ($val['dateadd'])?date("y-n-j H:i", $val['dateadd']):'';  
		$row[$key]['nowend']  = $val['dateadd'] + 1800;  
		$row[$key]['profukandate']  = date("y-n-j", $val['profukandate']);  
    } 
}
$StrtypeAll = ''; 

if($row)
foreach ($row AS $key=>$val)
{
		$n ++ ;
		$tmpstr = ($n % 2 == 0)?"even_m":"odd_m";
       $StrtypeAll .= '<TR class="'.$tmpstr.'" onMouseOver="this.style.backgroundColor=\'#FFFFFF\';" onMouseOut="this.style.backgroundColor=\'#e6e6e6\'; ">';
	   $StrtypeAll .= '<TD align=middle>'.$val["user_name"].'</TD>'; 
	   $StrtypeAll .= '<TD align=middle>'.$val["dateadd_show"].'</TD>'; 
	   $kf = $val["khname"].'|联系人：'.$val["linksnames"]; 
	   $kf2 = '客户名:'.$val["khname"].'|客户地址:'.$val["khaddress"].'|联系人：'.$val["linksnames"].'|电话：'.$val["tel"];
	   $StrtypeAll .= '<TD align=middle><a title="'.$kf2.'">'.$kf.'</a></TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["proname"].'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["proguige"].'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["promoney"].'<br/><span style="color:#FF0000">'.$val["ifhanshui"].'</span> <span style="color:#0000FF">'.$val["ifhanyunfei"].'</span></TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["nums"].'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.($val["nums"] *$val["promoney"]) .'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["shoukuanmoshi"].'</TD>';
	   $StrtypeAll .= '<TD align=middle>'. $val["profukandate"] .'</TD>'; 
	   $StrtypeAll .= '<TD align=middle title="'.$val["descs"].'">'.sub_str($val["descs"],5).'</TD>'; 
		$strstates = '';
	   if($val["shenhestats"] == 1 ){
			$strstates = '<span style="color:#FF0000">未通过</span>';
			$states = '【未通过,by '.$val["shenhename"].' '.$val["shenhedate"].'】'.$val["shenhedescs"];
	   }
	   elseif($val["shenhestats"] == 2){
			 $strstates = '已通过';
			 $states = '【已通过,by '.$val["shenhename"].' '.$val["shenhedate"].'】'.$val["shenhedescs"];
	   }
	   else{
			$strstates =  $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.'">'.$strstates.'</a></TD>'; 

  

       $StrtypeAll .= '<TD align=center>';
		if( $val["shenhestats"] < 1){
			//是否允许当天数据修改
			$StrtypeAll .= '<a href="'.$_SERVICE["PHP_SELF"].'?id='.$val["id"].'&action=edit&page='.$pagenew.'" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ '; 
			$StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?id='.$val["id"].'&action=del&page='.$pagenew.'" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除" onclick="return(confirm(\'确定删除?\'))"></a>';  
		}else{
			$StrtypeAll .= '<a href="'.$_SERVICE["PHP_SELF"].'?id='.$val["id"].'&action=show&page='.$pagenew.'" target="main"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查阅"></a>';		
		}
       $StrtypeAll .= '</TD>';
	   $StrtypeAll .= '</TR>';   
}
 

?>

<?php
   include_once( "header.php");
   if ($strMessage != ''){
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
   }

?>
<?php if($_GET['action'] == 'show') {?> 
<TABLE width="900" border=0 cellspacing="1" cellpadding="0"> 
  <TR class=bg1>
    <TD align=left >  

		  <TABLE width=96%> 
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">客户名称:</b></TD>
			<TD> 
				<?php echo ($work["id"] > 0)?$work["khname"]:'';?> 
				<b style="color:#FF0000">客户地址:</b>
				 <?php echo ($work["id"] > 0)?$work["khaddress"]:'';?> 
				<b style="color:#FF0000">联系人:</b>
				<?php echo ($work["id"] > 0)?$work["linksnames"]:'';?>
				<b style="color:#FF0000">联系电话:</b>
				<?php echo ($work["id"] > 0)?$work["tel"]:'';?>
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">产品名称:</b></TD>
			<TD> 
				<?php echo ($work["id"] > 0)?$work["proname"]:'';?> 
					<b style="color:#FF0000">规格:</b>
				<?php echo ($work["id"] > 0)?$work["proguige"]:'';?>
				<b style="color:#FF0000">数量:</b>
				 <?php echo ($work["id"] > 0)?$work["nums"]:'';?> 
				<b style="color:#FF0000">单价:</b>
				 <?php echo ($work["id"] > 0)?$work["promoney"]:'0.00';?> 元；
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>税 运费:</b></TD>
			<TD>
					<span style="color:#00FF00"> <? echo ($work["ifhanshui"] == '含税')?'含税':'未税';?>  
 					 <? echo ($work["ifhanyunfei"] == '含运费')?'含运费':'不含运费';?> </span>

			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">收款模式:</b></TD>
			<TD>
				 <?php echo ($work["id"] > 0)?$work["shoukuanmoshi"]:'';?> 
				<b style="color:#FF0000">收款时间</b>  
				 <?php echo ($work["id"] > 0)?date("Y-m-d",$work["profukandate"]):date("Y-m-d");?> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">备注说明:</b></TD>
			<TD>
		    	   <?php echo ($work["id"] > 0)?$work["descs"]:'';?> 
				   
			</TD>
		  </TR> 
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">申报时间:</b></TD>
			<TD>
		    	   <?php echo ($work["id"] > 0)?date("y-n-j H:i", $work['dateadd']):'';?> 
				   
			</TD>
		  </TR> 
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">审核状态:</b></TD>
			<TD>
 
				  <?php echo '【已通过,by '.$work["shenhename"].' '.$work["shenhedate"].'】'.$work["shenhedescs"];?>
				   
			</TD>
		  </TR>
		  </TABLE>
    </TD> 
  </TR> 
</table> 

<?php }else{ ?>
<TABLE width="900" border=0 cellspacing="1" cellpadding="0">
  <form action="" method="post" name="theForm" style="margin: 0">
  <TR class=bg1>
    <TD align=left > 
          <span style="margin-left:120px;color: #FF6633;font-size: 14px;font-weight: bold">（数据审核后，将不能修改！）</span> 
 

		  <TABLE width=96%> 
		  <TR>
			<TD width="120" align="right"><b>客户名称:</b></TD>
			<TD> 
				<input type="text" size="20" name="khname" value="<?php echo ($work["id"] > 0)?$work["khname"]:'';?>" />	 
				<b>客户地址:</b>
				<input type="text" name="khaddress" value="<?php echo ($work["id"] > 0)?$work["khaddress"]:'';?>" size="30"/>	
				<b>联系人:</b>
				<input type="text" name="linksnames" value="<?php echo ($work["id"] > 0)?$work["linksnames"]:'';?>" size="8"/>
				<b>联系电话:</b>
				<INPUT TYPE="text" NAME="tel" value="<?php echo ($work["id"] > 0)?$work["tel"]:'';?>" size="8"> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>产品名称:</b></TD>
			<TD> 
				<input type="text" size="10" name="proname" value="<?php echo ($work["id"] > 0)?$work["proname"]:'';?>"/>	 
				<b>规格:</b>
				<input type="text" name="proguige" value="<?php echo ($work["id"] > 0)?$work["proguige"]:'';?>" size="10"/>	
				<b>数量:</b>
				<input type="text" name="nums" value="<?php echo ($work["id"] > 0)?$work["nums"]:'';?>" size="3"/>
				<b>单价:</b>
				<INPUT TYPE="text" NAME="promoney" value="<?php echo ($work["id"] > 0)?$work["promoney"]:'0.00';?>" size="6">元；
				<b>是否含税:</b>
				<SELECT NAME="ifhanshui">
					<OPTION VALUE="含税" <? echo ($work["ifhanshui"] == '含税')?'SELECTED':'';?>>含税</OPTION>
					<OPTION VALUE="未税" <? echo ($work["ifhanshui"]  <> '含税')?'SELECTED':'';?>>未税</OPTION>
				</SELECT>
				<b>是否运费:</b>
				<SELECT NAME="ifhanyunfei">
					<OPTION VALUE="含运费" <? echo ($work["ifhanyunfei"] == '含运费')?'SELECTED':'';?>>含运费</OPTION>
					<OPTION VALUE="未含运费" <? echo ($work["ifhanyunfei"]  <> '含运费')?'SELECTED':'';?>>未含运费</OPTION>
				</SELECT> 				
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>收款模式</b></TD>
			<TD>
				<INPUT TYPE="text" NAME="shoukuanmoshi" value="<?php echo ($work["id"] > 0)?$work["shoukuanmoshi"]:'';?>" size="20">
				<b>收款时间</b>  
				<INPUT TYPE="text" NAME="profukandate" value="<?php echo ($work["id"] > 0)?date("Y-m-d",$work["profukandate"]):date("Y-m-d");?>" size="10"> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>备注说明:</b></TD>
			<TD>
		    	  <TEXTAREA NAME="descs" style="height:40px;width:500px;background-color: #FFFFD0"><?php echo ($work["id"] > 0)?$work["descs"]:'';?></TEXTAREA>
				  <br/>
				  <span style="color:#FF9900">申报时间：<?php echo date("y年m月d日 H:i");?></span>	
			</TD>
		  </TR>

  
		  <TR>
			<TD  width="120" align="right">&nbsp;</TD>
			<TD>
			<input type="submit" value="<?php echo ($work["id"] > 0)?'修改':'确定提交';?>" style="background-color: #FFCC66;"/> 
			<input type="hidden" name="id" value="<?php echo ($work["id"] > 0)?$work["id"]:'';?>" /> 
			<input type="hidden" name="act" value="<?php echo ($work["id"] < 1?'insert':'update');?>" /> 
			<input type="hidden" name="page" value="<?php echo $_REQUEST["page"];?>" />
			</TD>
		  </TR>   
		  </TABLE>
    </TD> 
  </TR>
 </form> 
</table> 
<?php } ?>
<TABLE width="100%" border=0 cellspacing="1" cellpadding="0"> 
  <TR class=bg5>  

	<TD align=middle width="5%">姓名</TD> 
	<TD align=middle width="10%">申报时间</TD>
	<TD align=middle width="13%">客户信息</TD>
	<TD align=middle width="10%">产品名</TD>
	<TD align=middle width="5%">规格</TD>
	<TD align=middle width="7%">单价</TD> 
	<TD align=middle width="5%">数量</TD>
	<TD align=middle width="5%">合计</TD>
	<TD align=middle width="10%">收款方式</TD> 
	<TD align=middle width="10%">收款日期</TD>
	<TD align=middle width="10%">备注</TD>
	<TD align=middle width="5%">审核状态</TD>
	<TD align=middle width="5%">操作</TD> 

  </TR>
   <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=middle colspan="13">
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR>
 </table>

<?php
include_once( "footer.php");
?>

