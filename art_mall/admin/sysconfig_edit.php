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
/* 临时用此方法限制普通用户对此模块的访问 */
if($_SESSION['auser_name'] != 'admin')
{
    $Aconf['priveMessage'] = '只有超级用户才有访问此模块的权限';
    echo showMessage($Aconf['priveMessage']);
    exit;
}

$db_table = $pre."sysconfig";
if( $action == 'edit' && $scid)
{
	$scid = $scid + 0;

	if($scid == 1){
		$_POST[states] = 0;
	}

	/*处理图片*/
	if($_FILES['logo']['size'] > 0 )
	{
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['logo']['type']))
        {
            $strMessage =  '图片类型错误';
			$logo = $old_logo;
        }
		else
		{
           /* 删除原有的 LOGO */
	       if(!empty($old_logo))
	       {               
			   @unlink('../data/weblogo/' . $old_logo);
	       }
		   $logo = basename($image->upload_image($_FILES['logo'],'weblogo'));

		}
	}
	else
	{
		$logo = $old_logo;

	}

	/*处理shop_logo图片*/
	if($_FILES['shop_logo']['size'] > 0 )
	{
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['shop_logo']['type']))
        {
            $strMessage =  '图片类型错误';
			$shop_logo = $old_shop_logo;
        }
		else
		{
           /* 删除原有的 shop_logo */
	       if(!empty($old_shop_logo))
	       {
			   @unlink('../' . $old_shop_logo);
	       }
		   $shop_logo = $image->make_thumb($_FILES['shop_logo']['tmp_name'],120,120);
		}
	}
	else
	{
		$shop_logo = $old_shop_logo;
	}

    if($main_domin)
	{
		$main_domin = str_replace('http://','', $main_domin);
		$main_domin = str_replace('https://','',$main_domin);
		$main_domin = str_replace('/','',$main_domin);
		/* 找域名所有者帐号 */
        $Ascuser_name = $oPub->getRow("SELECT user_name FROM ".$db_table." WHERE scid= '".$scid."'"); 

        if(strpos($main_domin,$Aconf['mail_url'] ) )
        { 
             $main_domin = ($main_domin == 'www.'.$Aconf['mail_url'])?$main_domin:$Ascuser_name[user_name].'.'.$Aconf['mail_url'];
		} else {
            /*查询 $main_domin 是否被使用 */
            $sql = "SELECT COUNT(*) as count FROM ".$db_table." WHERE `main_domin` LIKE '".$main_domin."' AND user_name <> '".$Ascuser_name[user_name]."'";
            $Asc = $oPub->getRow($sql);
            if($Asc['count'] > 0)
            {
			    $strMessage =  $main_domin.' 域名已被其它用户使用.';
				$main_domin .= $Ascuser_name[user_name].'.'.$Aconf['mail_url'];
            }
		}
	}
    /* 基本配置信息插入前的整理 */
	$strSets = '';
	if($sets)
    foreach ($sets AS $k => $v)
    {
		$strSets .= $k.'[|]'.$v.'{|}';
	}
	$sets = $strSets;
	//地区分类 start
	if($ccid_5){
		$ccid = $ccid_5;
	}elseif($ccid_4){
		$ccid = $ccid_4;
	}elseif($ccid_3){
		$ccid = $ccid_3;
	}elseif($ccid_2){
		$ccid = $ccid_2;
	}elseif($ccid_1){
		$ccid = $ccid_1;
	}
	$ccid = $ccid;
	//行业分类  start inducat_1
	if($inducat_5){
		$inducatid = $inducat_5;
	}elseif($inducat_4){
		$inducatid = $inducat_4;
	}elseif($inducat_3){
		$inducatid = $inducat_3;
	}elseif($inducat_2){
		$inducatid = $inducat_2;
	}elseif($inducat_1){
		$inducatid = $inducat_1;
	}
	$inducatid = $inducatid;

    /* 基本配置信息插入前的整理 */
	$strSets = '';
	if($sets)
    foreach ($sets AS $k => $v)
    {
		$strSets .= $k.'[|]'.$v.'{|}';
	}
	$sets = $strSets;

   if( $action == 'edit' && $scid > 0){
	   $scid = $scid + 0;
 
        clear_all_files();
		if($user_template)
		{
		   /* 复制目录 */
		   $target_dir = ROOT_PATH .'templates/user_themes/'.$scid;
		   $source_dir = ROOT_PATH.'themes/'.$Aconf['template'].'/';
		   if (!dir_copy($source_dir,$target_dir))
		   {
			   $strMessage .=  "模板目录创建失败";
		   }
		}

		$sql = "UPDATE " . $db_table . " SET 
				main_domin='$main_domin',sets='$sets',header_title='$header_title',logo='$logo',shop_logo='$shop_logo',
				ccid='$ccid',inducatid='$inducatid',user_template='$user_template',rewrite='$rewrite',states=$states
				WHERE  scid=".$scid; 
		$oPub->query($sql);
		$sql = "UPDATE " .$pre."sysconfigfast SET main_domin='$main_domin',states=$states WHERE  scid=".$scid; 
		$oPub->query($sql);
 
   }
	/* 修改登录密码 */
	if (trim($password) != '' && $user_id)
	{
	  $user_id = $user_id + 0; 
	  $Afields=array('password'=>mkmd5($password));
	  $condition = 'user_id='.$user_id;		 
	  $oPub->update($pre."admin_user",$Afields,$condition);
	  $strMessage .= '密码修改成功';
	}
} 
//get
$db_table = $pre."sysconfig";
if( $action == 'edit' && $scid){

	$scid = $scid + 0; 
	$Anorm = $oPub->getRow("SELECT * FROM ".$pre."sysconfig WHERE scid='".$scid."'"); 

	$Asets = explode("{|}",$Anorm['sets']);
	if(count($Asets))
    foreach ($Asets AS $v)
    {
	   $At = array();
	   $At = explode("[|]",$v);
	   if($At[0])
	   {
	       $Anorm[$At[0]] = $At[1];
		}
	} 
}

