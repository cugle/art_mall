<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
//header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

/* 得到关联文章的pacid 数组 */
$db_table = $pre."prattri";
$str = '<div style="margin-left: 30px">';
$_GET[pacid] = $_GET[pacid] +0;
if($_GET[pacid] )
{
     $sql = "SELECT paid,pacid,attr_name,attr_input_type,attr_values  FROM ".$db_table." 
             WHERE `pacid` = $_GET[pacid]
			 ORDER BY sort_order,paid ASC";
     $row = $oPub->select($sql);
     while( @list( $k, $v) = @each( $row) ) 
	 {
		/* 取对应值 */
		if($_GET[prid])
		 {
            $db_table = $pre."prattrival";
            $sql = "SELECT pavals  FROM ".$db_table." 
                   WHERE `paid` = $v[paid] 
			       AND prid  = $_GET[prid] 			      
			       limit 1";
			$rowpavals = $oPub->getRow($sql);
		 }
		 else
		 {
            $rowpavals[pavals] = '';
		 }

		$str .= '<span style="margin: 5px;">'.$v[attr_name].':</span>';
		$str .= '<span>';
		if(!$v[attr_input_type])
		 {
		$str .= '<INPUT TYPE="text" NAME="attr_name['.$v[paid].']" size="20" value="'.$rowpavals[pavals].'"/>'; 
		 }
		 else
		 {
			 $str .= '<SELECT NAME="attr_name['.$v[paid].']">';
			 $attr_values = str_replace("\n", ", ",$v["attr_values"]);
			 $Aattr_values = explode(", ",$attr_values);
             while( @list( $key, $val) = @each( $Aattr_values) ) 
	         {
				$selected = ($rowpavals[pavals] == $val)?'SELECTED':'';
			    $str .= '<OPTION VALUE="'.$val.'" "$selected">'.$val.'</OPTION>';

			 }
			 $str .= '</SELECT>';
		 }
		 $str .= '</span><br/>';
     }
}

echo $str;
?>