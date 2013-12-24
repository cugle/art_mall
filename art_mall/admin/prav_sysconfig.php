<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

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

$db_table = $pre."pravail";
if( $action == 'add' || $action == 'edit' )
{
		/*处理shop_logo图片*/
		if($_FILES['shop_logo']['size'] > 0 )
		{
			/* 判断图像类型 */
			if (!$image->check_img_type($_FILES['shop_logo']['type']))
			{
				$strMessage =  '图片类型错误';
				$shop_logo = $_POST['old_shop_logo'];
			} else {
			   /* 删除原有的 shop_logo */
			   if(!empty($_POST['old_shop_logo']))
			   {
				   @unlink('../' . $_POST['old_shop_logo']);
			   }
			   $shop_logo = $image->make_thumb($_FILES['shop_logo']['tmp_name'],$Aconf["logo_w"],$Aconf["logo_h"]);
			}
		} else
		{
			$shop_logo = $_POST['old_shop_logo'];
		}

		if($_POST['pra_url'])
		{
			$pra_url = str_replace('http://','', $_POST['pra_url']);
			$pra_url = str_replace('https://','', $pra_url);
		}

		/* 基本配置信息插入前的整理 */
		$strSets = '';
		if($_POST[sets])
		foreach ($_POST[sets] AS $k => $v)
		{
			$strSets .= $k.'[|]'.$v.'{|}';
		}
		$sets = $strSets; 
		//地区分类 start
		if($_POST["ccid_5"]){
			$_POST["ccid"] = $_POST["ccid_5"];
		}elseif($_POST["ccid_4"]){
			$_POST["ccid"] = $_POST["ccid_4"];
		}elseif($_POST["ccid_3"]){
			$_POST["ccid"] = $_POST["ccid_3"];
		}elseif($_POST["ccid_2"]){
			$_POST["ccid"] = $_POST["ccid_2"];
		}elseif($_POST["ccid_1"]){
			$_POST["ccid"] = $_POST["ccid_1"];
		}
		$ccid = $_POST["ccid"];

		if( $action == 'add' && $_POST['praid'] == 0){
			$sql = "INSERT INTO " . $db_table . " (pra_url,sets,pra_name,shop_logo,ccid,domain_id)" .
						"VALUES ('$pra_url','$sets','$_POST[pra_name]','$shop_logo','$ccid','".$Aconf['domain_id']."')"; 
			$oPub->query($sql);
		}

		if( $action == 'edit' && $_POST['praid'] > 0){
			$_POST['praid'] = $_POST['praid'] + 0; 
			$sql = "UPDATE " . $db_table . " SET 
					pra_url='$pra_url',sets='$sets',pra_name='$_POST[pra_name]',shop_logo='$shop_logo',ccid='$ccid' 
					WHERE  praid='".$_SESSION['apraid']."'";
			$oPub->query($sql);

		}

}
/* 网站配置信息 */
$db_table = $pre."pravail";
$sql = "SELECT * FROM ".$db_table." WHERE praid = ".$_SESSION['apraid']." ORDER BY praid ASC LIMIT 1";
$Anorm = $oPub->getRow($sql);
if($Anorm) {
	$Asets = explode("{|}",$Anorm['sets']);
	if(count($Asets))
    foreach ($Asets AS $v) {
	   $At = array();
	   $At = explode("[|]",$v);
	   if($At[0]) {
	       $Anorm[$At[0]] = $At[1];
		}
	} 
} else {
   echo "系统错误！";
   exit;
}

