<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image(); 
if($Aconf['priveMessage'] != '') {
   echo showMessage($Aconf['priveMessage']);
   exit;
}

if($act == 'insert' || $act == 'update' ) {
	$is_insert   = $act == 'insert';
	$myuser_id   = $_SESSION['auser_id'];
	$up_date = local_strtotime($_POST[up_date]);

	 $name = filter($name);
	 $enname = filter($enname);
	 $edit_comm = filter($edit_comm);
	 $descs = filter($descs);

    if(trim($_POST[name]) == '' ) {
		$strMessage = '标题及内容不能为空';
	}else {
 	    /* 处理主图 */
		$shop_thumb = $old_shop_thumb;
		$min_thumb = $old_min_thumb;
	    if($_FILES["shop_thumb"]["size"] > 0 ) {
		    /* 判断图像类型 */
            if (!$image->check_img_type($_FILES['shop_thumb']['type'])) {
                $strMessage =  '图片类型错误,只支持 .jpg,.gif,.png格式.\n';
            } else {
			    if($_FILES["shop_thumb"]["size"] > settype($MAX_FILE_SIZE, "integer"))
			    {
				    $strMessage =  '文件太大，不能上传：最大为2M.\n';
			    } 
				else {	
				$image_size = getimagesize($_FILES["shop_thumb"]['tmp_name']); 
				  $img_width=$image_size[0];
				   $img_height=$image_size[1];

				   $img_width_big=$Aconf['big_thumb_w'];
				   $img_height_big=intval($img_width_big * $img_height/$img_width);
				   $img_width_min=$Aconf['min_thumb_w'];
				   $img_height_min=intval($img_width_min * $img_height/$img_width);
				   //放大图
				   $img_width_mis=$Aconf['mis_thumb_w'];
				   $img_height_mis=intval($img_width_mis * $img_height/$img_width);
   			       /* $img_width = ($img_width > 0 )?$img_width:$Aconf['big_thumb_w'];
					$img_height = ($img_height > 0 )?$img_height:$Aconf['big_thumb_h'];

                    if($img_width >= $img_height )
					{
						 $img_width_big  = $img_width;
						 $img_height_big = intval($img_width * $img_height/$img_width);
					} 
					else {
                         $img_width_big  = intval($img_height * $img_width/$img_height);
						 $img_height_big = $img_height; 
					}

                    if($Aconf['min_thumb_w'] >= $Aconf['min_thumb_h'] )
					{
						 $img_width_min  = $Aconf['min_thumb_w'];
						 $img_height_min = intval($Aconf['min_thumb_w'] * $Aconf['min_thumb_h']/$Aconf['min_thumb_w']);
					} 
					else {
                          $img_width_min  = intval($Aconf['min_thumb_h'] * $Aconf['min_thumb_w']/$Aconf['min_thumb_h']);
						  $img_height_min = $Aconf['min_thumb_h']; 
					}*/
					
                    /* 生成大缩略图 */
                    $shop_thumb = $image->make_thumb($_FILES["shop_thumb"]['tmp_name'], $img_width_big , $img_height_big); 
                    /* 像册 */
					$thumb_url = $image->make_thumb($_FILES["shop_thumb"]['tmp_name'], $img_width_min, $img_height_min);
					$min_thumb = $thumb_url;
					//放大图
					$mis_thumb = $image->make_thumb($_FILES["shop_thumb"]['tmp_name'], $img_width_mis, $img_height_mis);
				    /* 原图 */
                    $filename = $image->upload_image($_FILES["shop_thumb"]);
			     }
		     }		  
	     } 
	     /* 像册图片处理 star */
         /* 检查图片：如果有错误，检查尺寸是否超过最大值；否则，检查文件类型 */
         if (isset($_FILES['img_url']['error'][0])) // php 4.2 版本才支持 error
         {
             // 最大上传文件大小
             $php_maxsize = ini_get('upload_max_filesize');
             $htm_maxsize = '2M';
             // 相册图片
             foreach ($_FILES['img_url']['error'] AS $key => $value)
             {				
                 if ($value == 0)
                {		
                    if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
                    {
                       $strMessage = '文件类型错误:'.$key;
					   break;
                    }
                 }
                 elseif ($value == 1)
                {
                    $strMessage = '文件太大:'.$key.' '.$php_maxsize;
				    break;
                }
                elseif ($_FILES['img_url']['error'] == 2)
               {
                   $strMessage = '文件太大:'.$key.' '. $htm_maxsize;
			       break;
               }
             }
	      }
          /* 4。1版本 */
          else
          {
              // 相册图片
		      while( @list( $key, $value ) = @each( $_FILES['img_url']['tmp_name']) )
              {			
                  if ($value != 'none')
                  {				
                      if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
                      {
                          $strMessage = '文件无效:'. $key + 1;
					      break;
                       }
                    }
                }
           }
	      /* 像册图片处理 end */

	    /* 关联文章处理 */
	    $cltion = '';
		while( @list( $k, $v) = @each($_POST['keysname']) )
        {
			if($_POST['keyshttp'][$k] != ''){
			   $cltion .=  $v.'[|]'.$_POST['keyshttp'][$k].'{|}';
			}

		}
	    /* 关联产品处理 */
	    $cltion_product = '';
		while( @list( $k, $v) = @each($_POST['keysname_product']) )
        {
			if($_POST['keyshttp_product'][$k] != ''){
			   $cltion_product .=  $v.'[|]'.$_POST['keyshttp_product'][$k].'{|}';
			}

		}
	    /* 关联专题处理 */
	    $cltion_topic = '';
		while( @list( $k, $v) = @each($_POST['keysname_topic']) )
        {
			if($_POST['keyshttp_topic'][$k] != ''){
			   $cltion_topic .=  $v.'[|]'.$_POST['keyshttp_topic'][$k].'{|}';
			}

		}
		/* 经销商*/
		 $praids = '';
		 if($_POST[praid])
		 foreach($_POST[praid] as $v)
         {
			 $praids .= $v.',';
		 }
		 $praids = ($praids != '')?substr($praids,0,-1):'';
      //数据添加
	  $myuser_id   = $_SESSION['auser_id'];
	  $shop_sn = ($shop_sn == '')?'un'.$Aconf['domain_id'].date("ymdHms"):$shop_sn; 

	  if($is_insert)
	  {
	    /* 入库 */ 

		$Afields=array('pcid'=>$pcid,'pacid'=>$pacid,'prbid'=>$prbid,'user_id'=>$myuser_id,'name'=>$name,'enname'=>$enname,'edit_comm'=>$edit_comm,'praids'=>$praids,'shop_sn'=>$shop_sn,'shop_price'=>$shop_price,'up_date'=>$up_date,'shop_number'=>$shop_number,'cnwidth'=>$cnwidth,'cnheight'=>$cnheight,'enwidth'=>$enwidth,'enheight'=>$enheight,'s_discount'=>$s_discount,'s_dis_exp'=>$s_dis_exp,'min_thumb'=>$min_thumb,'shop_thumb'=>$shop_thumb,'filename'=>$filename,'mis_thumb'=>$mis_thumb,'top'=>$top,'special'=>$special,'colors'=>$colors,'dateadd'=>gmtime(),'states'=>$states,'domain_id'=>$Aconf['domain_id']);
		$oPub->install($pre."producttxt",$Afields) ;  
        /* 编号 */
        $prid = $is_insert ? $oPub->insert_id() : $prid;
        /* 详细记录 */ 
		$Afields=array('prid'=>$prid,'cltion'=>$cltion,'cltion_product'=>$cltion_product,'cltion_topic'=>$cltion_topic,'file_exp'=>$file_exp,'dateadd'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
		$oPub->install($pre."product",$Afields);
		$oPub->query("UPDATE " . $pre."product SET descs = '$descs' WHERE `prid` =".$prid); 
		/* 属性赋值 */ 
		while( @list( $k, $v) = @each($_POST['attr_name']) )
        {
			if($v){ 
				$Afields=array('paid'=>$k,'prid'=>$prid,'pavals'=>$v,'domain_id'=>$Aconf['domain_id']);
				$oPub->install($pre."prattrival",$Afields);
			}
		}

		/* tag */
 
		while( @list( $k, $v) = @each($_POST['keys']) )
        {
			if($v){ 
				$Afields=array('arid'=>$prid,'art_pro_type'=>1,'keys'=>$v,'domain_id'=>$Aconf['domain_id']);
				$oPub->install($pre."arti_tag",$Afields);
			}
		}

		/* 相册 */
		if($filename && $thumb_url)
		{ 
			$Afields=array('prid'=>$prid,'filename'=>$filename,'thumb_url'=>$thumb_url,'shop_thumb'=>$shop_thumb,'domain_id'=>$Aconf['domain_id']);
			$oPub->install($pre."product_file",$Afields);
		} 
		$strMessage .= '添加成功!';
		$_REQUEST[prid] = $prid;

	  } else if($act == 'update' && $prid > 0)
	  { 
		$prid = $prid + 0; 
		$Afields=array('pcid'=>$pcid,'pacid'=>$pacid,'prbid'=>$prbid,'name'=>$name,'enname'=>$enname,'edit_comm'=>$edit_comm,'praids'=>$praids,'shop_sn'=>$shop_sn,'shop_price'=>$shop_price,'up_date'=>".$up_date.",'shop_number'=>$shop_number,'s_discount'=>$s_discount,'s_dis_exp'=>$s_dis_exp,'min_thumb'=>$min_thumb,'shop_thumb'=>$shop_thumb,'cnwidth'=>$cnwidth,'cnheight'=>$cnheight,'enwidth'=>$enwidth,'enheight'=>$enheight,'top'=>$top,'special'=>$special,'colors'=>$colors,'states'=>$states);
		$condition = "prid =".$prid." and  domain_id=".$Aconf['domain_id'];
		$oPub->update($pre."producttxt",$Afields,$condition);   

		$Afields=array('cltion'=>$cltion,'cltion_product'=>$cltion_product,'cltion_topic'=>$cltion_topic,'file_exp'=>$file_exp); 
		$condition = "prid =".$prid." and  domain_id=".$Aconf['domain_id'];
		$oPub->update($pre."product",$Afields,$condition);

		$oPub->query("UPDATE " . $pre."product SET descs = '$descs' WHERE `prid` =".$prid); 

		/* 属性赋值 */
        $db_table = $pre.'prattrival';
        $sql = "delete from ".$db_table." WHERE prid  = '".$prid."'";
        $oPub->query($sql); 
		while( @list( $k, $v) = @each($_POST['attr_name']) ) {
			if($v)
			{  
				$Afields=array('paid'=>$k,'prid'=>$prid,'pavals'=>$v,'domain_id'=>$Aconf['domain_id']);
				$oPub->install($pre."prattrival",$Afields); 
			}
		} 
		/* tag */
		$db_table = $pre.'arti_tag';
		$art_pro_type = 1;
		while( @list( $k, $v) = @each($_POST['keys']) ) {
			if($v){
				$Anorm = $oPub->getRow("SELECT atid FROM ".$pre."arti_tag WHERE atid = '$k' AND art_pro_type = '$art_pro_type' limit 1"); 
				if( $Anorm['atid'] >0){ 
					$Afields=array('keys'=>$v); 
					$condition = "atid='".$Anorm[atid]."'";
					$oPub->update($pre."arti_tag",$Afields,$condition); 
				} else 
				{ 
					$Afields=array('arid'=>$prid,'art_pro_type'=>1,'keys'=>$v,'domain_id'=>$Aconf['domain_id']);
					$oPub->install($pre."arti_tag",$Afields);
				}
			}
		}
		/* 相册 */
		if($filename && $thumb_url)
		{
			$img_desc = $_FILES["shop_thumb"]["name"]; 
			if(empty($img_desc)){
				$A = explode(".",$image_files['name'][$key]);
				$img_desc = $A[0];
			}	 
			$Afields=array('prid'=>$prid,'filename'=>$filename,'thumb_url'=>$thumb_url,'shop_thumb'=>$shop_thumb,'descs'=>$img_desc,'domain_id'=>$Aconf['domain_id']);
			$oPub->install($pre."product_file",$Afields); 
		 }
		/* 编辑图片描述 old_img_desc */
		if (isset($_POST['old_img_desc']))
        {
			foreach ($_POST['old_img_desc'] AS $key => $val)
           { 
				$Afields=array('descs'=>$val); 
				$condition = "fileid ='".$key."'";
				$oPub->update($pre."product_file",$Afields,$condition);              
		   }
		}
        $strMessage .= '修改成功！';
	  }

	  /* 记录到经销商产品对应表处理 */ 
	  if($prid > 0 && $_POST[praid])
	  {
		 foreach($_POST[praid] as $v)
         {
			$row = $oPub->getRow("SELECT ptpid FROM ".$pre."prtopra WHERE prid = '$prid' AND praid  = '$v' limit 1"); 
			if(!$row)
			{ 
				$Afields=array('praid'=>$v, 'prid'=>$prid,'domain_id'=>$Aconf['domain_id']);
				$oPub->install($pre."prtopra",$Afields);
			}
		 }
	  }

	  /* 处理相册图片 */
      handle_gallery_imagepro($prid, $_FILES['img_url'], $_POST['namedesc']);
    }//if(trim($_POST[name]) == '')
	//新加图片列表修正
	if($prid > 0)
	{
		$oPub->query( "update " . $pre."product_file set prid=$prid where user_id=".$_SESSION['auser_id']." and prid=".$Aconf['domain_id']."  and domain_id =".$Aconf['domain_id']);
	}  
}
/* 编辑修改结束 */ 

if($_REQUEST["prid"]) { 
	$prid = $_REQUEST["prid"] +0;
	$sql = "SELECT a.*,b.descs,b.cltion,b.cltion_product,b.cltion_topic,b.file_exp    
	        FROM ".$pre."producttxt as a,".$pre."product as b 
			where a.prid = b.prid 
			AND a.prid = '$prid'
			AND a.domain_id='".$Aconf['domain_id']."'";
    $work = $oPub->getRow($sql);
	/* 关联文章格式调整 */
    if($work[cltion]) {
		$strCltion = '<b>编辑关联文章：</b><br/>';
		$Acltion = explode("{|}",$work[cltion]);
        while( @list( $k, $v) = @each($Acltion) ) {
	       $Akeysname = explode("[|]",$v);
           $strCltion .= '标题：<input type="text" name="keysname[]" value="'.$Akeysname[0].'" size="50"/>';
           $strCltion .= '网址：<input type="text" name="keyshttp[]" value="'.$Akeysname[1].'" size="50"/>';

           $pos = strpos($Akeysname[1], '://');
           if ($pos === false) {
              $strCltion .= '<A HREF="../'.$Akeysname[1].'" target="_blank"> 详情>> </A><br/>';
		   }else{
			   $strCltion .= '<A HREF="'.$Akeysname[1].'" target="_blank"> 详情>> </A><br/>';
		   }
        }
	}
	/* 关联产品格式调整 */
    if($work[cltion_product]) {
		$strCltion_product = '<b>编辑关联产品：</b><br/>';
		$Acltion = explode("{|}",$work[cltion_product]);
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
	} 
	/* 关联专题格式调整 */
    if($work[cltion_topic]) {
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
	}

    /* 关键词tag */
    $db_table = $pre."arti_tag"; 
	$art_pro_type = 1;
    $sql = "SELECT * FROM ".$db_table." 
	       where arid = $prid 
		   AND art_pro_type = $art_pro_type 
		   ORDER BY atid ASC LIMIT 3";
    $row = $oPub->select($sql);
    while( @list( $k, $v) = @each( $row) ) {
	   $atid = $v[atid];
       $work[keys][$atid] = $v[keys];
    }
	/* 产品属性 start */
    if($work[pacid] > 0 )
	{
		$Strprattvalue = '<div style="margin-left: 30px">';
        $db_table = $pre."prattri";
        $sql = "SELECT paid,pacid,attr_name,attr_input_type,attr_values  FROM ".$db_table." 
                WHERE pacid = $work[pacid] 
			    ORDER BY sort_order,paid ASC";
        $row = $oPub->select($sql);
        while( @list( $k, $v) = @each( $row) ) 
	    {
		    /* 取对应值 */
		    if($work[prid]) {
                $db_table = $pre."prattrival";
                $sql = "SELECT pavals  FROM ".$db_table." 
                       WHERE paid = $v[paid] 
			           AND prid  = $work[prid]
			           limit 1";
			    $rowpavals = $oPub->getRow($sql);
		    } else {
               $rowpavals[pavals] = '';
		    }

		   $Strprattvalue .= '<span style="margin: 5px;">'.$v[attr_name].':</span>';
		   $Strprattvalue .= '<span>';
		   if(!$v[attr_input_type])
		   {
		       $Strprattvalue .= '<INPUT TYPE="text" NAME="attr_name['.$v[paid].']" size="20" value="'.$rowpavals[pavals].'"/>'; 
		   } else {
			    $Strprattvalue .= '<SELECT NAME="attr_name['.$v[paid].']">';
			    $attr_values = str_replace("\n", ", ",$v["attr_values"]);
			    $Aattr_values = explode(", ",$attr_values);
                while( @list( $key, $val) = @each( $Aattr_values) ) 
	           {
				    $selected = ($rowpavals[pavals] == $val)?'SELECTED':'';
			        $Strprattvalue .= '<OPTION VALUE="'.$val.'" "$selected">'.$val.'</OPTION>';

			   }
			  $Strprattvalue .= '</SELECT>';
		   }
		   $Strprattvalue .= '</span><br/>';
        }
	}
    /* 产品属性 end */

	/* 像册列表 */
	$db_table = $pre.'product_file';
    $sql = "SELECT * FROM " . $db_table . " WHERE prid = '$prid'";
    $img_list = $oPub->select($sql);
}
/* 商品状态 */
$Astates = array(0=>'正常',1=>'已删除',2=>'已售出');
$statsOpt = '<SELECT NAME="states">';
while( @list( $k, $v) = @each( $Astates ) ) {
	$selected = ($k == $work["states"])? 'SELECTED':'';
	$statsOpt .= '<OPTION VALUE="'.$k.'" '.$selected.' >'.$v.'</OPTION>';	
}
$statsOpt .= '</SELECT>';
/* 找到所有的产品分类到select start*/
$db_table = $pre."productcat"; 
$sql = "SELECT * FROM ".$db_table." where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY pcid ASC";
$AnormAll = $oPub->select($sql);

$Stropt = '<SELECT NAME="pcid">';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($_REQUEST['pcid'] == $value["pcid"])? 'SELECTED':'';
       $Stropt .= '<OPTION VALUE="'.$value["pcid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>';
	   /* 查找儿子 */
       if($value["next_node"] != ''){          
           $Stropt .= get_next_node($value["next_node"],$work['pcid'] );
	   }	   
}
$Stropt .= '</SELECT>';
/* 找到所有的分类到select end*/

/* 找到所有的产品属性 select start*/
$db_table = $pre."prattcat"; 
$sql = "SELECT pacid,paname FROM ".$db_table." where enabled = 1  ORDER BY pacid ASC";
$AnormAll = $oPub->select($sql);

$Strprattcatopt = '<SELECT NAME="pacid" id="pacid" onchange="return check_prattri()">';
$Strprattcatopt .= '<OPTION VALUE="0" >选择产品属性</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($work["pacid"] == $value["pacid"])? 'SELECTED':'';
       $Strprattcatopt .= '<OPTION VALUE="'.$value["pacid"].'" '.$selected.' >'.$value["paname"].'</OPTION>';
  
}
$Strprattcatopt .= '</SELECT>';
/* 找到所有的产品属性 select end*/

