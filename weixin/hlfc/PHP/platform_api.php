<?php
class platform_api
{
	private $_secret;
	
	function __construct($secret)
	{
		$this->_secret = $secret;		
	}	
	
	public function execute($url, $params)
	{
		return $this->http_post_data($url, $this->create_post_data($params));
	}
	
	private function http_post_data($url, $post_data)
	{
	     $url .= "?start_debug=1"; 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);		
		return curl_exec($ch);
	}
		
	private function create_post_data($params) 
	{   
		ksort($params); 
		
		$post_params = array();
		foreach ($params as $k => $v) 
		{
			
			$post_params[] = $k.'='.urlencode($v);
		}			
		$post_params[] = 'sig='.md5(implode("&", $post_params). $this->_secret);	
		return implode('&', $post_params);
	}	  
}

?>

