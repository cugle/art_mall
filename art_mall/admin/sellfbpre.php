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
	
	$Afields=array('praid'=>$praid,'pra_name'=>$pra_name,'user_id'=>$_SESSION['auser_id'],'user_name'=>$_SESSION[$un_auser_name],'premoney'=>$_POST["premoney"],'descs'=>$_POST["descs"],'prestats'=>0,'year'=>date("Y"),'month'=>date("n"),'dateadd'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
 
	if($_POST['act'] == 'insert'){
		$oPub->install($pre."feibao ",$Afields);
		$strMessage = '成功添加！';
	}
	elseif($_POST['act'] == 'update' && $_POST["id"] > 0 ){  
		$condition = 'id='.$_POST["id"].' and shennwstats < 1 and shenhestats < 1 AND shenhecwstats<1 and domain_id = '.$Aconf['domain_id']; 
		$oPub->update($pre."feibao",$Afields,$condition);
		$strMessage = '成功修改！';
	}else{
		$strMessage = '没有执行，请重试！';	
	}
} 

if( $_GET['action'] == 'edit' || $_GET['action'] == 'show'){ 
	$id = $_REQUEST[id] + 0;
	$sql = "SELECT a.*     
	        FROM ".$pre."feibao  as a 
			where  a.id = '$id'
			AND a.domain_id=".$Aconf['domain_id'];
    $work = $oPub->getRow($sql); 
 
	$work['nowend']  = $work['dateadd'] + 1800;  
 
}
if( $_GET[action] == 'del') {  
	$condition = 'id='.$_GET['id'].' and shennwstats<2 and shenhestats<1 and  shenhecwstats<1 and user_id='.$_SESSION['auser_id']; 
    $oPub->delete($pre."feibao",$condition); 
}
 