/* 找到所有的产品品牌 select start*/
$db_table = $pre."probrand"; 
$sql = "SELECT prbid,brand_name FROM ".$db_table." where is_show = 1 AND domain_id=".$Aconf['domain_id']." ORDER BY prbid ASC";
$AnormAll = $oPub->select($sql);

$Strprobrandopt = '<SELECT NAME="prbid">';
$Strprobrandopt .= '<OPTION VALUE="0" >选择产品品牌</OPTION>';
$n = 0;
if($AnormAll)
foreach($AnormAll as $key => $value)
{
	   $n ++;
       $selected = ($work['prbid'] == $value["prbid"])? 'SELECTED':'';
       $Strprobrandopt .= '<OPTION VALUE="'.$value["prbid"].'" '.$selected.' >'.$value["brand_name"].'</OPTION>';
  
}
$Strprobrandopt .= '</SELECT>';
/* 找到所有的产品品牌 select end*/
?>
<?php
   include_once( "header.php");
	if ($strMessage != '')
	{
		 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
	}
?>
<form action="" method="post" name="theForm" enctype="multipart/form-data" style="margin:0" />
<TABLE width="800" border=0>
  <TR>
    <TD align=left>
	   <span style="float: right"> <a href="productlist.php"> [产品列表]</a></span>
	     <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
         <b>名称：<span style="color: #FF0000">*</span></b>
		 <input type="text" size="16" name="name" value="<?php echo ($work["prid"] > 0)?$work["name"]:'';?>" size="20" title="必填"/>
		  <b>别名：<span style="color: #FF0000">*</span></b>
		 <input type="text" size="16" name="enname" value="<?php echo ($work["prid"] > 0)?$work["enname"]:'';?>" size="20" title="必填"/>		 
		 <b>产品编号：</b>
		 <input type="text" name="shop_sn" value="<?php echo ($work["prid"] > 0)?$work["shop_sn"]:'';?>" size="20" title="如果不填系统将自动生成编号"/>
		 <b>上市日期：</b>
		 <input type="text" name="up_date" value="<?php echo ($work["prid"] > 0)?date("Y-m-d",$work["up_date"]):date("Y-m-d");?>" size="10"/>
 
		 <b>库存数量：</b>
		 <input type="text" name="shop_number" value="<?php echo ($work["prid"] > 0)?$work["shop_number"]:1;?>" size="5"/>

		 <br/>
		 <b>
		 尺寸:宽<input type="text" name="cnwidth"  size="2" value="<?php echo $work[cnwidth];?>" />
		 高<input type="text" name="cnheight"  size="2" value="<?php echo $work[cnheight];?>"/>
		 </b>
		 
		 <b>
		 尺寸:宽<input type="text" name="enwidth"  size="2" value="<?php echo $work[enwidth];?>" />
		 高<input type="text" name="enheight"  size="2" value="<?php echo $work[enheight];?>"/>
		 </b>
		 <br/>
		 <b>市场价：</b>
		 <input type="text" name="shop_price" value="<?php echo ($work["prid"] > 0)?$work["shop_price"]:'0.00';?>" size="10"/>
		 <b>优惠价：</b>
		 <input type="text" name="s_discount" value="<?php echo ($work["prid"] > 0)?$work["s_discount"]:'0.00';?>" size="10"/>
		 <b>优惠价简述：</b>
		 <input type="text" name="s_dis_exp" value="<?php echo ($work["prid"] > 0)?$work["s_dis_exp"]:'';?>" size="28" TITLE="最多26个中文字(如：抢先预订9折优惠)"/>
		 <?php echo $statsOpt;?>
		 <br/>

		 <?php
		 $Strcolorsopt = '<b>名称颜色：</b>';
         $Strcolorsopt .= '<SELECT NAME="colors">';
		 $selected0 = ($work[colors]=='')?'selected':'';
		 $selected1 = ($work[colors]=='#FF0000')?'selected':'';
		 $selected2 = ($work[colors]=='#00FF00')?'selected':'';
		 $selected3 = ($work[colors]=='#0000FF')?'selected':'';
         $Strcolorsopt .= '<OPTION VALUE="" '.$selected0.' >默认颜色</OPTION>';
		 $Strcolorsopt .= '<OPTION VALUE="#FF0000" '.$selected1.' style="color:#FF0000" >红色</OPTION>';
		 $Strcolorsopt .= '<OPTION VALUE="#00FF00" '.$selected2.' style="color:#00FF00" >绿色</OPTION>';
		 $Strcolorsopt .= '<OPTION VALUE="#0000FF" '.$selected3.' style="color:#0000FF">蓝色</OPTION>';
         $Strcolorsopt  .= '</SELECT>';

		 $Strtopopt = '<b>促销：</b>';
         $Strtopopt .= '<SELECT NAME="top">';
         $selected0 = ($work[top]==0)?'selected':'';
		 $selected1 = ($work[top]==1)?'selected':'';
         $Strtopopt .= '<OPTION VALUE="0" '.$selected0.' >否</OPTION>';
		 $Strtopopt .= '<OPTION VALUE="1" '.$selected1.' >是</OPTION>';
         $Strtopopt .= '</SELECT>';

		 $Strtopoptcx = '<b>畅销：</b>';
         $Strtopoptcx .= '<SELECT NAME="special">';
         $selected0 = ($work[special]==0)?'selected':'';
		 $selected1 = ($work[special]==1)?'selected':'';
         $Strtopoptcx .= '<OPTION VALUE="0" '.$selected0.' >否</OPTION>';
		 $Strtopoptcx .= '<OPTION VALUE="1" '.$selected1.' >是</OPTION>';
         $Strtopoptcx .= '</SELECT>';
		 echo $Strcolorsopt.$Strtopopt.$Strtopoptcx;
		 ?>	
         <b>分类选择：</b>
		 <?php echo $Stropt;?>
         <b>品牌选择：</b>
		 <?php echo $Strprobrandopt;?>
		 <br/>
		 <div style="clear:left"></div>
		 <b>产品缩图：</b>		 
		 <input type="file" name="shop_thumb"  size="20"/>
		 <span id="prod_thumb_show">
         <?php 
		 if($work["shop_thumb"])
		 {
			 $tmp = '<A HREF="../'.$work["shop_thumb"].'" target="_blank">';
			 $tmp .= '<IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="显示缩图"></A> ';
			 $tmp .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) drop_prodtxtImg(\''.$work["prid"].'\',\''.$work["shop_thumb"].'\')">';
			 $tmp .= '<IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除缩图"></A> ';
		     $tmp .= '<input type="hidden" name="old_shop_thumb" value="'.$work["shop_thumb"].'" />';
			 $tmp .= '<input type="hidden" name="old_min_thumb" value="'.$work["min_thumb"].'" />';
		 } else {
             $tmp = '<input type="hidden" name="old_shop_thumb" value="" />';
			 $tmp .= '<input type="hidden" name="old_min_thumb" value="" />';
		 }
		 echo $tmp;
		 ?>
		 </span>
		 缩图尺寸:宽<input type="text" name="img_width"  size="2" value="<?php echo $Aconf[big_thumb_w];?>" />
		 高<input type="text" name="img_height"  size="2" value="<?php echo $Aconf[big_thumb_h];?>"/>
	</TD> 
  </TR>
  <TR class=bg1>
    <TD align=left>
	<B>商品简述：</B>
     <TEXTAREA NAME="edit_comm" ROWS="2" COLS="16"><?php echo ($work["prid"] > 0)?$work["edit_comm"]:'';?></TEXTAREA>
    </TD> 
  </TR>
  <TR class=bg1>
    <TD align=left>
	<B>详细描述：</B>
	<textarea name="descs" style="width:750px;height:450px;visibility:hidden;"><?php echo $work["descs"];?></textarea> 
    </TD> 
  </TR>
