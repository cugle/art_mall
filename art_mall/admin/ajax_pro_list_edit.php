<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//op=" + edit_price + "&prid=" + prid 

$prid = $_GET['prid'] + 0;
$edit_val = getUtf8( "$edit_val");
$str = '';
if($prid)
{
	if($_GET['praid']>0)
	{
		$praid = $_GET['praid'];
		/* 经销商价格维护 */
		//praid prid shop_price dateadd domain_id 
	    $db_table = $pre."price_history";
	    $sql = "SELECT shop_price    
	        FROM ".$db_table." 
			where  prid = '$prid'
			AND praid = '$praid'
			AND domain_id = '".$Aconf['domain_id']."' 
			ORDER BY  dateadd DESC 
			LIMIT 1";
        $row = $oPub->getRow($sql);
        /* 更新价格,记录价格变化 */
        if($_GET['op'] == 'price')
        {
	       $str = '<INPUT TYPE="text" value="'.$edit_val.'" size="10" onDblClick=pro_list_edit(\''.$_GET['op'].'\',\''.$prid.'\',this.value,\''.$praid.'\') />';
	   
	       if($edit_val != $row[shop_price])
		    {
 
				$sql = "SELECT  praids  FROM ".$pre."producttxt where  prid = '$prid'";
				$row = $oPub->getRow($sql);
			    $Apraids = explode(",",$row['praids']);
				if(count($Apraids) > 0){
					if(!in_array($praid,$Apraids)){
						$praids = $row['praids'].','.$praid;
					}else{
						$praids = $row['praids'];
					}
				}else{
					$praids = $praid;
				}

				$sql = "UPDATE " . $pre."producttxt SET `praids`='".$praids."' 
					WHERE `prid` =".$prid." and `domain_id`=".$Aconf['domain_id']; 
				if($oPub->query($sql)) {

                   $db_table = $pre."price_history";
			       $sql = "INSERT INTO ".$db_table."( praid,prid,shop_price,dateadd,domain_id) 
                           VALUES ('$praid','$prid' ,'$edit_val','".gmtime()."','".$Aconf['domain_id']."')";
			       $oPub->query($sql);
				}
		    }
        }
	} else if(!$_GET['praid']) {
       /* 网站所有者价格维护 */
	    $db_table = $pre."producttxt";
	    $sql = "SELECT shop_price  FROM ".$db_table." where  prid = '$prid'";
        $row = $oPub->getRow($sql);
        /* 更新价格,记录价格变化 */
       if($_GET['op'] == 'price') {
	       $str = '<INPUT TYPE="text" value="'.$edit_val.'" size="10" onDblClick=pro_list_edit(\''.$_GET['op'].'\',\''.$prid.'\',this.value) />'; 
	       if($edit_val != $row[shop_price]) {
 
                $sql = "UPDATE " . $db_table . " SET `shop_price`='".$edit_val."'  
	                WHERE `prid` =".$prid." and `domain_id`=".$Aconf['domain_id']; 
                if($oPub->query($sql)) {
                   $db_table = $pre."price_history";
			       $sql = "INSERT INTO ".$db_table."( praid,prid,shop_price,dateadd,domain_id) 
                           VALUES ('$praid','$prid' ,'$edit_val','".gmtime()."','".$Aconf['domain_id']."')";
			       $oPub->query($sql);
			    }

		    }
        }
        /* 更新标题 */
        if($_GET['op'] == 'name')
       {
	       $str = '<INPUT TYPE="text" value="'.$edit_val.'" size="36" onDblClick=pro_list_edit(\''.$_GET['op'].'\',\''.$prid.'\',this.value) />';
	   
	       if($edit_val != $row['name'])
		   {
               $sql = "UPDATE " . $db_table . " SET 
			      `name`='".$edit_val."' 
	               WHERE `prid` =".$prid." and `domain_id`=".$Aconf['domain_id'];
               $oPub->query($sql);
		   }
       }
        /* 置顶 */
        if($_GET['op'] == 'top')
       {
   
	       $edit_val = ($edit_val == 1)?0:1;
           $sql = "UPDATE " . $db_table . " SET 
			      `top`='".$edit_val."' 
	               WHERE `prid` =".$prid." and `domain_id`=".$Aconf['domain_id'];
           $oPub->query($sql);
           
		   $tmpstr = ($edit_val)?'是':'否';
		   $str = '<span style="cursor:pointer" onmousedown="return pro_list_edit(\'top\',\''.$prid.'\','.$edit_val.')">'.$tmpstr.'</span>';
       }
        /* 畅销 */
        if($_GET['op'] == 'special')
       {
   
	       $edit_val = ($edit_val == 1)?0:1;
           $oPub->query('UPDATE ' . $db_table . ' SET `special`="'.$edit_val.'"  WHERE `prid` ="'.$prid.'" and `domain_id`="'.$Aconf['domain_id'].'"'); 
		   $tmpstr = ($edit_val)?'是':'否';
		   $str = '<span style="cursor:pointer" onmousedown="return pro_list_edit(\'special\',\''.$prid.'\','.$edit_val.')">'.$tmpstr.'</span>';
       }
	   /* 更新折扣价 */
       if($_GET['op'] == 'discount')
       {
	       $str = '<INPUT TYPE="text" value="'.$edit_val.'" size="10" onDblClick=pro_list_edit(\''.$_GET['op'].'\',\''.$prid.'\',this.value) />';
	   
	       if($edit_val != $row['s_discount'])
		   {
               $sql = "UPDATE " . $db_table . " SET `s_discount`='".$edit_val." WHERE `prid` =".$prid." and `domain_id`=".$Aconf['domain_id'];
               $oPub->query($sql);
		   }
       }
	}	
}
echo $str;
?>