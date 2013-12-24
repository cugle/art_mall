<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");

if($Aconf['priveMessage'] != '') {
   echo showMessage($Aconf['priveMessage']);
   exit;
}
 
 
if($_POST['act'] == 'update' &&  $_POST["id"] > 0) {  
	
	if($_POST['shenhestats']>0  ){
		$Afields=array('shenhestats'=>$_POST['shenhestats'], 'shenheuserid'=>$_SESSION[$un_auser_id],'shenhename'=>$_SESSION[$un_auser_name],'shenhedescs'=>$_POST['shenhedescs'],'shenhedate'=>date("Y-m-d H:i:s")); 
		$condition = 'id='.$_POST["id"].' and shenhestats != 2 AND domain_id = '.$Aconf['domain_id']; 
		$oPub->update($pre."shenbao",$Afields,$condition);
		$strMessage = '审核成功！';
	} 
	$_GET['action'] = '';
} 

if(  $_GET['action'] == 're'){ 
	$id = $_REQUEST[id] + 0;
	$sql = "SELECT a.*     
	        FROM ".$pre."shenbao  as a 
			where  a.id = '$id'
			AND a.domain_id=".$Aconf['domain_id'];
    $work = $oPub->getRow($sql);  
} 
 
//列表
$where = " domain_id = '".$Aconf['domain_id']."'"; 
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
if($row ) { 
    foreach ($row AS $key=>$val) { 
        $row[$key]['dateadd_show']  = ($val['dateadd'])?date("y/n/j H:i", $val['dateadd']):'';  
		$row[$key]['nowend']  = $val['dateadd'] + 1800; 
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
	   $StrtypeAll .= '<TD align=middle>'.($val["nums"] * $val["promoney"]) .'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["shoukuanmoshi"].'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.date("y-m-d",$val["profukandate"]).'</TD>'; 
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
        if($val["shenhestats"] == 2){
			$StrtypeAll .= '<a href="'.$_SERVICE["PHP_SELF"].'?id='.$val["id"].'&action=re&page='.$pagenew.'" target="main"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查阅"></a>';
		}else{
			$StrtypeAll .= '<a href="'.$_SERVICE["PHP_SELF"].'?id='.$val["id"].'&action=re&page='.$pagenew.'" target="main"><IMG SRC="images/zoo.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="审核"></a>';
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
<?php if($work["id"] >0 ){?> 
<TABLE width="100%" border=0 cellspacing="1" cellpadding="0">
  <form action="" method="post" name="theForm" style="margin: 0">
  <TR class=bg1>
    <TD align=left > 
 
		  <TABLE width=96% cellspacing="4">
		  <TR>
			<TD width="120" align="right"><b>姓名:</b></TD>
			<TD>
			    <span style="color:#0000FF"><?php echo ($work["id"] > 0)?$work["user_name"]:'';?></span>  
				<b style="color:#FF0000">申报时间:</b><span style="color:#0000FF"><?php echo ($work["id"] > 0)?date("Y-m-d",$work["dateadd"]):'';?></span> 

			</TD>
		  </TR>

		  <TR>
			<TD width="120" align="right"><b>客户信息:</b></TD>
			<TD>
			<?php
			echo '<b style="color:#FF0000">开户名:</b><span style="color:#0000FF">'.$work["khname"].'</span> <b style="color:#FF0000">客户地址:</b><span style="color:#0000FF">'.$work["khaddress"].'</span> <b style="color:#FF0000">联系人:</b><span style="color:#0000FF">'.$work["linksnames"].'</span> <b style="color:#FF0000">电话:</b><span style="color:#0000FF">'.$work["tel"].'</span>'; 
			?> 
			</TD>
		  </TR>

		  <TR>
			<TD width="120" align="right"><b>产品详细信息:</b></TD>
			<TD>
 
				<b style="color:#FF0000">产品名称:</b><span style="color:#0000FF"><?php echo ($work["id"] > 0)?$work["proname"]:'';?> </span>
				 <b style="color:#FF0000">规格:</b><span style="color:#0000FF"><?php echo ($work["id"] > 0)?$work["proguige"]:'';?></span> 

				<b style="color:#FF0000">单价:</b><span style="color:#0000FF"><?php echo ($work["id"] > 0)?$work["promoney"]:'';?>元；</span>
				<b style="color:#FF0000">数量:</b><span style="color:#0000FF"><?php echo ($work["id"] > 0)?$work["nums"]:'';?> </span>
				<b style="color:#FF0000">总价:</b><span style="color:#0000FF"><?php echo ($work["id"] > 0)?$work["nums"]*$work["promoney"]:'';?> </span>

				<br/>
				<b style="color:#FF0000">收款方式：</b><span style="color:#0000FF"><?php echo ($work["id"] > 0)?$work["shoukuanmoshi"]:'';?></span>
				<b style="color:#FF0000">付款日期:</b><span style="color:#0000FF"><?php echo ($work["id"] > 0)?date("Y-m-d",$work["profukandate"]):date("Y-m-d");?></span>
				 <br/>
		        <b style="color:#FF0000">备注说明:</b><span style="color:#0000FF"><?php echo ($work["id"] > 0)?$work["descs"]:'';?> </span>

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
			<TD width="120" align="right"><b>申报时间:</b></TD>
			<TD>
		    	   <?php echo ($work["id"] > 0)?date("y-n-j H:i", $work['dateadd']):'';?> 
				   
			</TD>
		  </TR>

		  <TR>
			<TD width="120" align="right"><b>审核状态:</b></TD>
			<TD>
			<?php if($work["shenhestats"]!=2){?>
		    <INPUT TYPE="radio" NAME="shenhestats" value="1" <?php echo $work["shenhestats"]==1?"checked":'';?>>不通过	
			<INPUT TYPE="radio" NAME="shenhestats" value="2" <?php echo $work["shenhestats"]==2?"checked":'';?>>通过
			<INPUT TYPE="radio" NAME="shenhestats" value="0" <?php echo $work["shenhestats"]<1?"checked":'';?>>不处理
			<?php }else{ ?>
				 <?php echo '【已通过,by '.$work["shenhename"].' '.$work["shenhedate"].'】'.$work["shenhedescs"];?>
			<?php } ?>
			</TD>
		  </TR>  		  
		  <TR>
			<TD width="120" align="right"><b>备注说明:</b></TD>
			<TD>
			<?php if($work["shenhestats"]!=2){?>
		    	  <TEXTAREA NAME="shenhedescs" style="height:40px;width:400px;background-color: #FFFFD0"><?php echo ($work["id"] > 0)?$work["shenhedescs"]:'';?></TEXTAREA>
			<?php }else{ ?>
				<?php echo ($work["id"] > 0)?$work["shenhedescs"]:'';?>
			<?php } ?>
			</TD>
		  </TR> 
		  <?php if($work["shenhestats"]!=2){?>
		  <TR>
			<TD  width="120" align="right">&nbsp;</TD>
			<TD>
			<input type="submit" value="<?php echo ($work["id"] > 0)?'确定提交':'';?>" style="background-color: #FFCC66;"/> 
			<input type="hidden" name="id" value="<?php echo ($work["id"] > 0)?$work["id"]:'';?>" /> 
			<input type="hidden" name="act" value="<?php echo ($work["id"] < 1?'':'update');?>" /> 
			<input type="hidden" name="page" value="<?php echo $_REQUEST["page"];?>" />
			</TD>
		  </TR>
		  <?php } ?>
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

