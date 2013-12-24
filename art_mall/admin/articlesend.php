<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image();
//include_once($ROOT_PATH.'includes/ckeditor/ckeditor.php');  
if(!empty($Aconf['priveMessage']))
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

if ( $_SESSION['aaction_list'] != 'all' and empty($_SESSION['aarticlecat_list']))
{
   echo showMessage("新闻分类权限没有指定，不能查阅新闻列表，请与管理员联系");
   exit;  
}

$Aarticlecat_list = false;
if(!empty($_SESSION['aarticlecat_list'])) {
  //找到所有的新闻分类权限,通过提交的分类查找包含的下级分类
	$db_table = $pre."articat";
	$Aarticlecat_list = explode(',',$_SESSION['aarticlecat_list']);
	foreach ($Aarticlecat_list AS  $v){
         $strAcid .= $v.','.next_node_all($v,$db_table,'acid',true).',';
	}
    $Aarticlecat_list = explode(',',$strAcid);
	$Aarticlecat_list = array_unique($Aarticlecat_list);
	$articlecat_list = '';
	foreach ($Aarticlecat_list AS  $v) {
         if($v > 0 ) {
              $articlecat_list .= $v.',';
		 }
	 } 
	 $articlecat_list = substr($articlecat_list,0,-1);
	 $_SESSION['aarticlecat_list'] = $articlecat_list; 
	 $Aarticlecat_list = explode(",",$_SESSION['aarticlecat_list']); //得到分类名权限
	 //查找包含的下级分类 end 
}
 
