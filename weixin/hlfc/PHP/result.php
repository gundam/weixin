<?php

if($_REQUEST['content'] == "" || $_REQUEST['url'] == "")
{
	$ret = "输入不能为空";
	echo $ret;
	exit();
}
else 
{
	include_once("./platform_api.php");
	 echo $content =  $_REQUEST['content'];
	 echo $sUrl    =  $_REQUEST['url'];
 	 echo $sSecret =  $_REQUEST['secret'];
	 echo $sAppKey =  $_REQUEST['appkey'];	
	 $params['appkey'] = $sAppKey;
	 $params['v'] = '1.0';
	 $params['time'] = time();//1305690837;//time();
	 $params['xmlparam'] = $content;	
	 $params['format']='JSON';
	
	$api = new platform_api($sSecret);
	$ret = $api->execute($sUrl, $params);
	if($ret === false)
		echo "服务请求失败！";
	else
	{
		 header("Content-Type:text/html;charset=utf-8");
		 $temp=json_decode($ret);
		echo urldecode(json_encode($temp));
		//echo  json_encode($ret);
	}
}
?>