</TABLE>
<!-- 附件 start -->
<DIV id=tabbar-div></DIV><!-- tab body -->
<TABLE id=gallery-table width="800" align=left>
 <tr>
   <td>
    产品像册：(<U>1.点“加号”可以批量上传多张图片；2.点提交按钮提交图片；3.单击相册列表中的缩图，可把原图添加到编辑器中</U>) 
     <div id="delimg_show" style="margin: 0"> 
		<?php while( @list( $k, $v ) = @each( $img_list) ) { ?>
			<div id="gallery_<?php echo $v['fileid'];?>" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;width:122px;height:130px">
				<a href="javascript:;" onclick="if (confirm('删除')) dropImg('<?php echo $v['fileid'];?>','<?php echo $v['prid'];?>')" title="删除">[-]</a>
				<a href="../<?php echo $v['filename'];?>" target="_blank" title="查看原信息:<?php echo $v['descs'];?>">[>]</a>  
				<br />  
				<?php if($v['thumb_url'] != '') { ?>
					 <img src="../<?php  echo $v['thumb_url'];?>" width="120" height="90"  border="0" title="插入编辑器" onclick="insertHtml('<?php  echo  '../'.$v['filename'];?>','<?php  echo $v['descs'];?>')" />
				<?php } else {?>
					 <div style="width:120px;height:90px;background-color:#E4E4E4"><br/><br/><a href="../<?php  echo $v['filename'];?>" target="_blank">查阅>></a></div>
				<?php  }?> 
				<input type="text" value="<?php echo $v['descs'];?>" size="15" name="old_img_desc[<?php echo $v['fileid'];?>]" />
			</div>
		<?php } ?> 
	 </div> 
    </TD> 
  </TR> 
  <TR>
    <TD>
	<A onclick=addImg(this) href="javascript:;">[+]</A> 
	描述:<INPUT TYPE="text" NAME=namedesc[] value="" size="30"/>
	地址:<INPUT type=file name=img_url[]> 
	</TD>
  </TR>
  <TR>
    <TD>
    <b>附件下载:</b>
	<INPUT TYPE="text" NAME="file_exp" value="<?php echo ($work["prid"] > 0)?$work["file_exp"]:'';?>" size="60"/>注：可填对应文件的网址
	</TD>
  </TR>