if(!isset($strMessage)) $strMessage=false;
if(!isset($inacid)) $inacid=false;
if(!isset($sort_by)) $sort_by=false;
if(!isset($start_time)) $start_time=false;
if(!isset($end_time)) $end_time=false;
if(!isset($dateadd)) $dateadd=false;
if(!isset($sear_name)) $sear_name=false; 
if(!isset($act)) $act=false; 
if(!isset($filename)) $filename=false;
$http_var = 'acid='.$inacid.'&sort_by='.$sort_by.'&start_time='.$start_time.'&end_time='.$end_time.'&dateadd='.$dateadd.'&sear_name='.$sear_name;
if($act == 'insert' || $act == 'update' ) {
	$is_insert   = $act == 'insert';
	$myuser_id   = $_SESSION['auser_id'];
	$name = filter($name);
	$edit_comm = filter($edit_comm);
	$descs = filter($descs);
	if(trim($name) == '' ) {
		//$strMessage = '标题不能为空';
		$name = sub_str(clean_html($descs),40,false); 
	} 
	$subname = empty($subname )?sub_str($name,14,false) :clean_html($subname); 
	/* 处理主图 */
	$arti_thumb = $_POST['old_arti_thumb'];
	$min_thumb = $_POST['old_min_thumb'];
	if($_FILES["arti_thumb"]["size"] > 0 ) {
		/* 判断图像类型 */
		if (!$image->check_img_type($_FILES['arti_thumb']['type'])) {
			$strMessage =  '图片类型错误,只支持 .jpg,.gif,.png格式.\n';
		}else{
			if($_FILES["arti_thumb"]["size"] > settype($_POST['MAX_FILE_SIZE'], "integer")){
				$strMessage =  '文件太大，不能上传：最大为2M.\n';
			} else{	
				
				   $image_size = getimagesize($_FILES["shop_thumb"]['tmp_name']); 
				   $img_width=$image_size[0];
				   $img_height=$image_size[1];

				   $img_width_big=$Aconf['big_thumb_w'];
				   $img_height_big=intval($img_width_big * $img_height/$img_width);
				   
				   //$img_width_min=$Aconf['min_thumb_w'];
				   //$img_height_min=intval($img_width_min * $img_height/$img_width);
				  
				  
				   $img_width_min  = $Aconf['min_thumb_w'];
			   	   $img_height_min =  $Aconf['min_thumb_h'];
					
				   
				   
				   
				   //放大图
				   $img_width_mis=$Aconf['mis_thumb_w'];
				   $img_height_mis=intval($img_width_mis * $img_height/$img_width);
				   
				   $nav_w = ($_POST['img_width'] > 0 )?$_POST['img_width']:$Aconf['nav_w'];
				   $nav_h = ($_POST['img_height'] > 0 )?$_POST['img_height']:$Aconf['nav_h'];
				/*$img_width = ($_POST['img_width'] > 0 )?$_POST['img_width']:$Aconf['big_thumb_w'];
				$img_height = ($_POST['img_height'] > 0 )?$_POST['img_height']:$Aconf['big_thumb_h'];

				if($img_width >= $img_height ) {
					 $img_width_big  = $img_width;
					 $img_height_big = intval($img_width * $img_height/$img_width);
				}  else {
					 $img_width_big  = intval($img_height * $img_width/$img_height);
					 $img_height_big = $img_height; 
				}

				if($Aconf['min_thumb_w'] >= $Aconf['min_thumb_h'] ) {
					 $img_width_min  = $Aconf['min_thumb_w'];
					 $img_height_min = intval($Aconf['min_thumb_w'] * $Aconf['min_thumb_h']/$Aconf['min_thumb_w']);
				}  else {
					  $img_width_min  = intval($Aconf['min_thumb_h'] * $Aconf['min_thumb_w']/$Aconf['min_thumb_h']);
					  $img_height_min = $Aconf['min_thumb_h']; 
				}*/
				/* 生成缩略图 */
				 $arti_thumb = $image->make_thumb($_FILES["arti_thumb"]['tmp_name'], $nav_w, $nav_h);
				/* 像册 */
				 $thumb_url = $image->make_thumb($_FILES["arti_thumb"]['tmp_name'],  $img_width_min , $img_height_min);
				 $min_thumb = $thumb_url;
				/* 原图 */
				 $filename = $image->upload_image($_FILES["arti_thumb"]);
			 }
		 }		  
	}//$_FILES["arti_thumb"]["size"] 

	/* 像册图片处理 star */
	/* 检查图片：如果有错误，检查尺寸是否超过最大值；否则，检查文件类型 */
	if (isset($_FILES['img_url']['error'][0])) // php 4.2 版本才支持 error
	{
		// 最大上传文件大小
		$php_maxsize = ini_get('upload_max_filesize');
		$htm_maxsize = '2M';
		// 相册图片
		foreach ($_FILES['img_url']['error'] AS $key => $value) {				
			if ($value == 0) {		
				if (!$image->check_img_type($_FILES['img_url']['type'][$key])) {
					$strMessage = '文件类型错误:'.$key;
					break;
				}
			} elseif ($value == 1) {
				$strMessage = '文件太大:'.$key.' '.$php_maxsize;
				break;
			} elseif ($_FILES['img_url']['error'] == 2) {
				$strMessage = '文件太大:'.$key.' '. $htm_maxsize;
				break;
			}
		}//foreach
	}
	/* 4。1版本 */
	else {
		// 相册图片
		while( @list( $key, $value ) = @each( $_FILES['img_url']['tmp_name']) ) {			
			if ($value != 'none') {				
				if (!$image->check_img_type($_FILES['img_url']['type'][$key])) {
					$strMessage = '文件无效:'. $key + 1;
					break;
				}
			}
		}
	}
	/* 像册图片处理 end */

	/* 关联新闻处理 */
	$cltion = '';
	while( @list( $k, $v) = @each($_POST['keysname']) ) {
		if($_POST['keyshttp'][$k] != ''){
		   $cltion .=  $v.'[|]'.$_POST['keyshttp'][$k].'{|}';
		} 
	}
	/* 关联产品处理 */
	$cltion_product = '';
	while( @list( $k, $v) = @each($_POST['keysname_product']) ) {
		if($_POST['keyshttp_product'][$k] != ''){
		   $cltion_product .=  $v.'[|]'.$_POST['keyshttp_product'][$k].'{|}';
		} 
	}
	/* 关联专题处理 */
	$cltion_topic = '';
	while( @list( $k, $v) = @each($_POST['keysname_topic']) ){
		if($_POST['keyshttp_topic'][$k] != ''){
		   $cltion_topic .=  $v.'[|]'.$_POST['keyshttp_topic'][$k].'{|}';
		} 
	} 
	//数据添加  
	$otherurl = trim($otherurl);
	$otherurl = str_replace('http://','', $otherurl);
	if($otherurl) {
		$otherurl = "http://".$otherurl;
	}
	//是否加入图库  
	$ifpic = $ifpic; 
	$arti_date = local_strtotime($arti_date);
	 
	if($is_insert) {
		/* 入库 */  
		$Afields=array('acid'=>$inacid,'aaid'=>$aaid,'ifpic'=>$ifpic,'name'=>$name,'subname'=>$subname,'otherurl'=>$otherurl,'edit_comm'=>$edit_comm,'top'=>$top,'colors'=>$colors,'arti_date'=>$arti_date,'dateadd'=>gmtime(),'user_id'=>$myuser_id,'min_thumb'=>$min_thumb,'arti_thumb'=>$arti_thumb,'states'=>$states,'domain_id'=>$Aconf['domain_id']);
		$oPub->install($pre."artitxt",$Afields) ;  
		$arid = $is_insert ? $oPub->insert_id() : $arid;  

				
		$Afields=array('arid'=>$arid,'user_id'=>$myuser_id,'sour'=>$sour,'sourhttp'=>$sourhttp,'name'=>$name,'cltion'=>$cltion,'cltion_product'=>$cltion_product,'cltion_topic'=>$cltion_topic,'dateadd'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
		$oPub->install($pre."article",$Afields) ; 
		
		$oPub->query("UPDATE " . $pre."article SET descs = '$descs' WHERE `arid` =".$arid); 
 

		/* tag */ 
		foreach ($_POST['keys'] AS $k => $v) {
			if($v){
				$Afields=array('arid'=>$arid,'keys'=>$v,'domain_id'=>$Aconf['domain_id']);
				$oPub->install($pre."arti_tag",$Afields); 
			}
		}
		/* 相册 */ 
		if($filename && $thumb_url) { 
			$Afields=array('arid'=>$arid,'filename'=>$filename,'thumb_url'=>$thumb_url,'descs'=>$subname,'domain_id'=>$Aconf['domain_id']);
			$oPub->install($pre.'arti_file',$Afields) ;
		 }
		 $strMessage = '添加成功!'; 
		 $_REQUEST['action'] = 'edit' ;
		 $_REQUEST['arid']      =  $arid;
	} else if($act == 'update' && $_POST['arid'] > 0 && $_POST['states'] < 1)
	{
  
		$arid = $arid+0; 
		$Afields=array('acid'=>$inacid,'aaid'=>$aaid,'ifpic'=>$ifpic,'name'=>$name,'subname'=>$subname,'otherurl'=>$otherurl,'edit_comm'=>$edit_comm,'top'=>$top,'colors'=>$colors,'arti_date'=>$arti_date,'min_thumb'=>$min_thumb,'arti_thumb'=>$arti_thumb);
		$condition = "arid =".$arid." and states = 0 and domain_id=".$Aconf['domain_id'];
		$oPub->update($pre."artitxt",$Afields,$condition);  
 
		$Afields=array('sour'=>$sour,'sourhttp'=>$sourhttp,'name'=>$name,'cltion'=>$cltion,'cltion_product'=>$cltion_product,'cltion_topic'=>$cltion_topic);
		$condition = "arid =".$arid."  and domain_id=".$Aconf['domain_id'];
		$oPub->update($pre."article",$Afields,$condition); 

		$oPub->query("UPDATE " . $pre."article SET descs = '$descs' WHERE `arid` =".$arid); 
		/* tag */ 
		$art_pro_type = 0;
		$oPub->query("delete from ".$pre."arti_tag WHERE art_pro_type =0  AND  arid ='".$arid."'");
		foreach ($_POST['keys'] AS $k => $v) {
			$k= $k+0;
			if($v){   
				$Afields=array('arid'=>$arid,'keys'=>$v,'domain_id'=>$Aconf['domain_id']);
				$oPub->install($pre."arti_tag",$Afields) ; 
			}
		}
		/* 相册 */
		if(!isset($filename)) $filename=false;

		if($filename && $thumb_url) {  
			$Afields=array('arid'=>$arid,'filename'=>$filename,'thumb_url'=>$thumb_url,'descs'=>$subname,'domain_id'=>$Aconf['domain_id']);
			$oPub->install($pre."arti_file",$Afields) ; 
		 }
		/* 编辑图片描述 old_img_desc */
		if (isset($_POST['old_img_desc'])) {
			foreach ($_POST['old_img_desc'] AS $key => $val) { 
				$Afields=array('descs'=>$val);
				$condition = "fileid =".$key;
				$oPub->update($pre."arti_file",$Afields,$condition);  
		   }
		}
        $strMessage .= ' 修改成功！';
	  }
		/* 处理相册图片 */
		handle_gallery_image($arid, $_FILES["img_url"], $_POST['namedesc']); 
		$_REQUEST['action'] = 'edit' ;
		$_REQUEST['arid']      =  $arid;
		//新加图片列表修正
		if($arid > 0)
		{
			$oPub->query( "update " . $pre."arti_file set arid=$arid where user_id=".$_SESSION['auser_id']." and arid=".$Aconf['domain_id']." and type='descs' and domain_id =".$Aconf['domain_id']);
		}
} 
$strCltion = $strCltion_product  = $strCltion_topic = '';
if($action == 'edit' && $arid) {   
	$work = $oPub->getRow("SELECT a.arid,a.acid,a.acid,a.aaid,a.vtid,a.ifpic,a.name,a.subname,a.otherurl,a.top,a.colors,a.arti_date,a.min_thumb,a.arti_thumb,a.states,a.edit_comm,b.sour,b.sourhttp,b.descs,b.cltion,b.cltion_product,b.cltion_topic  FROM ".$pre."artitxt as a,".$pre."article as b 
			where a.arid = b.arid AND a.arid = $arid AND a.domain_id='".$Aconf['domain_id']."'"); 
	$work["arti_date"]  = date("Y-m-d H:i", $work["arti_date"]); 
	/* 关联新闻格式调整 */ 
	$n = 0;
    if($work["cltion"]) {
		$strCltion = '<b>编辑关联新闻：</b><br/>';
		$Acltion = explode("{|}",$work["cltion"]);
        while( @list( $k, $v) = @each($Acltion) ) {
	       $Akeysname = explode("[|]",$v);
		   if(!isset($Akeysname[0])) $Akeysname[0]=false;
		   if(!isset($Akeysname[1])) $Akeysname[1]=false;
           $strCltion .= '标题：<input type="text" name="keysname['.$n.']" value="'.$Akeysname[0].'" size="50"/>';
           $strCltion .= '网址：<input type="text" name="keyshttp['.$n.']" value="'.$Akeysname[1].'" size="50"/>';

           $pos = strpos($Akeysname[1], '://');
           if ($pos === false) {
              $strCltion .= '<A HREF="../'.$Akeysname[1].'" target="_blank"> 详情>> </A><br/>';
		   }else{
			   $strCltion .= '<A HREF="'.$Akeysname[1].'" target="_blank"> 详情>> </A><br/>';
		   }
		   $n ++ ;
        }
		$work["strCltion"] = $strCltion;
	}
	/* 关联产品格式调整 */
    if($work["cltion_product"])
	{
		$strCltion_product = '<b>编辑关联产品：</b><br/>';
		$Acltion = explode("{|}",$work["cltion_product"]);
        while( @list( $k, $v) = @each($Acltion) ) {
	       $Akeysname = explode("[|]",$v);
           $strCltion_product .= '标题：<input type="text" name="keysname_product[]" value="'.$Akeysname[0].'" size="50"/>';
           $strCltion_product .= '网址：<input type="text" name="keyshttp_product[]" value="'.$Akeysname[1].'" size="50"/>';

           $pos = strpos($Akeysname[1], '://');
           if ($pos === false) {
               $strCltion_product .= '<A HREF="../'.$Akeysname[1].'" target="_blank"> 详情>> </A><br/>';
		   }else{
			   $strCltion_product .= '<A HREF="'.$Akeysname[1].'" target="_blank"> 详情>> </A><br/>';
		   }
        }
		$work["strCltion_product"] = $strCltion_product;
	}
	/* 关联专题格式调整 */
    if($work["cltion_topic"]) {
		$strCltion_topic = '<b>编辑关联专题：</b><br/>';
		$Acltion = explode("{|}",$work[cltion_topic]);
		while( @list( $k, $v) = @each($Acltion) ) {
		   $Akeysname = explode("[|]",$v);
		   $strCltion_topic .= '标题：<input type="text" name="keysname_topic[]" value="'.$Akeysname[0].'" size="50"/>';
		   $strCltion_topic .= '网址：<input type="text" name="keyshttp_topic[]" value="'.$Akeysname[1].'" size="50"/>';

		   $pos = strpos($Akeysname[1], '://');
		   if ($pos === false) {
			  $strCltion_topic .= '<A HREF="../'.$Akeysname[1].'" target="_blank"> 详情>> </A><br/>';
		   }else{
			   $strCltion_topic .= '<A HREF="'.$Akeysname[1].'" target="_blank"> 详情>> </A><br/>';
		   }
		}
		$work["strCltion_topic"] = $strCltion_topic;
	}
    /* 关键词tag */ 
	$art_pro_type = $j = 0;  
    $row = $oPub->select("SELECT * FROM ".$pre."arti_tag where arid = $arid AND art_pro_type = $art_pro_type ORDER BY atid ASC LIMIT 3"); 
    while( @list( $k, $v) = @each( $row) ) {
	   $atid = $v["atid"];
       $work["keys"][$atid] = $v[keys]; 
	   $j ++ ;
    }
	/* 像册列表 */ 
    $work["img_list"] =  $oPub->select("SELECT * FROM " . $pre."arti_file WHERE arid = '$arid'");  
}

/* 找到所有的分类到select start*/ 
$AnormAll = $oPub->select("SELECT * FROM ".$pre."articat where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY acid ASC"); 
$Stropt = '<SELECT NAME="inacid">';
$n = 0;
if(!isset($work['acid'])) $work['acid']=false;

while( @list( $key, $value ) = @each( $AnormAll) ) {
	$inacid = !empty($work['acid']) ? $work['acid']:$inacid;
	$selected = ($inacid == $value["acid"])? 'SELECTED':'';
	if(is_array($Aarticlecat_list) && $_SESSION['aaction_list'] != 'all') {
		if(in_array($value["acid"],$Aarticlecat_list)) {
			$n ++;  
			$Stropt .= '<OPTION VALUE="'.$value["acid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>';
		}
	} else {
		 $n ++;
		 $Stropt .= '<OPTION VALUE="'.$value["acid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>';
	}
	/* 查找儿子 */
	if($value["next_node"] != '') {          
		 $Stropt .= get_next_node($value["next_node"],$work['acid'],$str = '　',$Aarticlecat_list);
	} 
}
$Stropt .= '</SELECT>';
$Ahome["Stropt"]  = $Stropt;
/* 找到所有的分类到select end*/

/* 找到所有的属性到select start*/ 
$AnormAll = $oPub->select("SELECT * FROM ".$pre."arti_attr where  domain_id=".$Aconf['domain_id']." ORDER BY aaid ASC"); 
$Stroptaaid = '<SELECT NAME="aaid">';
$n = 0;
if(!isset($work['aaid'])) $work['aaid']=false;
while( @list( $key, $value ) = @each( $AnormAll) )
{
    $n ++;
    $selected = ($work['aaid'] == $value["aaid"])? 'SELECTED':'';
    $Stroptaaid .= '<OPTION VALUE="'.$value["aaid"].'" '.$selected.' >'.$value["attr_name"].'</OPTION>';
}
$Stroptaaid .= '</SELECT>';
$Ahome["Stroptaaid"] = $Stroptaaid;
/* 找到所有的属性到select end*/
//调查项显示
$vt_name = '';
if(!isset($work["arid"])) $work["arid"]=false;
if($work["arid"] > 0){
	if($work["vtid"] > 0){
		$vt_name = $oPub->getOne("SELECT vt_name FROM ".$pre."vote_title where vtid=".$work["vtid"]);  
		$vt_name = '<span style="color:#f00;font-size:20px">[<a href="vote_title.php?vtid='.$work["vtid"].'&action=edit&arid='.$work["arid"].'" >网上关联调查：'.$vt_name.'</a>]</span>';
	}else{
		$vt_name = '<span style="color:#0c0;font-size:20px">[<a href="vote_title.php?arid='.$work["arid"].'">网上关联调查申请</a>]</span>';
	}
} 
assign_template($Aconf);
$Ahome["vt_name"]     = $vt_name;  
$Ahome["Stroptaaid"]  = $Stroptaaid; 
$Ahome["http_var"]    = $http_var;
$Ahome["sear_name"]   = $sear_name;
$Ahome["dateadd"]     = $dateadd;
$Ahome["end_time"]    = $end_time;
$Ahome["start_time"]  = $start_time;
$Ahome["sort_by"]     = $sort_by;  
$Ahome["nowName"]     = $nowName; 
$work["arti_date"]    = !empty($work["arti_date"])?$work["arti_date"]:date("Y-m-d H:i:s");
$Ahome["work"]        = $work; 
$Ahome["strMessage"]  = $strMessage;  
$smarty->assign('home', $Ahome );  
$smarty->display($Aconf["displayFile"]);

/* OPTION 递归 */
/* OPTION 递归 */
function get_next_node($next_node,$fid,$str = '　',$Aarticat = false)
{
   global $oPub,$pre,$_SESSION,$un_aaction_list;
   $db_table = $pre.'articat';
   $Agrad = explode(',',$next_node);
   $Stropt = '';
   if(count($Agrad) > 0 )
	{
	   $tn = 0;
	   $str .= '　';
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		   $sql = "SELECT * FROM ".$db_table." where acid = $v";
           $Anorm = $oPub->getRow($sql);
		   if( $Anorm["name"] != ''){
			   $tn ++;
			   $selected = ($fid == $v)? 'SELECTED':'';
			   if(is_array($Aarticat)  && $_SESSION['aaction_list'] != 'all')
			   {
                  if(in_array($v,$Aarticat))
				   {
					  $tn ++;
                      $Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$Anorm["name"].'</OPTION>';
				   }
			   }
			   else
			   {
				   $tn ++;
		          $Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$Anorm["name"].'</OPTION>';
			   }

              $Stropt .= get_next_node($Anorm["next_node"],$fid,$str,$Aarticat);
		   }
		   
	   }
	}
	return $Stropt;
}
/* tbale 递归 */
function tab_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre;
   $db_table = $pre.'articat';
   $Agrad = explode(',',$next_node);
   $Strtab = '';
   if(count($Agrad) > 0 )
	{
	   $tn = 0;
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		    $sql = "SELECT * FROM ".$db_table." where acid = $v";
            $Anorm = $oPub->getRow($sql);
			if( $Anorm["name"] != ''){
			  $tn ++ ;
	          $tmpstr = ($n % 2 == 0)?"even":"odd";
              $Strtab  .= '<TR class='.$tmpstr.'>';

              $Strtab  .= '<TD align=left>'.$str.$tn.'）'.$Anorm["name"].'</TD>';
			  $Strtab  .= '<TD align=left>'.$Anorm["descs"].'</TD>';
			  $tmp = ($Anorm["ifshow"])?'是':'否';
			  $Strtab  .= '<TD align=left>'.$tmp.'</TD>';
	          $tmp = ($Anorm["ifnav"])?'是':'否';
	          $Strtab .= '<TD align=left>'.$tmp.'</TD>';
              $Strtab  .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?acid='.$v.'&fid='.$fid.'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> ';
	          $Strtab  .= '<a href="'.$_SERVER["PHP_SELF"].'?acid='.$v.'&fid='.$fid.'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
              $Strtab  .= '</TR>';  
	          $n ++;
              $Strtab .= tab_next_node($Anorm["next_node"],$v,$str .= '　');
		      $str = '　';
			}
	   }
	}
	return $Strtab;
}
 ?> 

