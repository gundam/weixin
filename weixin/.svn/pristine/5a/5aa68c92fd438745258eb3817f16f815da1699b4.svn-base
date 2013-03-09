<?php
$content = '<?xml version="1.0" encoding="UTF-8"?> 
<Root>
<Input>
<Property Name="Content">测试内容</Property> 
  </Input>  
  <ProcessList Template="">   
  	<Resource ID="0" Adapter="DA_HLSegment" OutputXml="true" IgnoreFailed="true" >  
		  <Param Name="Input" Value="Content" /> 
  		<Param Name="Output" Value="HLSegToken" /> 
  		<Param Name="CustomCalcSign" Value="POS_TAG" /> 
  		<Param Name="OutputFieldSign" Value="" /> 
  	</Resource> 
  		<Resource ID="1000" Adapter="ClearSegmentProxy" OutputXml="false" IgnoreFailed="true" />   
  </ProcessList>  
</Root>';
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>HLSegment API Test</title>
	</head>
	<body>
<table border="0"><tr><td>
	<form action="result.php" method="POST" target="_blank">
		<table border="0">
		<tr>
			<td>APPKEY：</td><td><input type="text" style="width:600px" name="appkey" value="5815049766379871523"></td>
		</tr>
		<tr>
			<td>私钥：</td><td><input type="text" style="width:600px" name="secret" value="1c34c03e0c95151f278272d90c8dc1a2b1e05770"></td>
		</tr>
		<tr>
			<td>命令url：</td><td><input type="text" style="width:600px" name="url" value="http://freeapi.hylanda.com/rest/se/segment/realtime"></td>
		</tr>
		<tr>
			<td valign="top">输入参数：</td><td><textarea name="content" style="height:450px;width:600px"><?php echo $content ?></textarea></td>
		</tr>
		<tr>
			<td colspan="2" align="right"><input type="submit" value="处理" onclick="document.getElementById('iresult').value=''"></td>
		</tr>
		</table>
	</form>
	</td>
<td style="display:none">
	<table border="0">
	<tr><td>
		<iframe style="height:400px;width:500px" src="" id="iresult"></iframe>
		<iframe style="display:none" id="postresult" src="" name="postresult"></iframe>
		<tr><td>&nbsp;</td></tr>
	</table>
</td></tr></table>
</body>
</html>