if( $action == 'del' && $scid && $scid <> 1){

	 $scid = $scid + 0;
	 $sql = "SELECT  states  FROM ".$db_table." 
	       WHERE scid='".$scid ."'" ;
	 $Anorm = $oPub->getRow($sql);
	 if( $Anorm[states] == 1 )
	{
		/* 执行删除 */
		$db_table = $pre."admin_user";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."admin_userbase";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql); 
		$db_table = $pre."arti_comms";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."arti_file";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."arti_tag";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."articat";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."article";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."artitxt";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql); 
		$db_table = $pre."links";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."nav";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."prattcat";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."prattri";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."prattrival";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."pravail";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."price_history";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."probrand";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."product";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."product_comms";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."product_file";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."productcat";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."producttxt";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."prtopra";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);

		$db_table = $pre."support";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql); 
		$db_table = $pre."vote_ip";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."vote_item";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."vote_title";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);

		$db_table = $pre."users";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."users_comms";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."usersverify";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);
		$db_table = $pre."qq";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);

		$db_table = $pre."sernet";
		$sql = "DELETE FROM ".$db_table."  WHERE domain_id = '$scid'";
		$oPub->query($sql);





		$db_table = $pre."sysconfig";
		$sql = "DELETE FROM ".$db_table."  WHERE scid  = '$scid'";
		$oPub->query($sql);  

		} else {
			$strMessage = '删除后将不能恢复，请关闭网站后才能删除！';
		} 
}

/* 用户基本资料查看 */
$db_table = $pre."sysconfig";
if( $action == 'info' && $scid){

	$scid = $scid + 0;
	$sql = "SELECT * FROM ".$db_table." 
	       WHERE scid='".$scid."'" ;
	$Anorm = $oPub->getRow($sql);
	$Asets = explode("{|}",$Anorm['sets']);
	if(count($Asets))
    foreach ($Asets AS $v)
    {
	   $At = array();
	   $At = explode("[|]",$v);
	   if($At[0])
	   {
	       $Anorm[$At[0]] = $At[1];
		}
	}
    $str = '网站名:'.$Anorm['header_title'].'<br/>';
	$str .= '网 址:'.$Anorm['main_domin'].'<br/>';
    $str .= '<br/>';
	$str .= ($Anorm['user_template'])?'启用自定义模板<br/>':'';
	$str .= '邮编:'.$Anorm['zip'].'<br/>';
	$str .= '详细地址:'.$Anorm['address'].'<br/>';
	$str .= '公司名:'.$Anorm['shop_name'].'<br/>';
	$str .= '联系人:'.$Anorm['contact'].'<br/>';
	$str .= '电话:'.$Anorm['phone'].'<br/>';
    $str .= '传真:'.$Anorm['fax'].'<br/>';
	$str .= '手机:'.$Anorm['tel'].'<br/>';
    $str .= 'Email:'.$Anorm['email'].'<br/>';
	$str .= 'MSN:'.$Anorm['msn'].'<br/>';
	$str .= 'QQ:'.$Anorm['qq'].'<br/>';
	$str .= '版权提示:'.$Anorm['footer_title'].'<br/>';
	$str .= 'ICP:'.$Anorm['icp'];
	$str .= '<a href="javascript:window.close()">关闭本页</a>';
    echo showMessage($str);
	exit;
}