</TABLE>
  <!-- 附件 end -->
<div style="clear:all"></div>
<TABLE width="800" border=0>
  <TR class=even>
    <TD align=left>
	  <b>产品属性：</b> 
      <?php echo $Strprattcatopt;?>
      <div id="prattri">
	  <?php echo $Strprattvalue;?>
	  </div>
    </TD> 
  </TR>
  <TR class=odd>
    <TD align=left>
     <b>关键词(TAG)：</b>
	 <?php
	   $n = 0;
	   $strKeys = '';
       while( @list( $k, $v ) = @each($work["keys"]) ) {
         $strKeys .= '<input type="text" id="keys'.$n.'" name="keys['.$k.']" value="'.$v.'" size="8"> ';
		 $n ++;
      }
	  for($n;$n < 3;$n++ )
	  {
		  $x = 88888888 + $n;
		  $strKeys .= '<input type="text" id="keys'.$n.'" name="keys['.$x.']" value="" size="8"> ';
	  }
	  echo $strKeys;
     ?>
	 <span style="cursor:pointer;color: #00CC00" onmousedown="return check_cltion_product()">
			  (搜索关联产品)
	 </span>
	 <span style="cursor:pointer;color: #CC0000" onmousedown="return check_cltion()">
			  (搜索关联文章)
	 </span>
	 <div id="cltion_product">
	 <?php echo $strCltion_product;?>
	 </div>	 
	 <div id="cltion">
	 <?php echo $strCltion;?>
	 </div>
    </TD> 
  </TR>
  <TR class=bg1>
    <TD align=left>
       <input type="submit" value="<?php echo ($work["prid"] > 0)?'修改产品':'提交新产品';?>" style="background-color: #FFCC66;margin-left: 100px"/>
	   <input type="hidden" name="prid" value="<?php echo ($work["prid"] > 0)?$work["prid"]:0;?>" id="prid" />
       <input type="hidden" name="act" value="<?php echo ($work["prid"] > 0)?'update':'insert';?>" /> 
    </TD> 
  </TR>
 </table>

