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
   $strMessage = '此账号没有绑定经销商，不能操作！请通过管理员设置.<br/><br/><a href="adminuser.php">用户权限->基本资料编辑 ->账号属于经销商</a>';
   echo  showMessage($strMessage);
   exit;
}

 $praid = $oPub->getOne("SELECT praid FROM ".$pre."pravail WHERE praid = ".$_SESSION['apraid']." ORDER BY praid ASC LIMIT 1"); 
 if( $praid < 1)
 {
	$strMessage = '此经销商已不存在，不能操作！请通过管理员设置.<br/><br/><a href="adminuser.php">用户权限->基本资料编辑 ->账号属于经销商</a>';
	echo  showMessage($strMessage);
	exit;
}

if($_POST['act'] == 'insert' || $_POST['act'] == 'update' )
{
	$is_insert   = $_POST['act'] == 'insert';
	$myuser_id   = $_SESSION['auser_id'];
	$up_date = local_strtotime($_POST[up_date]);
    if(trim($name) == '' )
	{
		$strMessage = '标题及内容不能为空';
	} else
	{
 	    /* 处理主图 */
		$shop_thumb = $_POST['old_shop_thumb'];
		$min_thumb = $_POST['old_min_thumb'];
	    if($_FILES["shop_thumb"]["size"] > 0 )
	    {
		    /* 判断图像类型 */
            if (!$image->check_img_type($_FILES['shop_thumb']['type']))
            {
                $strMessage =  '图片类型错误,只支持 .jpg,.gif,.png格式.\n';
            }
		    else
		    {
			    if($_FILES["shop_thumb"]["size"] > settype($_POST['MAX_FILE_SIZE'], "integer"))
			    {
				    $strMessage =  '文件太大，不能上传：最大为2M.\n';
			    } else
			    {		
   			        $img_width =  $Aconf['big_thumb_w'];
					$img_height =  $Aconf['big_thumb_h'];

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
					}
					
                    /* 生成大缩略图 */
                    $shop_thumb = $image->make_thumb($_FILES["shop_thumb"]['tmp_name'], $img_width_big , $img_height_big); 
                    /* 像册 */
					$thumb_url = $image->make_thumb($_FILES["shop_thumb"]['tmp_name'], $Aconf['min_thumb_w'], $Aconf['min_thumb_h']);
					$min_thumb = $thumb_url;
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

      //数据添加
	  $myuser_id   = $_SESSION['auser_id'];
	  $shop_sn = ($_POST[shop_sn] == '')?'un'.$Aconf['domain_id'].date("ymdHms"):$_POST[shop_sn];
	  if($is_insert)
	  {
	    /* 入库 */ 
		$sql = 'INSERT INTO '.$pre.'pravail_producttxt(prapcid,praid, pacid,prbid,user_id,name,shop_sn,shop_price,up_date,shop_number,min_thumb,shop_thumb, top , dateadd , states , domain_id )VALUES ("'.$prapcid.'","'.$_SESSION['apraid'].'","'.$pacid.'","'.$prbid.'","'.$myuser_id.'","'.$name.'","'.$shop_sn.'","'.$shop_price.'","'.$up_date.'","'.$shop_number.'","'.$min_thumb.'","'.$shop_thumb.'","'.$top.'","'.gmtime().'",0,"'.$Aconf['domain_id'].'")'; 
        $oPub->query($sql);  
        /* 编号 */
        $prid = $is_insert ? $oPub->insert_id() : $_POST['prid'];
        /* 详细记录 */ 
		$sql = 'INSERT INTO '.$pre.'pravail_product( prid,descs ,file_exp,dateadd,domain_id )VALUES ("'.$prid.'","'.$descs.'","'.$file_exp.'","'.gmtime().'","'.$Aconf['domain_id'].'")'; 
        $oPub->query($sql);
		/* 属性赋值 */
        $db_table = $pre.'pravail_prattrival';
		while( @list( $k, $v) = @each($attr_name) )
        {
			if($v){
		       $sql = 'INSERT INTO '.$pre.'pravail_prattrival( paid , prid,pavals ,domain_id)  VALUES ("'.$k.'","'.$prid.'","'.$v.'","'.$Aconf['domain_id'].'")'; 
               $oPub->query($sql);
			}
		}

		/* 相册 */
		if($filename && $thumb_url)
		{ 
		     $sql = 'INSERT INTO '.$pre.'pravail_product_file( prid,filename,thumb_url,domain_id )VALUES("'.$prid.'","'.$filename.'","'.$thumb_url.'","'.$Aconf['domain_id'].'")'; 
             $oPub->query($sql);
		 }

		$strMessage .= '添加成功!';
		$_REQUEST['prid'] = $prid;$_REQUEST['action'] = 'edit';

	  }
      else if($_POST['act'] == 'update' && $_POST['prid'] > 0)
	  {
		$db_table = $pre.'pravail_producttxt';
		$prid = $_POST['prid'] + 0;
        $sql = 'UPDATE '.$pre.'pravail_producttxt SET prapcid="'.$prapcid.'", pacid="'.$pacid.'", prbid="'.$prbid.'",
               name="'.$name.'", shop_sn="'.$shop_sn.'", shop_price="'.$shop_price.'", up_date="'.$up_date.'", shop_number="'.$shop_number.'", 
			   min_thumb = "'.$min_thumb.'",
			   shop_thumb="'.$shop_thumb.'",
               top="'.$top.'"
		       WHERE prid ="'.$prid.'" and domain_id="'.$Aconf['domain_id'].'"';
        $oPub->query($sql);  

        $sql = 'UPDATE '.$pre.'pravail_product SET  descs= "'.$descs.'", file_exp="'.$file_exp.'"  
		        WHERE prid ="'.$prid.'" and domain_id="'.$Aconf['domain_id'].'"';
        $oPub->query($sql);
		/* 属性赋值 */ 
        $sql = 'delete from '.$pre.'pravail_prattrival WHERE prid  = "'.$prid.'"';
        $oPub->query($sql);

		while( @list( $k, $v) = @each($attr_name) )
        {
			if($v)
			{
		            $sql = 'INSERT INTO '. $pre.'pravail_prattrival( paid , prid,pavals ,domain_id)  VALUES ("'.$k.'","'.$prid.'","'.$v.'","'.$Aconf['domain_id'].'")'; 
                    $oPub->query($sql);
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
		    $oPub->query('INSERT INTO '. $pre.'pravail_product_file ( prid ,  filename , thumb_url ,shop_thumb,descs,domain_id )VALUES ("'.$prid.'","'.$filename.'","'.$thumb_url.'","'.$shop_thumb.'","'.$img_desc.'","'.$Aconf['domain_id'].'")');  
		 }
		/* 编辑图片描述 old_img_desc */
		if (isset($_POST['old_img_desc']))
        {
			foreach ($_POST['old_img_desc'] AS $key => $val)
			{  
				$oPub->query('UPDATE '. $pre.'pravail_product_file SET descs = "'.$val.'" WHERE fileid ="'.$key.'"'); 
			}
		}
        $strMessage .= '修改成功！';
	  }

	  /* 处理相册图片 */
      handle_gallery_imagepro($prid, $_FILES['img_url'], $_POST['namedesc']);
    }//if(trim($name) == '')

	if($prid > 0)
	{
		$oPub->query( "update " . $pre."pravail_product_file set prid=$prid where user_id=".$_SESSION['auser_id']." and prid=".$Aconf['domain_id']."  and domain_id =".$Aconf['domain_id']);
	}  
}
/* 编辑修改结束 */


if($_REQUEST['action'] == 'edit' && $_REQUEST[prid])
{ 
	$prid = $_REQUEST[prid];
	$sql = 'SELECT a.*,b.descs,b.file_exp    
	        FROM '.$pre.'pravail_producttxt as a,'.$pre.'pravail_product as b 
			where a.prid = b.prid 
			AND a.prid = "'.$prid.'"
			AND a.domain_id="'.$Aconf['domain_id'].'"';
    $work = $oPub->getRow($sql);

	/* 产品属性 start */
    if($work[pacid] > 0 )
	{
		$Strprattvalue = '<div style="margin-left: 30px">'; 
        $sql = 'SELECT paid,pacid,attr_name,attr_input_type,attr_values  FROM '.$pre.'prattri 
                WHERE pacid = "'.$work['pacid'].'" 
			    ORDER BY sort_order,paid ASC';
        $row = $oPub->select($sql);
        while( @list( $k, $v) = @each( $row) ) 
	    {
		    /* 取对应值 */
		    if($work[prid])
		    { 
                $sql = 'SELECT pavals  FROM '.$pre.'pravail_prattrival 
                       WHERE paid = "'.$v[paid].'" AND prid  = "'.$work[prid].'" limit 1';
			    $rowpavals = $oPub->getRow($sql);
		    } else
		    {
               $rowpavals[pavals] = '';
		    }

		   $Strprattvalue .= '<span style="margin: 5px;">'.$v['attr_name'].':</span>';
		   $Strprattvalue .= '<span>';
		   if(!$v['attr_input_type'])
		   {
		       $Strprattvalue .= '<INPUT TYPE="text" NAME="attr_name['.$v['paid'].']" size="20" value="'.$rowpavals[pavals].'"/>'; 
		   } else
		   {
				$Strprattvalue .= '<SELECT NAME="attr_name['.$v['paid'].']">';
				$attr_values = str_replace("\n", ", ",$v['attr_values']);
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
    $img_list = $oPub->select('SELECT * FROM '. $pre.'pravail_product_file WHERE prid = "'.$prid.'"'); 
}

/* 找到所有的产品分类到select start*/ 
$sql = 'SELECT * FROM '.$pre.'pravail_productcat where praid = "'.$_SESSION['apraid'].'" AND domain_id="'.$Aconf['domain_id'].'" ORDER BY prapcid ASC';
$AnormAll = $oPub->select($sql);

$Stropt = '<SELECT NAME="prapcid">';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($_REQUEST['prapcid'] == $value["prapcid"])? 'SELECTED':'';
       $Stropt .= '<OPTION VALUE="'.$value["prapcid"].'" '.$selected.' >'.$value["name"].'</OPTION>';
   
}
$Stropt .= '</SELECT>';
/* 找到所有的分类到select end*/

/* 找到所有的产品属性 select start*/ 
$AnormAll = $oPub->select('SELECT pacid,paname FROM '.$pre.'prattcat where enabled = 1  ORDER BY pacid ASC'); 
$Strprattcatopt = '<SELECT NAME="pacid" id="pacid" onchange="return check_prattri()">';
$Strprattcatopt .= '<OPTION VALUE="0" >选择产品属性</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
   $n ++;
   $selected = ($work[pacid] == $value["pacid"])? 'SELECTED':'';
   $Strprattcatopt .= '<OPTION VALUE="'.$value["pacid"].'" '.$selected.' >'.$value["paname"].'</OPTION>'; 
}
$Strprattcatopt .= '</SELECT>';
/* 找到所有的产品属性 select end*/

/* 找到所有的产品品牌 select start*/ 
$AnormAll = $oPub->select('SELECT prbid,brand_name FROM '.$pre.'probrand where is_show = 1 AND domain_id="'.$Aconf['domain_id'].'" ORDER BY prbid ASC'); 
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
  <table width="800" border="0" cellspacing="0" cellpadding="0" class="button">
    <tr>
      <td align="middle">
       <span style="float: left"><a href="prav_productsend.php"> [经销商添加新产品]</a> </span>
	   <span style="float: right"> <a href="prav_productlist.php"> [经销商产品列表]</a></span>
     </td>
    </tr>
  </table>
<form action="" method="post" name="theForm" enctype="multipart/form-data" style="margin:0" />
<TABLE width="800" border="0"  cellspacing="0" cellpadding="0">
 
  <TR class=bg1>
    <TD align=left>

	      <input type="hidden" name="MAX_FILE_SIZE" value="2097152" /> 
		 <span style="color: #FF0000">*</span> 
         <b> 名 称 ： </b>
		 <input type="text" size="16" name="name" value="<?php echo ($work["prid"] > 0)?$work["name"]:'';?>" size="20"/>
		 		 
		 <b>编号：</b>
		 <input type="text" name="shop_sn" value="<?php echo ($work["prid"] > 0)?$work["shop_sn"]:'';?>" size="20"/>
		 <b>价格：</b>
		 <input type="text" name="shop_price" value="<?php echo ($work["prid"] > 0)?$work["shop_price"]:'0.00';?>" size="10"/>
		  <br/>
		 <b>上市日期：</b>
		 <input type="text" name="up_date" value="<?php echo ($work["prid"] > 0)?date("Y-m-d",$work["up_date"]):date("Y-m-d");?>" size="10"/>
        
         <b>分类选择：</b>
		 <?php echo $Stropt;?>
         <b>品牌选择：</b>
		 <?php echo $Strprobrandopt;?>

		 <?php
		 $Strtopopt = '<b>置顶：</b>';
         $Strtopopt .= '<SELECT NAME="top">';
         $selected0 = ($work[top]==0)?'selected':'';
		 $selected1 = ($work[top]==1)?'selected':'';
         $Strtopopt .= '<OPTION VALUE="0" '.$selected0.' >否</OPTION>';
		 $Strtopopt .= '<OPTION VALUE="1" '.$selected1.' >是</OPTION>';
         $Strtopopt .= '</SELECT>';
		 echo $Strcolorsopt.$Strtopopt;
		 ?>	
		 <div style="clear:left"></div>
		 <b>产品缩图：</b>		 
		 <input type="file" name="shop_thumb"  size="20"/>
		 <span id="prod_thumb_show">
         <?php 
		 if($work["shop_thumb"]) {
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
	</TD> 
  </TR>
  <TR class=bg1>
    <TD align=left>
	<b>产品描述：</b> 
	<textarea name="descs" style="width:750px;height:450px;visibility:hidden;"><?php echo $work["descs"];?></textarea> 
	</TD> 
  </TR>
</TABLE>
<!-- 附件 start -->
<DIV id=tabbar-div></DIV><!-- tab body -->
<TABLE id=gallery-table width="800" align="left">
 <tr>
   <td>
	相册：(<U>1.点“加号”可以批量上传多张图片；2.点提交按钮提交图片；3.单击相册列表中的缩图，可把原图添加到编辑器中</U>) 
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
	var strTemp = "ajax_prav_product_delimg.php?fileid=" + fileid + "&prid=" + prid + "&op=delimg&action=edit";
	//alert(strTemp);
	//document.getElementById('gallery_' + fileid).style.display = 'none';
	send_request(strTemp);	
  }
  function drop_prodtxtImg(prid,prod_thumb_file)
  {
    obj = "prod_thumb_show";
	var strTemp = "ajax_prav_product_delimg.php?prid=" + prid + "&prod_thumb_file=" + prod_thumb_file; 
	send_request(strTemp);	
  }
  function check_prattri()
    {
     obj = "prattri";
	 var pacid = document.getElementById("pacid").value;
	 var prid = document.getElementById("prid").value;
	 
	 var strTemp = "ajax_prav_check_prattri.php?pacid=" + pacid + "&prid=" + prid; 
	 send_request(strTemp);
  }
 	var editor;
	KindEditor.ready(function(K) {  
		editor = K.create('textarea[name="descs"]', {
			cssPath : 'plugins/code/prettify.css',
			uploadJson : '../upload_json.php?jsonop=pproducts&prid=<?php echo $prid;?>',
			fileManagerJson : '../upload_manager_json.php?jsonop=pproducts&prid=<?php echo $prid;?>',
			allowFileManager : false,
			width : '750px',
			height: '450px',
			resizeType: 0,
			items:['source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste','plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript','superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/','formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image','flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak','anchor', 'link', 'unlink'],

			afterCreate : function() {
				var self = this;
				K.ctrl(document, 13, function() {
					self.sync();
					K('form[name=theForm]')[0].submit();
				});
				K.ctrl(self.edit.doc, 13, function() {
					self.sync();
					K('form[name=theForm]')[0].submit();
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

/**
 * 保存某商品的相册图片
 * @param   int     $workid
 * @param   array   $image_files
 * @param   array   $image_descs
 * @return  void
 */
function handle_gallery_imagepro($prid, $image_files, $image_descs)
{
	global $image,$oPub,$pre,$Aconf;
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
            // 生成缩略图
			//if( in_array($image_files['type'][$key],$imgType) )
			if($image->check_img_type($image_files['type'][$key]))
			{ 
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
			if(empty($img_desc)){
				$A = explode(".",$image_files['name'][$key]);
				$img_desc = $A[0];
			}
            $oPub->query("INSERT INTO " . $pre."pravail_product_file (prid, filename,thumb_url,shop_thumb,descs,domain_id) 
            	VALUES ('$prid', '$img_url', '$thumb_url','$shop_thumb','$img_desc','".$Aconf['domain_id']."')"); 

        }
    }
}

?>
<?php
include_once( "footer.php");
?>