/* 城市列表 */
$optcountry  = $optprovince = $optcity = '';
$db_table = $pre."citycat";
$sql = "SELECT * FROM ".$db_table." where fid = 0 AND allow=1 ORDER BY ccid ASC";
$row = $oPub->select($sql);
$optcountry = '<SELECT NAME="ccid_0" id="ccid_1" onchange="return citySearch(1)">';
$optcountry .= '<OPTION VALUE="0">国家选择...</OPTION>';
while( @list( $k, $v ) = @each( $row) ) {
       $selected = ($Anorm[country] ==$v[ccid])? 'SELECTED':'';
       $optcountry .= '<OPTION VALUE="'.$v[ccid].'" '.$selected.' >'.$v[name]. '</OPTION>';
}
$optcountry .= '</SELECT>';
 
/* 网站配置信息 */
$db_table = $pre."sysconfig";

if(($act == 'search') && ($header_title  != '')) {
	$where = "   WHERE `header_title` LIKE '%".trim($header_title )."%'";
	$sql = "SELECT count( * ) AS count FROM ".$pre."sysconfig". $where ;
	$row = $oPub->getRow($sql);
	$count = $row['count'];
	unset($row);
	$page = new ShowPage;
	$page->PageSize = $Aconf['set_pagenum'];
	$page->Total = $count;
	$pagenew = $page->PageNum();
	$page->LinkAry = array(); 
	$strOffSet = $page->OffSet();
	$sql = "SELECT * FROM ".$pre."sysconfig". $where ." ORDER BY scid DESC  limit ".$strOffSet; 
} else {
    $sql = "SELECT count( * ) AS count FROM ".$pre."sysconfig";
    $row = $oPub->getRow($sql);
    $count = $row['count'];
    unset($row);
    $page = new ShowPage;
    $page->PageSize = 30;
    $page->Total = $count;
    $pagenew = $page->PageNum();
    $page->LinkAry = array(); 
    $strOffSet = $page->OffSet();
    $sql = "SELECT * FROM ".$pre."sysconfig  ORDER BY scid DESC  limit ".$strOffSet;
}

$AsysAll = $oPub->select($sql);

