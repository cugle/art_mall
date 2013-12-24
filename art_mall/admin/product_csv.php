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

/* 检查csv文件 与 添加到数据表*/
if($_POST['csv_name'])
{
	$Apath = explode("/",$_POST['csv_name']);
	$sountpath = (count($Apath) > 1)?$Apath[0].'/':'';

    $file_path = ROOT_PATH."images/csvfiles/".trim($_POST['csv_name']);
    if(!file_exists($file_path))
	{
        $strMessage =  $_POST['csv_name']."文件不存在,请上传对应的csv文件";
		echo "<SCRIPT language='javascript'>\nalert('$strMessage');location='product_csv.php';</script>";
		exit;
	}

     if($_POST['csv_install'] == 1)
	{
         /* 添加到数据表 */
	}
	else
	{
		$csv_install = 1;
		$pacid = 11;
		$myuser_id = $_SESSION['auser_id'];
        /* 注意属性值的取值，需要依据属性分类手动设定 $Aattr => 属性值 $pacid=>属性ID */

		/* csv 文件列表 */
        /* 将文件按行读入数组，逐行进行解析 */
        $line_number = 0;
        $csv_file = file($file_path);
        foreach ($csv_file AS $line)
        {
            if ($line_number <= 1)
            {
			   if($line_number == 0)
				{
                   $v = explode(",",$line);
                   $ncol = count($v);
				   $A_0   = $v;         //得到分类ID,属性ID，属性值ID
				   $pacid = $A_0[$ncol-2]; //属性ID
				   $pcid  = $A_0[$ncol-1]; //分类ID
				}

               $line_number++;
               continue;			    
            }
            //产品编号,产品名,产品规格,装箱尺寸,图片地址,属性编号,分类编号
			//判断编码类型，只支持gbk,utf8，如果不是GBK则默认为utf-8,并只判断第一个词
            $line = isUTF8($line)?$line:iconv('GBK','UTF-8',$line);
            $v = explode(",",$line);
			/* 生成缩图 */
			//$v[11]
			$filename =  ROOT_PATH."images/csvfiles/".$sountpath.trim($v[$ncol-3]);
            if(file_exists( $filename ))
			{
                $shop_thumb = $image->make_thumb($filename, 208,  208);
			    $thumb_url = $image->make_thumb($filename, 80,  80);
                $Afilename = explode("/",$shop_thumb);

				$kf = count($Afilename) - 1;
				$tfile = 'sour'.$Afilename[$kf];
				$target = '';
			    foreach ($Afilename AS $k=>$v)
		        {

					$target .= ($k == $kf)?$tfile:$v.'/'; 		
			    }
                if(function_exists(rename))
				{
				    $filename = (rename($filename , ROOT_PATH.$target))?$target:'';
				}
				else
				{
                   if(copy($filename,ROOT_PATH.$target))
					{
                       unlink(ROOT_PATH.$filename);
					   $filename = $target;
					}
					else
					{
						$filename = '';
					}                  
				   
				}
			}
			else
			{
                $shop_thumb = $thumb_url = $filename = '';
			}

	        /* 入库产品库 */

		    $db_table = $pre.'producttxt';			
		    $sql = "INSERT INTO " . $db_table . " ( `pcid` , `pacid`, `user_id`,`name`,`shop_sn`,`min_thumb`,`shop_thumb`, `dateadd` , `domain_id` )" .
                 "VALUES ('$pcid','$pacid','$myuser_id', '".trim($v[1])."', '".trim($v[0])."', '$min_thumb','$shop_thumb', '".gmtime()."', '".$Aconf['domain_id']."')"; 
            $oPub->query($sql);
			$prid = $oPub->insert_id();

             /* 详细记录 */
			if($prid)
			{
                $db_table = $pre.'product';
		        $sql = "INSERT INTO " . $db_table . " ( `prid` ,`dateadd` , `domain_id`  )" .
                     "VALUES ($prid,'".gmtime()."', '".$Aconf['domain_id']."')"; 
                $oPub->query($sql);

		         /* 属性赋值 */
                 $db_table = $pre.'prattrival';
			     $sql = "INSERT INTO " . $db_table."( `paid` , `prid`,`pavals` ,`domain_id`)  VALUES";
			     foreach ($v AS $ak=>$av)
		        {
				   if($ak > 1 && $ak < $ncol-3)
					{
		              $sql .=   "('".trim($A_0[$ak])."','$prid','".trim($av)."','".$Aconf['domain_id']."'),"; 
					}
			     }				 
                 $sql = substr($sql,0,-1);
			     $oPub->query($sql);	
             
			     /* 加到像册 */
		         if($filename != '')
		         {
		             $db_table = $pre.'product_file';
		             $sql = "INSERT INTO " . $db_table . " ( `prid` ,  `filename` , `thumb_url`,`domain_id` )" .
                        "VALUES ($prid,'$filename', '$thumb_url','".$Aconf['domain_id']."')"; 
                     $oPub->query($sql);
		         }
			}

	    }//foreach
		$line_number++;
        $strMessage =  $line_number."条记录添加成功!";
		echo "<SCRIPT language='javascript'>\nalert('$strMessage');location='product_csv.php';</script>";
		if($line_number >  1)
		{
		   //unlink($file_path);
		}
		exit;
	}
}
?>
<?php
   include_once( "header.php");
