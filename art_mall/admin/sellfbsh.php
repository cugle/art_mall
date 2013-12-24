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
		$Afields=array('shenhestats'=>$_POST['shenhestats'], 'shenheuserid'=>$_SESSION['auser_id'],'shenhename'=>$_SESSION[$un_auser_name],'shenhedescs'=>$_POST['shenhedescs'],'shenhedate'=>date("Y-m-d H:i:s")); 
		$condition = 'id='.$_POST["id"].' and shennwstats>1 and chunastatsuserid<1 and shenhecwstats<1 AND domain_id = '.$Aconf['domain_id']; 
		$oPub->update($pre."feibao",$Afields,$condition);
		$strMessage = '审核成功！';
	} 
	$_GET['action'] = '';
} 

if(  $_GET['action'] == 're'){ 
	$id = $_REQUEST[id] + 0;
	$sql = "SELECT a.*     
	        FROM ".$pre."feibao  as a 
			where  a.id = '$id'
			AND a.domain_id=".$Aconf['domain_id'];
    $work = $oPub->getRow($sql);  
}
 
 
//列表
$where = " domain_id = '".$Aconf['domain_id']."'  and shennwstats=2"; 
$sql = "SELECT COUNT(*) as count FROM ".$pre."feibao  AS a WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = 40;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$pre."feibao  WHERE  $where ".
       " ORDER BY shenhestats asc, dateadd DESC ".
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
if($row ) { 
    foreach ($row AS $key=>$val) { 
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
	    $StrtypeAll .= '<TD align=middle><span style="color:#0080C0">'.$val["premoney"].'</span></TD>';
	   $StrtypeAll .= '<TD align=middle><span style="color:#FF0000">'.$val["money"].'</span></TD>';  
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
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.' '.$val["predate"].'">'.$strstates.'</a></TD>'; 

	   if($val["shennwstats"] == 1 ){
			$strstates = '未通过';
			$states = '【未通过'.$val["shennwdate"].'】'.$val["shennwdescs"];
	   }
	   elseif($val["shennwstats"] == 2){
			 $strstates = '已通过';
			 $states = '【已通过'.$val["shennwdate"].'】'.$val["shennwdescs"];
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.'">'.$strstates.'</a></TD>';

		$strstates = '';
	   if($val["shenhestats"] == 1 ){
			$strstates = '未通过';
			$states = '【未通过'.$val["shenhedate"].'】'.$val["shenhedescs"];
	   }
	   elseif($val["shenhestats"] == 2){
			 $strstates = '已通过';
			 $states = '【已通过'.$val["shenhedate"].'】'.$val["shenhedescs"];
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.'">'.$strstates.'</a></TD>'; 

		$strstates = '';
	   if($val["shenhecwstats"] == 1 ){
			$strstates = '<span style="color:#FF0000">未通过</span>'; 
			$states = '【未通过'.$val["shenhecwdate"].'】'.$val["shenhecwdescs"];
	   }
	   elseif($val["shenhecwstats"] == 2){
			 $strstates = '已通过';
			 $states = '【已通过'.$val["shenhecwdate"].'】'.$val["shenhecwdescs"];
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states .'">'.$strstates.'</a></TD>'; 

		$strstates = '';
	   if($val["chunastatsuserid"] >0 ){
			$strstates = '已付款';
			$states = '已付款'.$val["chunadescs"];
	   }
	   else{
			$strstates = $states =  '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states .'">'.$strstates.'</a></TD>'; 

 
		$StrtypeAll .= '<TD align=center>'; 
        if($val["shennwstats"]==2 && $val["shenhecwstats"]<1 ){
			$StrtypeAll .= '<a href="'.$_SERVICE["PHP_SELF"].'?id='.$val["id"].'&action=re&page='.$pagenew.'" target="main"><IMG SRC="images/zoo.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="审核"></a>';

		}else{
			$StrtypeAll .= '<a href="'.$_SERVICE["PHP_SELF"].'?id='.$val["id"].'&action=re&page='.$pagenew.'" target="main"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查阅"></a>';		
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
 
		  <TABLE width=96%>
		  <TR>
			<TD width="120" align="right"><b>姓名:</b></TD>
			<TD>
				<span style="color:#0000ff"><?php echo ($work["id"] > 0)?$work["user_name"]:'';?></span>   
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>申请金额:</b></TD>
			<TD> 
			 <span style="color:#0000ff"><?php echo ($work["id"] > 0)?$work["premoney"].'元':'';?></span> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>实报金额:</b></TD>
			<TD> 
			 <span style="color:#ff0000"><?php echo ($work["id"] > 0)?$work["money"].'元':'';?></span> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>用途说明:</b></TD>
			<TD>
				 <span style="color:#0000ff"><?php echo ($work["id"] > 0)?$work["descs"]:'';?> </span>
			</TD>
		  </TR> 
		  <TR>
			<TD width="120" align="right"><b>申报时间:</b></TD>
			<TD>
				  <span style="color:#0000ff"><?php echo ($work["id"] > 0)?date("y-n-j H:i", $val['dateadd']):'';?> </span> 
			</TD>
		  </TR>	
		  <TR>
			<TD width="120" align="right"><b>请款预备审核:</b></TD>
			<TD>
				  <span style="color:#0000ff">
				  <?php
						$strstates = ''; //predate preuserid prename predescs 
					   if($work["prestats"] == 1 ){
							$strstates = '<span style="color:#FF0000">【未通过'.$work["predate"].'】</span>'.$work["predescs"];
					   }
					   elseif($work["prestats"] == 2){
							 $strstates = '【已通过'.$work["predate"].'】'.$work["predescs"];
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
							$strstates = '<span style="color:#FF0000">【未通过'.$work["shennwdate"].'】</span>'.$work["shennwdescs"];
					   }
					   elseif($work["shennwstats"] == 2){
							 $strstates = '【已通过'.$work["shennwdate"].'】'.$work["shennwdescs"];
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
			<?php if($work["shenhecwstats"] > 0){ ?>
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
			<?php } else{
					   $strstates = '';
						if($work["shennwstats"] == 2){	
			?>
					<INPUT TYPE="radio" NAME="shenhestats" value="1" <?php echo $work["shenhestats"]==1?"checked":'';?>>不通过	
					<INPUT TYPE="radio" NAME="shenhestats" value="2" <?php echo $work["shenhestats"]==2?"checked":'';?>>通过
					<INPUT TYPE="radio" NAME="shenhestats" value="0" <?php echo $work["shenhestats"]<1?"checked":'';?>>不处理
			<?php 
						}else{
							$strstates = '未执行 ';
						}
						echo $strstates;
			} ?>
			</TD>
		  </TR>  
			<?php if($work["shenhecwstats"] < 1){ ?>		  
		  <TR>
			<TD width="120" align="right"><b>备注说明:</b></TD>
			<TD>

		    	  <TEXTAREA NAME="shenhedescs" style="height:40px;width:400px;background-color: #FFFFD0"><?php echo ($work["id"] > 0)?$work["shenhedescs"]:'';?></TEXTAREA>
			</TD>
		  </TR> 
		  <TR>
			<TD  width="120" align="right">&nbsp;</TD>
			<TD>
			<input type="submit" value="<?php echo ($work["id"] > 0)?'确定提交':'';?>" style="background-color: #FFCC66;"/> 
			<input type="hidden" name="id" value="<?php echo ($work["id"] > 0)?$work["id"]:'';?>" /> 
			<input type="hidden" name="act" value="<?php echo ($work["id"] < 1?'':'update');?>" /> 
			<input type="hidden" name="page" value="<?php echo $_REQUEST["page"];?>" />
			</TD>
		  </TR>  
			<?php }else{ ?>

			  <TR>
				<TD width="120" align="right"><b>请款预备审核:</b></TD>
				<TD>
					  <span style="color:#0000ff">
					  <?php
							$strstates = ''; //predate preuserid prename predescs 
						   if($work["prestats"] == 1 ){
								$strstates = '<span style="color:#FF0000">【未通过'.$work["predate"].'】</span>'.$work["predescs"];
						   }
						   elseif($work["prestats"] == 2){
								 $strstates = '【已通过'.$work["predate"].'】'.$work["predescs"];
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

			<?php } ?>
			
		  </TABLE>
    </TD> 
  </TR>
 </form> 
</table> 
<?php } ?>
<TABLE width="100%" border=0 cellspacing="1" cellpadding="0"> 
  <TR class=bg5> 
	<TD align=middle width="8%">姓名</TD> 
	<TD align=middle width="10%">时间</TD>
	<TD align=middle width="8%">申请金额</TD>
	<TD align=middle width="8%">实报金额</TD>
	<TD align=middle width="28%">用途说明</TD>
	<TD align=middle width="10%">请款预备审核</TD> 
	<TD align=middle width="6%">内务审核</TD>
	<TD align=middle width="6%">财务审核</TD>
	<TD align=middle width="6%">董事审核</TD>
	<TD align=middle width="6%">出纳审核</TD> 
	<TD align=middle width="6%">操作</TD> 
  </TR>
   <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=middle colspan="11">
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR>
 </table>

<?php
include_once( "footer.php");
?>