</form>
<script charset="utf-8" src="../kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="../kindeditor/lang/zh_CN.js"></script> 
 
<!--以下的两个script为添加的ajax上传-->
<SCRIPT src="js/tab.js" type="text/javascript"></SCRIPT>
<SCRIPT src="js/utils.js" type="text/javascript"></SCRIPT>	
<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
 
<SCRIPT language=JavaScript>
  function addImg(obj)
  {
      var src  = obj.parentNode.parentNode;
      var idx  = rowindex(src);
      var tbl  = document.getElementById('gallery-table');
      var row  = tbl.insertRow(idx + 1);
      var cell = row.insertCell(-1);
      cell.innerHTML = src.cells[0].innerHTML.replace(/(.*)(addImg)(.*)(\[)(\+)/i, "$1removeImg$3$4-");
  }

  /**
   * ~{I>3}M<F,IO4+~}
   */
  function removeImg(obj)
  {
      var row = rowindex(obj.parentNode.parentNode);
      var tbl = document.getElementById('gallery-table');

      tbl.deleteRow(row);
  }

  /**
   * ~{I>3}M<F,~}
   */
  function dropImg(fileid,prid)
  {
    obj = "delimg_show";
	var strTemp = "ajax_product_delimg.php?fileid=" + fileid + "&prid=" + prid + "&op=delimg&action=edit";
	//alert(strTemp);
	//document.getElementById('gallery_' + fileid).style.display = 'none';
	send_request(strTemp);	
  }
  function drop_prodtxtImg(prid,prod_thumb_file)
  {
    obj = "prod_thumb_show";
	var strTemp = "ajax_product_delimg.php?prid=" + prid + "&prod_thumb_file=" + prod_thumb_file;
	//alert(strTemp);
	//document.getElementById('gallery_' + fileid).style.display = 'none';
	send_request(strTemp);	
  }
  function check_cltion()
  {
     obj = "cltion";
     var keys0 = document.getElementById("keys0").value;  
	 var keys1 = document.getElementById("keys1").value;
	 var keys2 = document.getElementById("keys2").value;
	 
	 var strTemp = "ajax_check_cltion.php?keys0=" + escape(keys0) + "&keys1=" + escape(keys1) + "&keys2=" + escape(keys2);
	 //alert(strTemp);
	 send_request(strTemp);
  }
  function check_cltion_product()
  {
     obj = "cltion_product";
     var keys0 = document.getElementById("keys0").value;  
	 var keys1 = document.getElementById("keys1").value;
	 var keys2 = document.getElementById("keys2").value;
	 var prid = document.getElementById("prid").value;
	 
	 var strTemp = "ajax_check_cltion_product.php?keys0=" + escape(keys0) + "&keys1=" + escape(keys1) + "&keys2=" + escape(keys2) + "&prid=" + prid;
	 //alert(strTemp);
	 send_request(strTemp);
  }
  function check_prattri()
    {
     obj = "prattri";
	 var pacid = document.getElementById("pacid").value;
	 var prid = document.getElementById("prid").value;
	 
	 var strTemp = "ajax_check_prattri.php?pacid=" + pacid + "&prid=" + prid;
	 //alert(strTemp);
	 send_request(strTemp);
  }
  function check_cltion_topic()
  {
     obj = "cltion_topic";
     var keys0 = document.getElementById("keys0").value;  
	 var keys1 = document.getElementById("keys1").value;
	 var keys2 = document.getElementById("keys2").value;
	 
	 var strTemp = "ajax_check_cltion_topic.php?keys0=" + escape(keys0) + "&keys1=" + escape(keys1) + "&keys2=" + escape(keys2)
	 //alert(strTemp);
	 send_request(strTemp);
  }
 
	var editor;
	KindEditor.ready(function(K) {  
		editor = K.create('textarea[name="descs"]', {
			cssPath : 'plugins/code/prettify.css',
			uploadJson : '../upload_json.php?jsonop=products&',
			fileManagerJson : '../upload_manager_json.php?jsonop=products',
			allowFileManager : false,
			width : '750px',
			height: '450px',
			resizeType: 0,
			items:['source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste','plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript','superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/','formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image','flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak','anchor', 'link', 'unlink'],

			afterCreate : function() {
				var self = this;
				K.ctrl(document, 13, function() {
					self.sync();
					K('form[name=form1]')[0].submit();
				});
				K.ctrl(self.edit.doc, 13, function() {
					self.sync();
					K('form[name=form1]')[0].submit();
				});
			} 
		}); 
		K('input[name=clear]').click(function(e) {
			editor.html('');
		}); 

	}); 

	function insertHtml(value,b) {  
		editor.focus();  
		var str = '<IMG SRC="' + value + '"  BORDER="0" ALT="' + b + '">';
		editor.insertHtml( str ); 
	} 
</SCRIPT>
<?php
/* OPTION 递归 */
function get_next_node($next_node,$fid,$str = '----')
{
   global $oPub,$pre;
   $db_table = $pre.'productcat';
   $Agrad = explode(',',$next_node);
   $Stropt = '';
   if(count($Agrad) > 0 )
	{
	   $str .= '----';
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
              $Stropt .= get_next_node($Anorm["next_node"],$fid,$str);
		   }
		   
	   }
	}
	return $Stropt;
}
/* tbale 递归 */
function tab_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre;
   $db_table = $pre.'productcat';
   $Agrad = explode(',',$next_node);
   $Strtab = '';
   if(count($Agrad) > 0 )
	{
	   $str .= '　';
	   $tn = 0;
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		    $sql = "SELECT * FROM ".$db_table." where pcid = $v";
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
              $Strtab  .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?pcid='.$v.'&fid='.$fid.'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> ';
	          $Strtab  .= '<a href="'.$_SERVER["PHP_SELF"].'?pcid='.$v.'&fid='.$fid.'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
              $Strtab  .= '</TR>';  
	          $n ++;
              $Strtab .= tab_next_node($Anorm["next_node"],$v,$str );
			}
	   }
	}
	return $Strtab;
}