?>


<TABLE width="96%" border=0>
  <TR class=bg5>
    <TD align=right></TD>
  </TR> 
  <TR class=bg2>
    <TD align=left>
       <span style="float: left"> <a href="protocsv.php">[导出csv文件格式]</a></span> 
	   <span> &#187;&#187;<a href="fl_php/index.html">[批量上传图片及csv文件]</a></span>
	   <span style="color:#FFF"> &#187;&#187;[csv文件导入数据表]</span> 	    
	   <span> &#187;&#187;<a href="productlist.php"> [查看商品列表]</a></span> 
     </td>
  </tr>
  <form action="" method="post" name="theForm">
  <TR class=bg1>    
    <TD align=left>
          <b>csv文件导入数据表:</b><BR/><BR/><b>输入CSV文件路径：</b>
		 <input type="text" name="csv_name" value="<?php echo $_POST['csv_name']?$_POST['csv_name']:date("Ym").'/osunit.csv';?>" size="40"/>
		 <input type="hidden" name="csv_install" value="<?php echo $csv_install==1?1:0;?>"/>
         <input type="submit" value="CSV文件记录添加到数据库"  style="background-color: #FFCC66;margin-left:5px"/>
	  <br/> CSV文件中一次上传商品数量最好不要超过500，CSV文件大小最好不要超过300K.
	  <!--
	  <br/> 生成csv 属性格式>> (暂缓开发,用户指定属分类与属性值)
	  -->
    </TD>
  </TR>
  </form>
</TABLE>
<TABLE width="96%" border=0>
  <TR class=odd>
    <TD align=left>
     使用说明：
      <br/>1. CSV文件名固定为：osunit.csv；
	  <br/>2. <span style="color:#f00">文件内容不能有","</span>；
      <br/>3. 填写csv文件，可以使用excel或文本编辑器打开csv文件；
      <br/>4. 图片文件名只能为英文或数字，例如图片为abc.jpg，只要填写 abc.jpg 即可；
	  <br/>5. 导出保存的csv文件第一行为分类编号，第二行为属性名，不能修改；
	  <br/>
	  <br/>
	  <hr size="5">
	  <span style="color:#F00">产品批量导入流程:</span><br/>
	    1.导出保存csv文件<a href="protocsv.php">&#187;&#187;</a><br/>
		   |<br/>
		2.用excel打开文件填写对应内容(分类编号与属性编号可以不填写)<br/>
		   |<br/>
		3.批量上传图片及csv文件<a href="fl_php/index.html">&#187;&#187;</a><br/>
		   |<br/>
		4.csv文件导入数据表<a href="product_csv.php">&#187;&#187;</a>

    </TD> 
  </TR>  
 </table>
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

