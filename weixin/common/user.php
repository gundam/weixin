<?php 
/**
 * 用户类
 * 用户编号为2
 * //后台
 * 增加用户
 * 查询用户
 * 删除用户
 *
 * //
 * 获取用户id
 * 获取用户昵称/姓名
 * 获取用户地址
 * 获取用户手机号
 * 获取用户历史订单记录（3张）
 */

require_once 'database.php';
require_once 'log.php';

 class User 
 {	
 	private $con;
 	private $log;
 	function User()
 	{
 		# code...
 		# 
 		$this->con=new Database();

 		$this->log=new Log();
 	}

 	//增加用户
 	public function add_user($userData)
 	{

 		$a=microtime(true);
 		$rt=$this->con->insert("wx_user",$userData);
 		$diff=microtime(true)-$a;//时间差
 		$this->log->addLog("新用户插入,微信号".$userData['wx_user_wxNumber']."时间为".date("Y:m:d H:i:s",time())."用时".$diff);
 		return $rt;
 	}

 	//查询用户
 	//
 	public function find_user($wx_user_wxNumber)
 	{
 		$str="select * from wx_user where wx_user_wxNumber=$wx_user_wxNumber";
 		$sql=$this->con->query($str);
 		$user=mysql_fetch_array($sql);
 		var_dump($user);
 	}

 	public function del_user()
 	{
 		# code...
 	}



    
 	// 获取用户id
 	// 
 	public function get_user_id($wxNumber)
 	{

 		$str="select wx_user_id from wx_user where wx_user_wxNumber='$wxNumber'";
 		
 	    $a=microtime(true);
 		$rt=$this->con->query($str);
 		$diff=microtime(true)-$a;

 		
 		 $count=mysql_num_rows($rt);
 		if ($count==0) {
 			return "false";
 			$this->log->addLog("微信用户".$wxNumber."查询了ID"."在".date("Y:m:d H:i:s",time())."用时".$diff."结果为false");

 		}else{


 			$data=mysql_fetch_array($rt);
 		$this->log->addLog("微信用户".$wxNumber."查询了ID"."在".date("Y:m:d H:i:s",time())."用时".$diff.'结果为'.$data['wx_user_id']);

 			return $data['wx_user_id'];
 		}
 	}


 	// 获取用户昵称
 	// 
 	public function get_user_name($wxNumber)
 	{	
 		$str="select wx_user_name from wx_user where wx_user_wxNumber='$wxNumber'";

 		$a=microtime(true);
 		$rt=$this->con->query($str);

 		$diff=microtime(true)-$a;

 		$this->log->addLog("微信用户".$wxNumber."查询了用户名"."在".date("Y:m:d H:i:s")."用时".$diff);
 		$data=mysql_fetch_array($rt);
 		$username=$data['wx_user_name'];

 		if ($username=='') {

 			return "false";		
 		}else{


 			return $username;
 		}

 		
 	}

 	public function get_user_address($wxNumber)
 	{
 		$str="select wx_user_address from wx_user where wx_user_wxNumber='$wxNumber'";

 		$a=microtime(true);
 		$rt=$this->con->query($str);
 		$diff=microtime(true)-$a;
 		$this->log->addLog("微信用户".$wxNumber."查询了地址"."在".date("Y:m:d H:i:s")."用时".$diff);

 		$data=mysql_fetch_array($rt);
 		$useraddress=$data['wx_user_address'];

 		if ($useraddress=='') {

 			return "false";		
 		}else{


 			return $useraddress;
 		}
 	}


 	public function get_user_all_info($wxNumber)
 	{

 		$a=microtime(true);
 		$data=$this->con->select("wx_user","wx_user_wxNumber",$wxNumber);
 		$diff=microtime(true)-$a;
 		$this->log->addLog("微信用户".$wxNumber."查询了用户所有信息"."在".date("Y:m:d H:i:s")."用时".$diff);

 		return $data;
 	}

 	public function get_user_telphone($wxNumber)
 	{
 		$str="select wx_user_telphone from wx_user where wx_user_wxNumber='$wxNumber'";

 		$a=microtime(true);
 		$rt=$this->con->query($str);
 		$diff=microtime(true)-$a;
 		$this->log->addLog("微信用户".$wxNumber."查询了号码"."在".date("Y:m:d H:i:s")."用时".$diff);

 		$data=mysql_fetch_array($rt);
 		$usertelphone=$data['wx_user_telphone'];

 		if ($usertelphone=='') {

 			return "false";		
 		}else{


 			return $usertelphone;
 		}
 		
 	}


 	







 } 

// $user=new User();
// $user->find_user("qweqw");


 ?>