/**
 * 保存某商品的相册图片
 * @param   int     $workid
 * @param   array   $image_files
 * @param   array   $image_descs
 * @return  void
 */
function handle_gallery_imagepro($prid, $image_files, $image_descs)
{
	global $image,$oPub,$pre,$_SESSION,$un_domain_id,$Aconf;
	$imgType = array(1 => 'image/gif', 2 => 'image/jpeg', 3 => 'image/png',4 => 'image/pjpeg');
	while( @list( $key, $img_desc ) = @each( $image_descs) )
    {
        /* 是否成功上传 */
        $flag = false;
        if (isset($image_files['error']))
        {
            if ($image_files['error'][$key] == 0)
            {
                $flag = true;
            }
        }
        else
        {
            if ($image_files['tmp_name'][$key] != 'none')
            {
                $flag = true;
            }
        }

        if ($flag)
        {
            
			//if( in_array($image_files['type'][$key],$imgType) )
			if($image->check_img_type($image_files['type'][$key])) { 
				// 生成小缩略图
               $thumb_url = $image->make_thumb($image_files['tmp_name'][$key],$Aconf["min_thumb_w"],  $Aconf["min_thumb_h"]);
				// 生成大缩略图
               $shop_thumb = $image->make_thumb($image_files['tmp_name'][$key],$Aconf["big_thumb_w"],  $Aconf["big_thumb_h"]);
			}
            $thumb_url = is_string($thumb_url) ? $thumb_url : '';
			$shop_thumb = is_string($shop_thumb) ? $shop_thumb : '';

            $upload = array(
                'name' => $image_files['name'][$key],
                'type' => $image_files['type'][$key],
                'tmp_name' => $image_files['tmp_name'][$key],
                'size' => $image_files['size'][$key],
            );
            if (isset($image_files['error']))
            {
                $upload['error'] = $image_files['error'][$key];
            }
            $img_original = $image->upload_image($upload);

			$target_file = $filename = ROOT_PATH.$img_original;
			$watermark = ROOT_PATH.'data/weblogo/'.$Aconf["watermark"];  
			if(file_exists($watermark)){ 
				$image->add_watermark($filename, $target_file, $watermark,5,80 ); 
			}  

            $img_url = $img_original;

           // $db_table = $pre.'product_file';
			if(empty($img_desc)){
				$A = explode(".",$image_files['name'][$key]);
				$img_desc = $A[0];
			}
            $sql = "INSERT INTO " .$pre."product_file (prid, filename,thumb_url,shop_thumb,descs,domain_id) " .
                    "VALUES ('$prid', '$img_url', '$thumb_url','$shop_thumb','$img_desc','".$Aconf['domain_id']."')";
            $oPub->query($sql);
        }
    }
}

?>
<?php
include_once( "footer.php");
?>