$StrsysAll = '';
$n = 0;
if($AsysAll)
foreach ($AsysAll AS $v) {
   $tmpstr = ($n % 2 == 0)?"even":"odd";
   $n ++ ;
   $StrsysAll .= '<TR class='.$tmpstr.'>';
   /* 专题标题 */
   $StrsysAll .= '<TD align=left><A HREF="http://'.$v['main_domin'].'/'.$SUBPATH.'" target="_blank">'.$v['header_title'].'</A></TD>';
   $StrsysAll .= '<TD align=left>'.$v['user_name'].'</TD>';
   $add_time= $oPub->getOne("SELECT add_time FROM ".$pre."admin_user  where domain_id=".$v["scid"]." order by user_id asc limit 1"); 
   if($add_time){
		$StrsysAll .= '<TD align=left>'.date("Y年m月d日",$add_time).'</TD>';
   } else {
		$StrsysAll .= '<TD align=left></TD>';
   }
   $states = '';
   if($v['states'] == 2)
   {
	   $states = '<span style="color:#c00">vip</span>';
   }  else if($v['states'] == 1) {
	   $states = '<span style="color:#CCC">关闭</span>';

   } else if($v['states'] == 0) {
	   $states = '普通';
   }
   $StrsysAll .= '<TD align=left>'.$states.'</TD>';
   //上级代理
   if($v["pre_scid"] < 1) {
		$states = '总站';
   } else {
		$sql = "SELECT main_domin,header_title FROM ".$pre."sysconfig where scid=".$v["pre_scid"]." limit 1"; 
		$row = $oPub->getRow($sql);
		$states  = '<A HREF="http://'.$row['main_domin'].'/'.$SUBPATH.'" target="_blank">'.$row['header_title'].'</A>';
   }
	$StrsysAll .= '<TD align=left>'.$states.'</TD>';
	//下级网站
	$sql = "SELECT count(*) as count FROM ".$pre."sysconfig where pre_scid=".$v["scid"]; 
	$count= $oPub->getOne($sql);
	$str = '';
	if($count > 0 && $v["pre_scid"] > 0) {
		$sql = "SELECT main_domin,header_title FROM ".$pre."sysconfig where  pre_scid=".$v["scid"]; 
		$row = $oPub->select($sql); 
		while( @list( $ks, $vs ) = @each( $row) ) {  
			$str .= '<A HREF="http://'.$vs['main_domin'].'/'.$SUBPATH.'" target="_blank">'.$vs['header_title'].'</A> ';
		}
	}
   $StrsysAll .= '<TD align=left>'.$count.' '.$str.'</TD>';
   $StrsysAll .= '<TD align=left><A HREF="'.$PHP_SELF.'?scid='.$v["scid"].'&action=info" target="_blank">
   <IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="站长信息"></A> _ ';
   $StrsysAll .= ' <a href="'.$PHP_SELF.'?scid='.$v["scid"].'&action=edit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> _ '; 
   $StrsysAll .= '<a href="'.$PHP_SELF.'?scid='.$v["scid"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>'; 
   $StrsysAll .= '</TR>';    
}

/* 城市列表 */
if($Anorm["ccid"] > 0) {
	//找到所有的上级分类start
	$sql = "SELECT fid FROM ".$pre."citycat where ccid = ".$Anorm["ccid"]." limit 1";
	$fid= $oPub->getOne($sql);
	if($fid) {
		$preCcid = pre_node_orders($fid,$pre."citycat","ccid");
		$preCcid = $preCcid.','.$Anorm["ccid"];
	} else {
		$preCcid = $Anorm["ccid"];
	} 
	$Accid = explode(",",$preCcid);
	$ccidNum = count($Accid);
	//分类选择
	while( @list( $k, $v ) = @each( $Accid) ) { 
		if($k < 1){
 			$sql = "SELECT * FROM ".$pre."citycat where fid = 0";
		}else{
			$sql = "SELECT fid FROM ".$pre."citycat where ccid = ".$v."   limit 1";
			$fid = $oPub->getOne($sql);
			if(!$fid){
				break;
			}else{
				$sql = "SELECT * FROM ".$pre."citycat where fid = ".$fid;
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
} else {
	$sql = "SELECT * FROM ".$pre."citycat where fid = 0 ";
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

/* 所属行业 */
if($Anorm["inducatid"] > 0) {
	//找到所有的上级分类start
	$sql = "SELECT fid FROM ".$pre."inducat where inducatid = ".$Anorm["inducatid"]." limit 1";
	$fid= $oPub->getOne($sql);
	if($fid){
		$preinducatid = pre_node_orders($fid,$pre."inducat","inducatid");
		$preinducatid = $preinducatid.','.$Anorm["inducatid"];
	}else{
		$preinducatid = $Anorm["inducatid"];
	} 
	$Ainducatid = explode(",",$preinducatid);
	$inducatidNum = count($Ainducatid);
	//分类选择
	while( @list( $k, $v ) = @each( $Ainducatid) ) { 
		if($k < 1){
 			$sql = "SELECT * FROM ".$pre."inducat where fid = 0";
		}else{
			$sql = "SELECT fid FROM ".$pre."inducat where inducatid = ".$v." limit 1";
			$fid = $oPub->getOne($sql);
			if(!$fid){
				break;
			}else{
				$sql = "SELECT * FROM ".$pre."inducat where fid = ".$fid;
			}
		}
		$AnormAll = $oPub->select($sql);
		$j = $k + 1;
		$keyc = "inducatOpt".$k;
		$Ainducat[$keyc] = '<SELECT NAME="inducatid" onchange="selectsAjax(this.value,\'inducat\',\'show\',\'divinducatid\','.$j.')">'; 
		$Ainducat[$keyc] .= '<OPTION VALUE="0" >选择地域分类</OPTION>';
		$n = 0;
		while( @list( $key, $value ) = @each( $AnormAll) ) {
		$n ++;
			$selected = ($value['inducatid'] == $v)? 'SELECTED':'';
			$Ainducat[$keyc] .= '<OPTION VALUE="'.$value["inducatid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>'; 
		}
		$Ainducat[$keyc] .= '</SELECT>'; 
	}
} else {
	$AnormAll = $oPub->select('SELECT * FROM '.$pre.'inducat where fid = 0 AND domain_id="'.$Aconf['domain_id'].'"'); 
	$Ainducat[inducatOpt0] = '<SELECT NAME="inducatid" onchange="selectsAjax(this.value,\'inducat\',\'show\',\'divinducatid\',1)">';
	$Ainducat[inducatOpt0] .= '<OPTION VALUE="0" >选择地域分类</OPTION>';
	$n = 0;
	while( @list( $key, $value ) = @each( $AnormAll) ) {
		$n ++;
		$Ainducat[inducatOpt0] .= '<OPTION VALUE="'.$value["inducatid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>'; 
	}
	$Ainducat[inducatOpt0] .= '</SELECT>';
}
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
<?php
if( $action == 'edit' && $scid){
?>
  <TR>
  <form name="form1" method="post" action="" enctype="multipart/form-data"> 
    <TD width="13%" align="left" colspan="7" >

        <span style="font-weight:bold">网站名:</span>
     	<input name="header_title" type="text" size="20" value="<?php echo $Anorm['header_title'];?>" />
		<span style="font-weight:bold">网 址:</span>
		<input name="main_domin" type="text" size="26" value="<?php echo $Anorm['main_domin'];?>" />
		<span style="font-weight:bold">状态:</span>
        <SELECT NAME="states">
			<OPTION VALUE="0" <?PHP if($Anorm['states'] == 0) echo 'SELECTED';?>>普通</OPTION>
			<OPTION VALUE="1" <?PHP if($Anorm['states'] == 1) echo 'SELECTED';?>>关闭</OPTION>
			<OPTION VALUE="2" <?PHP if($Anorm['states'] == 2) echo 'SELECTED';?>>vip</OPTION>
        </SELECT>
		<?php if($Anorm['scid']>0) { ?>
		    <br/>
		    <span style="font-weight:bold">修改登录密码:</span>
			<input type="text" name="password" value="" />
		    <input type="hidden" name="user_id" value="<?php echo $Anorm['user_id'];?>" />
			[注：不修改密码，请保持为空]

		<?php } ?>
		<br/>		
		
		<br/>
		<span style="font-weight: bold">L O G O:</span>
		<INPUT type="file" name="logo" size="20" /> 
		<span id="logo_show">
         <?php 
		 if($Anorm["logo"]) {
			 $tmp = '<A HREF="../data/weblogo/'.$Anorm["logo"].'" target="_blank">';
			 $tmp .= '<IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="显示缩图"></A> ';
             //$tmp .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) drop_logoImg(\''.$Anorm["logo"].'\',\'logo_show\',\''.$Anorm["scid"].'\')">';
			 $tmp .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) selectsAjax(\''.$Anorm["logo"].'\',\'sysconfig\',\'del\',\'logo_show\')">';
			 $tmp .= '<IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除缩图"></A> ';
			 echo $tmp;
		 }	
		 ?>
		 </span>			
		<span style="color:#c8c">(注：支持.jpg .gif .png .swf格式。尺寸为:120px*60px)</span>
		<INPUT type="hidden" name="old_logo"  value="<?php echo $Anorm['logo'];?>" /> 
		<br/> 
		<span style="font-weight: bold">推荐图标:</span>
		<INPUT type="file" name="shop_logo" size="20" /> 
		<span id="shop_logo_show">
         <?php 
		 if($Anorm["shop_logo"]) {
			 $tmp = '<A HREF="../data/weblogo/'.$Anorm["shop_logo"].'" target="_blank">';
			 $tmp .= '<IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="显示缩图"></A> '; 
             //$tmp .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) drop_logoImg(\''.$Anorm["shop_logo"].'\',\'shop_logo_show\',\''.$Anorm["scid"].'\')">';
			 $tmp .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) selectsAjax(\''.$Anorm["shop_logo"].'\',\'sysconfig\',\'del\',\'shop_logo_show\')">';
			 $tmp .= '<IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除缩图"></A> ';
			 echo $tmp;
		 }		 
		 ?>
		 </span>			
		<span style="color:#c8c">(注：支持.jpg .gif .png .swf格式。尺寸为200px*120px支持)</span>
		<INPUT type="hidden" name="old_shop_logo"  value="<?php echo $Anorm['shop_logo'];?>" />

        <br/><br/> 
		<span style="font-weight:bold">前台页面缓存时间:</span>
		<input name="sets[cache_time]" type="text" size="2" value="<?php echo $Anorm['cache_time'];?>" />秒
 
        <br/>
		<span style="font-weight:bold">大缩图尺寸:</span>
		宽:<input name="sets[big_thumb_w]" type="text" size="2" value="<?php echo ($Anorm['big_thumb_w'])?$Anorm['big_thumb_w']:240;?>" />
		高:<input name="sets[big_thumb_h]" type="text" size="2" value="<?php echo ($Anorm['big_thumb_h'])?$Anorm['big_thumb_h']:180;?>" />
		<br/>
		<span style="font-weight:bold">小缩图尺寸:</span>
		宽:<input name="sets[min_thumb_w]" type="text" size="2" value="<?php echo $Anorm['min_thumb_w']?$Anorm['min_thumb_w']:90;?>" />
		高:<input name="sets[min_thumb_h]" type="text" size="2" value="<?php echo $Anorm['min_thumb_h']?$Anorm['min_thumb_h']:68;?>" />
		<br/><br/>
 
 		<span style="font-weight:bold">网站静态:</span>
		<?PHP
		 $temChecked1 = ($Anorm['rewrite'])?'CHECKED':'';
		 $temChecked0 = ($Anorm['rewrite'])?'':'CHECKED';
		?>
		是<INPUT TYPE="radio" NAME="sets[rewrite]" value="1" <?php echo $temChecked1;?>>
		否<INPUT TYPE="radio" NAME="sets[rewrite]" value="0" <?php echo $temChecked0;?>>
		<span style="color:#ccc">如果开启网站伪静态，需要Apache 服务器下开启 Rewrite 模块，才能使用。</span>
		<br/> 
 		<span style="font-weight:bold">自定义模版:</span>
		<?PHP
		 $temChecked1 = ($Anorm['user_template'])?'CHECKED':'';
		 $temChecked0 = ($Anorm['user_template'])?'':'CHECKED';
		?>
		是<INPUT TYPE="radio" NAME="user_template" value="1" <?php echo $temChecked1;?>>
		否<INPUT TYPE="radio" NAME="user_template" value="0" <?php echo $temChecked0;?>>
		<span style="color:#ccc">启用自定义模版后，能在<A HREF="template_edit.php">模版管理->自定义模版</A>修改网站首页</span>
		<br/><br/>
		<span style="font-weight:bold">用户留言:</span>
		<?PHP
		 $temChecked1 = ($Anorm['support'])?'CHECKED':'';
		 $temChecked0 = ($Anorm['support'])?'':'CHECKED';
		?>
		是<INPUT TYPE="radio" NAME="sets[support]" value="1" <?php echo $temChecked1;?>>
		否<INPUT TYPE="radio" NAME="sets[support]" value="0" <?php echo $temChecked0;?>>
		<span style="color:#ccc">留言审核后才显示</span>
 
        <br/>
		<span style="font-weight:bold">友情链接:</span>
		<?PHP
		 $temChecked1 = ($Anorm['links'])?'CHECKED':'';
		 $temChecked0 = ($Anorm['links'])?'':'CHECKED';
		?>
		是<INPUT TYPE="radio" NAME="sets[links]" value="1" <?php echo $temChecked1;?>>
		否<INPUT TYPE="radio" NAME="sets[links]" value="0" <?php echo $temChecked0;?>>
		<span style="color:#ccc">友情链接审核后才显示</span>
	
		<br/><br/>
		<span style="font-weight:bold">SEO搜索关键词:</span>
		<input name="sets[keywords]" type="text" size="52" value="<?php echo $Anorm['keywords'];?>"/>
		<br/>
		<span style="font-weight:bold">SEO描述:</span>
		<br/>
		<TEXTAREA NAME="sets[description]" style="width:500;height:30px"><?php echo $Anorm['description'];?></TEXTAREA>

		<br/><br/>

		<span style="font-weight:bold;margin-left:25px">邮编:</span>
		<input name="sets[zip]" type="text" size="6" value="<?php echo $Anorm['zip'];?>" />
        <span style="font-weight:bold">城市选择：</span> 
        <?php echo $Acitycat[citycatOpt0];?>
		<span id="divccid_1"><?php echo $Acitycat[citycatOpt1];?></span><span id="divccid_2"><?php echo $Acitycat[citycatOpt2];?></span><span id="divccid_3"><?php echo $Acitycat[citycatOpt3];?></span><span id="divccid_4"><?php echo $Acitycat[citycatOpt4];?></span><span id="divccid_5"><?php echo $Acitycat[citycatOpt5];?></span> 
		 
		<br/> 
		<span style="font-weight:bold">详细地址:</span>
        <input name="sets[address]" type="text" size="52" value="<?php echo $Anorm['address'];?>" />
		<br/><br/>		

        <span style="font-weight:bold">单位名:</span>
		<input name="sets[shop_name]" type="text" size="30" value="<?php echo $Anorm['shop_name'];?>" />
		<?php echo $Ainducat[inducatOpt0];?> 
		<span id="divinducatid_1"><?php echo $Ainducat[inducatOpt1];?></span><span id="divinducatid_2"><?php echo $Ainducat[inducatOpt2];?></span><span id="divinducatid_3"><?php echo $Ainducat[inducatOpt3];?></span><span id="divinducatid_4"><?php echo $Ainducat[inducatOpt4];?></span><span id="divinducatid_5"><?php echo $Ainducat[inducatOpt5];?></span>
        
		<br/>
		<span style="font-weight:bold">联系人:</span>
		<input name="sets[contact]" type="text" size="8" value="<?php echo $Anorm['contact'];?>" /><span style="color:#F00">*</span>

		<span style="font-weight:bold">电话:</span>
		<input name="sets[phone]" type="text" size="16" value="<?php echo $Anorm['phone'];?>" />

		<span style="font-weight:bold">传真:</span>
        <input name="sets[fax]" type="text" size="16" value="<?php echo $Anorm['fax'];?>" />
		<span style="font-weight:bold">手机:</span>
		<input name="sets[tel]" type="text" size="16" value="<?php echo $Anorm['tel'];?>" />

		<br/>	

		<span style="font-weight:bold">Email:</span>
          <input name="sets[email]" type="text" size="20" value="<?php echo $Anorm['email'];?>" />
		<span style="font-weight:bold">MSN:</span>
         <input name="sets[msn]" type="text" size="20" value="<?php echo $Anorm['msn'];?>" />
		<span style="font-weight:bold">QQ:</span>
		<input name="sets[qq]" type="text" size="20" value="<?php echo $Anorm['qq'];?>" />
		<br/><br/>
		<span style="font-weight:bold" >版权提示:</span>
		<input name="sets[footer_title]" type="text" size="40" value="<?php echo $Anorm['footer_title'];?>" />
		<span style="font-weight:bold">ICP:</span>
		<input name="sets[icp]" type="text" size="30" value="<?php echo $Anorm['icp'];?>" />
		<br/> 


		<input type="hidden" name="action" value="<?php echo ($Anorm['scid'])?'edit':'add';?>" />
        <input type="submit" name="Submit" value="编辑" style="background-color: #FFCC66"/>
		<input type="hidden" name="scid" value="<?php echo ($Anorm['scid'])?$Anorm['scid']:'0';?>" />  
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'];?>" /> 
    </TD>
    </form>
  </TR>	
<?php } ?>
  <TR class=bg5>
    <form name="form2" method="post" action="">
    <TD align=left>站名
	   <INPUT TYPE="text" NAME="header_title" value="" size="20">
	   <INPUT TYPE="submit" value="网站名搜索" style="background-color: #CCFF66">
	   <INPUT TYPE="hidden" NAME="act" value="search">
	</TD>
	</form>
	<TD align=left>帐号</TD>
	<TD align=left>日期</TD>
	<TD align=left>状态</TD>
	<TD align=left>上级代理</TD>
	<TD align=left>下级网站</TD>
    <TD align=" ">操作</TD>
  </TR>


  <?php echo $StrsysAll;?>

  <TR class=bg5>
    <TD align=right colspan="7"><?php echo $showpage = $page->ShowLink();?></TD>
  </TR>
</TABLE>
</DIV>

<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT> 
<?php
include_once( "footer.php");
?>