/* 城市列表 */
/* 城市列表 */
if($Anorm["ccid"] > 0){
	//找到所有的上级分类start
	$sql = "SELECT fid FROM ".$pre."citycat where ccid = ".$Anorm["ccid"]." limit 1";
	$fid= $oPub->getOne($sql); 
	if($fid){
		$preCcid = pre_node_orders($fid,$pre."citycat","ccid");
		$preCcid = $preCcid.','.$Anorm["ccid"];
	}else{
		$preCcid = $Anorm["ccid"];
	} 
	$Accid = explode(",",$preCcid);
	$ccidNum = count($Accid);
	//分类选择
	while( @list( $k, $v ) = @each( $Accid) ) { 
		if($k < 1){
 			$sql = "SELECT * FROM ".$pre."citycat where fid = 0 AND domain_id=".$Aconf['domain_id'];
		}else{
			$sql = "SELECT fid FROM ".$pre."citycat where ccid = ".$v." AND domain_id=".$Aconf['domain_id']." limit 1";
			$fid = $oPub->getOne($sql);
			if(!$fid){
				break;
			}else{
				$sql = "SELECT * FROM ".$pre."citycat where fid = ".$fid." AND domain_id=".$Aconf['domain_id'];
			}
		}
		$AnormAll = $oPub->select($sql);
		$j = $k + 1;
		$keyc = "citycatOpt".$k;
		$Acitycat[$keyc] = '<SELECT NAME="ccid" onchange="selectsAjax(this.value,\'citycat\',\'show\',\'divccid\','.$j.')">'; 
		$Acitycat[$keyc] .= '<OPTION VALUE="0" >选择地域分类</OPTION>';
		$n = 0;
		while( @list( $key, $value ) = @each( $AnormAll) ) {
		$n ++;
			$selected = ($value['ccid'] == $v)? 'SELECTED':'';
			$Acitycat[$keyc] .= '<OPTION VALUE="'.$value["ccid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>'; 
		}
		$Acitycat[$keyc] .= '</SELECT>'; 
	}
}else{
	$sql = "SELECT * FROM ".$pre."citycat where fid = 0 AND domain_id=".$Aconf['domain_id'];
	$AnormAll = $oPub->select($sql);
	$Acitycat[citycatOpt0] = '<SELECT NAME="ccid" onchange="selectsAjax(this.value,\'citycat\',\'show\',\'divccid\',1)">';
	$Acitycat[citycatOpt0] .= '<OPTION VALUE="0" >选择地域分类</OPTION>';
	$n = 0;
	while( @list( $key, $value ) = @each( $AnormAll) ) {
		$n ++;
		$Acitycat[citycatOpt0] .= '<OPTION VALUE="'.$value["ccid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>'; 
	}
	$Acitycat[citycatOpt0] .= '</SELECT>';
}
 

?>
<?php
include_once( "header.php"); 
if ($strMessage != '') {
	 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
 
?>

<DIV class=content>
 
<TABLE width="100%" border=0>
  <TR> 
    <TD align="left" >
		<form name="form1" method="post" action="" enctype="multipart/form-data" style="margin: 0px;padding: 0px">
        <span style="font-weight:bold">商铺名:</span>
     	<input name="pra_name" type="text" size="20" value="<?php echo $Anorm['pra_name'];?>" />
		<span style="font-weight:bold">网址:</span>
		<input name="pra_url" type="text" size="26" value="<?php echo $Anorm['pra_url'];?>" />
		<br/>
		<span style="font-weight: bold">商铺图片:</span>
		<INPUT type="file" name="shop_logo" size="20" /> 
		<span id="shop_prav_show">
         <?php 
		 if($Anorm["shop_logo"])
		 {
			 $tmp = '<A HREF="../'.$Anorm["shop_logo"].'" target="_blank">';
			 $tmp .= '<IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="显示缩图"></A> ';
			 $tmp .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) selectsAjax(\''.$Anorm["shop_logo"].'\',\'pravail\',\'del\',\'shop_prav_show\')">';
			 $tmp .= '<IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除缩图"></A> ';
			 echo $tmp;
		 }		 
		 ?>
		 </span>			
		<span style="color:#c8c">(注：用于商铺推荐，尺寸为<?php echo $Aconf["logo_w"];?>px*<?php echo $Aconf["logo_h"];?>px支持.jpg .gif .png格式)</span>
		<INPUT type="hidden" name="old_shop_logo"  value="<?php echo $Anorm['shop_logo'];?>" />

        <br/><br/>

         <span style="font-weight:bold">城市选择：</span> 
        <?php echo $Acitycat[citycatOpt0];?>
		<span id="divccid_1"><?php echo $Acitycat[citycatOpt1];?></span><span id="divccid_2"><?php echo $Acitycat[citycatOpt2];?></span><span id="divccid_3"><?php echo $Acitycat[citycatOpt3];?></span><span id="divccid_4"><?php echo $Acitycat[citycatOpt4];?></span><span id="divccid_5"><?php echo $Acitycat[citycatOpt5];?></span> 
		<A HREF="http://www.<?php echo $Aconf['mail_url'].'/'.$SUBPATH;?>support.php" target="_blank">建议增加地区城市>></A>  
	
		<br/>
       <span style="font-weight:bold">邮编:</span>
		<input name="sets[zip]" type="text" size="6" value="<?php echo $Anorm['zip'];?>" />
		<span style="font-weight:bold">详细地址:</span>
        <input name="sets[address]" type="text" size="50" value="<?php echo $Anorm['address'];?>" />
		<br/><br/>		

        <span style="font-weight:bold">单位名:</span>
		<input name="sets[shop_name]" type="text" size="35" value="<?php echo $Anorm['shop_name'];?>" />
		<?php echo $Stridctopt;?>   
		<span style="font-weight:bold">电话:</span>
		<input name="sets[phone]" type="text" size="16" value="<?php echo $Anorm['phone'];?>" />
		<span style="font-weight:bold">传真:</span>
		<input name="sets[fax]" type="text" size="16" value="<?php echo $Anorm['fax'];?>" />
		<br/>
		<span style="font-weight:bold">联系人:</span>
		<input name="sets[contact]" type="text" size="8" value="<?php echo $Anorm['contact'];?>" /> 

		<span style="font-weight:bold">手机:</span>
		<input name="sets[tel]" type="text" size="14" value="<?php echo $Anorm['tel'];?>" />  
		<span style="font-weight:bold">QQ:</span> 
		<input name="sets[qq]" type="text" size="14" value="<?php echo $Anorm['qq'];?>" /> 
		<span style="font-weight:bold">Email:</span>
       <input name="sets[email]" type="text" size="20" value="<?php echo $Anorm['email'];?>" />
        <br/><br/>
		<input type="hidden" name="action" value="<?php echo ($Anorm['praid'])?'edit':'add';?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['praid'])?'编辑修改':'增加';?>" style="background-color: #FFCC66;margin-left:50px"/>
		<input type="hidden" name="praid" value="<?php echo ($Anorm['praid'])?$Anorm['praid']:'0';?>" />  
		</form>
    </TD> 
  </TR>	 
</TABLE>

</DIV>
<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT> 
<?php
/* 所属行业 OPTION 递归 */
function get_next_node($next_node,$fid,$str = '') {
      global $oPub,$pre;
      $db_table = $pre.'inducat';
      $Agrad = explode(',',$next_node);
      $Stropt = '';
      $str .= '　　';
      if(count($Agrad) > 0 ) {
	       $tn = 0;
	       while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		   $sql = "SELECT * FROM ".$db_table." where inducat_id = $v";
           $Anorm = $oPub->getRow($sql);
		   if( $Anorm["inducat_name"] != ''){
			   $tn ++;
			   $selected = ($fid == $v)? 'SELECTED':'';
		       $Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$Anorm["inducat_name"].'</OPTION>';
               $Stropt .= get_next_node($Anorm["next_node"],$fid,$str);
		   } 
	   }
	}
	return $Stropt;
}
/* tbale 递归 */
function tab_next_node($next_node,$fid,$str = '')
{
   global $oPub,$pre;
   $db_table = $pre.'inducat';
   $Agrad = explode(',',$next_node);
   $Strtab = '';
   $str .= '　　';
   if(count($Agrad) > 0 )
	{
	   $tn = 0;
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		    $sql = "SELECT * FROM ".$db_table." where inducat_id = $v";
            $Anorm = $oPub->getRow($sql);
			if( $Anorm["inducat_name"] != ''){
			  $tn ++ ;
	          $tmpstr = ($n % 2 == 0)?"even":"odd";
              $Strtab  .= '<TR class='.$tmpstr.'>';

              $Strtab  .= '<TD align=left>'.$str.$tn.'）'.$Anorm["inducat_name"].'</TD>';
			  $Strtab  .= '<TD align=left>'.$Anorm["descs"].'</TD>';
			  $tmp = ($Anorm["ifshow"])?'是':'否';
			  $Strtab  .= '<TD align=left>'.$tmp.'</TD>';
              $Strtab  .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?inducat_id='.$v.'&fid='.$fid.'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> ';
	          $Strtab  .= '<a href="'.$_SERVER["PHP_SELF"].'?inducat_id='.$v.'&fid='.$fid.'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
              $Strtab  .= '</TR>';  
	          $n ++;
              $Strtab .= tab_next_node($Anorm["next_node"],$v,$str);
			}
	   }
	}
	return $Strtab;
}
?>	
<?php
include_once( "footer.php");
?>