//列表
$where = " domain_id = '".$Aconf['domain_id']."' and user_id=".$_SESSION['auser_id']; 
$sql = "SELECT COUNT(*) as count FROM ".$pre."feibao  AS a WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = $Aconf['set_pagenum'];
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$pre."feibao  WHERE  $where ".
       " ORDER BY dateadd DESC ".
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
if($row )
{ 
    foreach ($row AS $key=>$val)
    { 
        $row[$key]['dateadd_show']  = ($val['dateadd'])?date("y-n-j H:i", $val['dateadd']):'';  
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
	   $StrtypeAll .= '<TD align=middle><span style="color:#FF0000">'.$val["premoney"].'</span></TD>'; 
	   $StrtypeAll .= '<TD align=middle title="'.$val["descs"].'">'.sub_str($val["descs"],20).'</TD>'; 
		$strstates = '';

	   if($val["prestats"] == 1 ){
			$strstates = '未通过';
			$states = '【未通过】'.$val["predescs"];
	   }
	   elseif($val["prestats"] == 2){
			 $strstates = '已通过';
			 $states = '【已通过】'.$val["predescs"];
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.'">'.$strstates.'</a></TD>';


	   if($val["shennwstats"] == 1 ){
			$strstates = '未通过';
			$states = '【未通过 '.$val["shennwdate"].'】'.$val["shennwdescs"];
	   }
	   elseif($val["shennwstats"] == 2){
			 $strstates = '已通过';
			 $states = '【已通过 '.$val["shennwdate"].'】'.$val["shennwdescs"];
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.'">'.$strstates.'</a></TD>';

		$strstates = '';
	   if($val["shenhestats"] == 1 ){
			$strstates = '未通过';
			$states = '【未通过 '.$val["shenhedate"].'】'.$val["shenhedescs"];
	   }
	   elseif($val["shenhestats"] == 2){
			 $strstates = '已通过';
			 $states = '【已通过 '.$val["shenhedate"].'】'.$val["shenhedescs"];
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.'">'.$strstates.'</a></TD>'; 

		$strstates = '';
	   if($val["shenhecwstats"] == 1 ){
			$strstates = '<span style="color:#FF0000">未通过</span>'; 
			$states = '【未通过 '.$val["shenhecwdate"].'】'.$val["shenhecwdescs"];
	   }
	   elseif($val["shenhecwstats"] == 2){
			 $strstates = '已通过';
			 $states = '【已通过 '.$val["shenhecwdate"].'】'.$val["shenhecwdescs"];
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states .'">'.$strstates.'</a></TD>'; 


		$strstates = '';
	   if($val["chunastatsuserid"] >0 ){
			$strstates = '已付款';
			$states = '已付款';
	   }
	   else{
			$strstates = $states =  '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states .'">'.$strstates.'</a></TD>'; 

       $StrtypeAll .= '<TD align=center>';
 

		if( $val["prestats"] < 2 and $val["shennwstats"] < 1 && $val["shenhestats"] < 1 && $val["shenhecwstats"] < 1 && $val["chunastatsuserid"] < 1   ){
			//是否允许当天数据修改
			$StrtypeAll .= '<a href="'.$_SERVICE["PHP_SELF"].'?id='.$val["id"].'&action=edit&page='.$pagenew.'" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ '; 
			$StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?id='.$val["id"].'&action=del&page='.$pagenew.'" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除" onclick="return(confirm(\'确定删除?\'))"></a>';  
		}else{
			$StrtypeAll .= '<a href="'.$_SERVICE["PHP_SELF"].'?id='.$val["id"].'&action=show&page='.$pagenew.'" target="main"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查阅"></a>';		
		}

       $StrtypeAll .= '</TD>';
	   $StrtypeAll .= '</TR>';   
}
$sql = "SELECT sum(premoney) as premoney FROM ".$pre."feibao  WHERE  $where ";
$premoney = $oPub->getOne($sql); 

?>

<?php
   include_once( "header.php");
   if ($strMessage != ''){
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
   }

?>
<?php if($_GET['action'] == 'show') {?> 
	<TABLE width="100%" border=0 cellspacing="1" cellpadding="0"> 
	  <TR class=bg1>
		<TD align=left > 
			  <span style="margin-left:120px;color: #FF6633;font-size: 14px;font-weight: bold">请款预备: </span> 
			  <TABLE width="96%"> 
			  <TR>
				<TD width="120" align="right"><b>申请原因:</b></TD>
				<TD>
					 <span style="color:#0000ff"><?php echo ($work["id"] > 0)?$work["descs"]:'';?> </span>
				</TD>
			  </TR>
			  <TR>
				<TD width="120" align="right"><b>预计金额:</b></TD>
				<TD>
					  <span style="color:#0000ff"><?php echo ($work["id"] > 0)?$work["premoney"]:'';?> 元；</span> 
				</TD>
			  </TR> 
			  <TR>
				<TD width="120" align="right"><b>申报时间:</b></TD>
				<TD>
					  <span style="color:#0000ff"><?php echo ($work["id"] > 0)?date("y-n-j H:i", $val['dateadd']):'';?> </span> 
				</TD>
			  </TR>
			  <TR>
				<TD width="120" align="right"><b>预备款审核:</b></TD>
				<TD>
					  <span style="color:#0000ff">
					  <?php
							$strstates = '';
						   if($work["prestats"] == 1 ){
								$strstates = '<span style="color:#FF0000">【未通过'.$work["predate"].'】</span>'.$work["predescs"];
						   }
						   elseif($work["prestats"] == 2){
								 $strstates = '【已通过 '.$work["predate"].'】'.$work["predescs"];
						   }
						   else{
								$strstates = '暂未审核';
						   }
						   echo $strstates;
					   ?> 
					   </span> 
				</TD>
			  </TR>
			  <TR>
				<TD width="120" align="right"><b>内务审核:</b></TD>
				<TD>
					  <span style="color:#0000ff">
					  <?php
							$strstates = '';
						   if($work["shennwstats"] == 1 ){
								$strstates = '<span style="color:#FF0000">【未通过 '.$work["shennwdate"].'】</span>'.$work["shennwdescs"];
						   }
						   elseif($work["shennwstats"] == 2){
								 $strstates = '【已通过 '.$work["shennwdate"].'】'.$work["shennwdescs"];
						   }
						   else{
								$strstates = '暂未审核';
						   }
						   echo $strstates;
					   ?> 
					   </span> 
				</TD>
			  </TR>
			  <TR>
				<TD width="120" align="right"><b>财务审核:</b></TD>
				<TD>
					  <span style="color:#0000ff">
					  <?php
							$strstates = '';
						   if($work["shenhestats"] == 1 ){
								$strstates = '<span style="color:#FF0000">【未通过'.$work["shenhedate"].'】</span>'.$work["shenhedescs"];
						   }
						   elseif($work["shenhestats"] == 2){
								 $strstates = '【已通过'.$work["shenhedate"].'】'.$work["shenhedescs"];
						   }
						   else{
								$strstates = '暂未审核';
						   }
						   echo $strstates;
					   ?> 
					   </span> 
				</TD>
			  </TR>
			  <TR>
				<TD width="120" align="right"><b>董事审核:</b></TD>
				<TD>
					  <span style="color:#0000ff">
					  <?php
 
							$strstates = '';
						   if($work["shenhecwstats"] == 1 ){
								$strstates = '<span style="color:#FF0000">【未通过'.$work["shenhecwdate"].'】</span>'.$work["shenhecwdescs"];
						   }
						   elseif($work["shenhecwstats"] == 2){
								 $strstates = '【已通过'.$work["shenhecwdate"].'】'.$work["shenhecwdescs"];
						   }
						   else{
								$strstates = '未执行';
						   }
						   echo $strstates;
					   ?> 
					   </span> 
				</TD>
			  </TR>
			  <TR>
				<TD width="120" align="right"><b>出纳审核:</b></TD>
				<TD>
					  <span style="color:#0000ff">
					  <?php
 
							$strstates = '';
						   if($work["chunastatsuserid"] >0 ){
								$strstates = '已付款'; 
						   }
						   else{
								$strstates = '未付款';
						   }
						   echo $strstates;
					   ?> 
					   </span>
				</TD>
			  </TR>
			  </TABLE>
		</TD> 
	  </TR> 
	</table> 
<?php }else{ ?>
	<TABLE width="100%" border=0 cellspacing="1" cellpadding="0">
	  <form action="" method="post" name="theForm" style="margin: 0">
	  <TR class=bg1>
		<TD align=left > 
			  <span style="margin-left:120px;color: #FF6633;font-size: 14px;font-weight: bold">请款预备:</span> 
			  <TABLE width=96%>
	 
			  <TR>
				<TD width="120" align="right"><b>申请原因:</b></TD>
				<TD>
					  <TEXTAREA NAME="descs" style="height:40px;width:600px;background-color: #FFFFD0"><?php echo ($work["id"] > 0)?$work["descs"]:'';?></TEXTAREA>
				</TD>
			  </TR>
			  <TR>
				<TD width="120" align="right"><b>预计金额:</b></TD>
				<TD>
					  <INPUT TYPE="text" NAME="premoney" value="<?php echo ($work["id"] > 0)?$work["premoney"]:'';?>" size="10">元；
					  <span style="color:#FF9900">录入时间：<?php echo date("y年m月d日 H:i");?></span>	
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
<?php }  ?>
<TABLE width="100%" border=0 cellspacing="1" cellpadding="0"> 
  <TR class=bg5> 
	<TD align=middle width="8%">姓名</TD> 
	<TD align=middle width="10%">时间</TD>
	<TD align=middle width="8%">预计金额</TD>
	<TD align=middle width="22%">用途说明</TD>
	<TD align=middle width="12%">请款预备审核</TD>
	<TD align=middle width="10%">内务审核</TD>
	<TD align=middle width="8%">财务审核</TD>
	<TD align=middle width="8%">董事审核</TD>
	<TD align=middle width="8%">出纳审核</TD> 
	<TD align=middle width="6%">操作</TD> 
  </TR>
   <?php echo $StrtypeAll;?>
  <TR > 
	<TD align=middle > </TD> 
	<TD align=middle >合计</TD>
	<TD align=middle ><span style="color:#FF0000"><?php echo $premoney;?></span></TD>
	<TD align=middle > </TD>
	<TD align=middle > </TD>
	<TD align=middle > </TD>
	<TD align=middle > </TD>
	<TD align=middle > </TD>
	<TD align=middle > </TD> 
	<TD align=middle > </TD> 
  </TR>
  <TR class=bg5>
    <TD  align=middle colspan="10">
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR>
 </table>

<?php
include_once( "footer.php");
?>

