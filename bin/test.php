<?php


define('HTTP_GET',7001);
define('HTTP_POST',7002);

//include 'libraries/Function/_curl.func.php';
$_json = [
	'swift_number'  =>  '201311181736230917000',
	'timestamp'     =>  date('YmdHis').floor( microtime()*1000),
	'qos'           =>  'normal',
	'request' =>  [
		'code'  =>  '187',
	]
];

$_json = '{
    "serial_number": "201311300936239174801372VOPI",
    "timestamp": "20131130093623917",
    "qos": "normal",
    "mvnokey": "OJvWpmI",
    "service_type":"base_service",
    "service_name":"open_account",
    "api_name":"cu.vop.base_service.open_account",
    "open_account_request": {
        "order_id":"VOPS9RTWt6lUuF81SJx3NFKP3Syo",
		"serial_number":170928172261,
		"imsi":"460012024007697T",
		"user_property":1,
		"iccid":"8986011320223971841T",
		"service_entitys":[
		{
			"oper_type":"1",
			"service_code":"01",
			"service_nature":[
				{
					"oper_type":"1",
					"service_ ":"0",
					"nature_value":"1"
				}
			]
        }
        ]
    }
}';


$_json_system = [
	'mvnokey'       =>  'x9lelkX',
	'serial_number' =>  '20131110092011224jdswe23ZGLT2j',
	'timestamp'     =>  '2013-11-10 09:20:11',
	'service_type'  =>  'base_service',
	'service_name'  =>  'open_account',
	'api_name'      =>  'cu.vop.base_service.open_account',
	'qos_level'     =>  'normal',
];

$_service_entities = [
	[
		'act_type'=>'A',
        'prod_id'=>'33000',
        'prod_name'=>'3G网语音'
	],
	[
		'act_type'=>'A',
		'prod_id'=>'33019',
		'prod_name'=>'短信'
	],
];


$_json_application = [
	'open_account_request'  =>  [
		'order_id'          =>  'dik22013111009201122432sdf8x1',
		'phone_number'      =>  '17090000000',
		'imsi'              =>  '0000000000000',
		'iccid'             =>  '11111111111111111',
		'user_property'     =>  1,
		'services'          =>  $_service_entities,
		'customer'          =>  [
			'name'              =>  '王三',
            'cert_address'      =>  '北京',
            'cert_type_code'    =>  '01',
            'cert_code'         =>  '220111198603010011'
		],
	],
];



//var_dump(json_encode(array_merge($_json_system,$_json_application), JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT));

function make_random($_size = 7) {
	$_str = 'abcdefghijklmnopqresuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

	for  ($i = 0; $i<$_size; $i++) {
		echo $_str{mt_rand(0,61)};
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
body,table{
	font-size:13px;
}
table{
	table-layout:fixed;
        empty-cells:show;
        border-collapse: collapse;
        margin:0 auto;
  border:1px solid #cad9ea;
}
th{
	height:22px;
  font-size:13px;
  font-weight:bold;
  background-color:#CCCCCC;
  text-align:center;
}
td{
	height:20px;
}
.tableTitle{font-size:14px; font-weight:bold;}

</style>
<title>zuizen数据库结构</title>
</head>
<?php
$dbname = 'cu_interface_collection';
?>
<body>
<div style="margin:0 auto;width:880px; border:1px #006600 solid; font-size:12px; line-height:20px;">
	<div style="width:100%;height:30px; font-size:16px; font-weight:bold; text-align:center;">
		数据库:<?php echo $dbname; ?>
	</div>
<?php

$dbconn=new mysqli("110.249.150.80:33306", "dev","ewp2GW96UH8dbVGm");
$sqlname="information_schema";
$dbconn->select_db($sqlname);
session_start();

$sql = "SELECT * FROM tables where table_schema='".$dbname."' order by TABLE_NAME";
$result = $dbconn->query($sql);
while($row=$result->fetch_array())
{
	//print_r($row);
	?>
	<div style="margin:0 auto; width:100%; padding-top:10px;">
		<b class="tableTitle">表名： <?php echo $row["TABLE_NAME"] ?> </b> <br />
		<?php echo $row["TABLE_COMMENT"] ?>
	</div>
	<table width="100%" border="1">
		<thead>
		<th width="70">序号</td>
		<th width="170">字段名</td>
		<th width="140">字段类型</td>
		<th width="80">允许为空</td>
		<th width="70">默认值</td>
		<th>备注</td>
		</thead>
		<?php
		$sql2 = "SELECT * FROM columns where table_name='".$row["TABLE_NAME"]."' order by COLUMN_NAME";
		$result2 = $dbconn->query($sql2);

		$num=0;
		while($row2=$result2->fetch_array())
		{
			$num = $num+1;
			//print_r($row);
			?>

			<tr>
				<td align="center"><b><?php echo $num ?></b></td>
				<td><?php echo $row2["COLUMN_NAME"] ?></td>
				<td><?php echo $row2["COLUMN_TYPE"] ?></td>
				<td align="center"><?php echo $row2["IS_NULLABLE"] ?></td>
				<td align="center"><?php echo $row2["COLUMN_DEFAULT"] ?></td>
				<td><?php echo $row2["COLUMN_COMMENT"] ?></td>
			</tr>
		<?php
		}
		?>
	</table>
<?php
}
$dbconn->close();
?>

</div>
</body>
</html>
