<?php
/* 公共AJAX调用模块 
   //a 选择的值   b :数据库名  c:显示出来的样式名 d:操作类型del show install edit  
   obj = c; 
   var strTemp = "selectsajax.php?op=" + d + "&cstyle=" + c + "&bdatebase=" + b + "&avalue=" + escape(a);
*/
define('IN_OUN', true);
include_once( "./includes/command.php"); 
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false); 

$value		= getUtf8( $_GET["avalue"]);
$datebase	= $bdatebase;
$op			= $op;
$str = '';
if( $op ) {
	$db_table = $pre.$datebase; 
    if($op == 'show'){
		//sysconfig.php地区AJAX调用 1级 start
		if($value &&  $cstyle == 'divccid')
		{
			$AnormAll = $oPub->select('SELECT * FROM '.$db_table.' where fid = "'.$value.'"'); 
			if($AnormAll)
			{
				$num = $cstyleend + 1;  
				$str .= '<SELECT NAME="ccid_'.$cstyleend.'" onchange="selectsAjax(this.value,\'citycat\',\'show\',\''.$cstyle.'\','.$num.')">';
				$str .= '<OPTION VALUE="0" >下级分类</OPTION>';
				$n = 0;
				while( @list( $key, $value ) = @each( $AnormAll) ) {
					$n ++;
					$str .= '<OPTION VALUE="'.$value["ccid"].'">'.$value["name"].'</OPTION>'; 
				}
				$str .= '</SELECT>';
			}
		}//$cstyle == 'divccid_1' end 
		//sysconfig.php 所属行业 AJAX调用 1级 start
		if($value &&  $cstyle == 'divinducatid')
		{
			$AnormAll = $oPub->select('SELECT * FROM '.$db_table.' where fid = "'.$value.'"'); 
			if($AnormAll)
			{
				$num = $cstyleend + 1;  
				$str .= '<SELECT NAME="inducat_'.$cstyleend.'" onchange="selectsAjax(this.value,\'inducat\',\'show\',\''.$cstyle.'\','.$num.')">';
				$str .= '<OPTION VALUE="0" >下级分类</OPTION>';
				$n = 0;
				while( @list( $key, $value ) = @each( $AnormAll) ) {
					$n ++;
					$str .= '<OPTION VALUE="'.$value["inducatid"].'">'.$value["name"].'</OPTION>'; 
				}
				$str .= '</SELECT>';
			}
		}//$cstyle == 'divccid_1' end 
	}////////////// $op == show end
	//留言AJAX回复 start
	if($op == 'supre' )
	{
		$spid = $spid + 0;
		if($spid > 0 )
		{ 
			$value = filter($value);//关键词过滤
			if(!empty($value))
			{ 
				$spid = $oPub->getOne('SELECT spid FROM '.$pre.'support where domain_id = '.$Aconf['domain_id'].' and spid = '.$spid);
				if($spid > 0)
				{  
					$oPub->query('UPDATE '.$pre.'support SET comms=comms + 1  WHERE domain_id = '.$Aconf['domain_id'].' and spid = '.$spid); 
					$Afields=array('spid'=>$spid,'users_id'=>$_SESSION['user_id'],'ip'=>real_ip(),'supports'=>$value,'dateadd'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
					$oPub->install($pre.'support_re',$Afields); 
				}
			}

			$AnormAll = $oPub->select('SELECT * FROM '.$pre.'support_re where spid = "'.$spid.'"'); 
			if($AnormAll)
			{ 
				$str .= '';
				$n = 0;
				while( @list( $key, $value ) = @each( $AnormAll) )
				{
					$n ++;
					$row = $oPub->getRow('SELECT user_name,avatar from '.$pre.'users where   id='.$value['users_id']);
				    $user_name = (empty($row['user_name']))?'匿名':$row['user_name']; 
					if($row['avatar'] > 0)
					{ 
						$avatar    = '<IMG SRC="data/userimg/avatar_small/'.$row['avatar'].'_small.jpg" WIDTH="16" HEIGHT="16" BORDER="0" >';
					}else
					{
						$avatar    = '<IMG SRC="images/command/osunt_back.png" WIDTH="16" HEIGHT="16" BORDER="0" >';
					}

					$str .= '<div style="border-bottom:1px dotted #ccc;margin-left: 20px">';
					$str .= '<b title="'.$value['ip'].'">'.$n.')'.$avatar.$user_name.' </b>';
					$str .=  $value['supports'].'<span style="font-size: 9px;color:#A7A7A7;margin-left: 5px;">'.date("y.m.d h:i",$value['dateadd'])."</span></div>"; 
				}  
			} 
		}else
		{
			$str = '参数错误，重新提交！';
		}
		//留言AJAX回复 end
	} 
	//////////////////
}else{
	$str = '无对应值！';
}
echo $str;
?>