<?php 

	/**
	* 订单类
	* 订单编号为4
	* 1.生成订单
	* 2.改变订单状态
	* 3.获取一张订单
	*
	* //给后台
	*查看所有订单
	* 
	* 修改订单
	* 删除订单
	*/
	require_once 'database.php';
	require_once 'log.php';
	class Order 
	{
		private $con;
		private $log;
		function Order()
		{
			
			$this->con=new Database();
			$this->log=new Log();
		}

		public function add_order($wx_order_storeId,$wx_order_userId,$wx_order_foodId,$wx_order_userName,$wx_order_address,$wx_order_telphone,$wx_order_valid)
		
		{


			$a=microtime(true);
			$data = array('wx_order_storeId' => $wx_order_storeId,'wx_order_userId'=>$wx_order_userId,'wx_order_foodId' =>$wx_order_foodId,'wx_order_userName'=>$wx_order_userName,'wx_order_address'=>$wx_order_address,'wx_order_telphone'=>$wx_order_telphone,'wx_order_valid'=>$wx_order_valid);
			$rt=$this->con->insert("wx_order",$data);
			$diff=microtime(true)-$a;
			

			if ($rt==true) {
               $query="SELECT LAST_INSERT_ID()";
                $result=mysql_query($query);
                $rows=mysql_fetch_row($result);
                $this->log->addLog("插入了id为".$rows[0]."的订单在".date("Y:m:d H:i:s",time())."用时".$diff);
               return $rows[0];
			}
			else{

				return "false";

			}


		}
		



		public function create_order_status($status,$id)
		{	
			$a=microtime();
			$str="update wx_order set wx_order_valid=$status where wx_order_id=$id";
			$rt=$this->con->query($str);
			$diff=microtime(true)-$a;
			$this->log->addLog("确认了id为".$id.'的订单在'.date("Y:m:d H:i:s",time())."用时".$diff);

			return $rt;
		}

		public function change_order()
		{
			# code...
		}

		public function get_one_order($id)
		{
				$a=microtime(true);
				$data=$this->con->select("wx_order","wx_order_id",$id);
				$diff=microtime(true)-$a;
				$this->log->addLog("查询了id为".$id."在".date("Y:m:d H:i:s",time())."用时".$diff);
				return $data;
		}


		//店家获取最新一段时间订单
		public function get_storeLatest_order($storeId)
		{	
			$a=microtime(true);
			$str="select * from wx_order where wx_order_storeId= '$storeId'";
			$this->log->addLog("数据库操作".$str);
			$rt=$this->con->query($str);

			$diff=microtime()-$a;
			$this->log->addLog("获取了店家".$storeId."的所有订单在".date("Y:m:d H:i:s",time())."用时".$diff);
			while ( $temp =  mysql_fetch_array($rt)) {
					$orderAll .=$temp['wx_order_id'].'   ';				
			}
			if (!isset($orderAll)) {
				$orderAll='';
			}
			return $orderAll;
		}


		public function get_userLatest_orderInfo($userId)
		{
			$a=microtime(true);
			$str="select * from wx_order where wx_order_userId = '$userId' order by wx_order_id DESC";
			$this->log->addLog('数据库操作'.$str);
			$rt=$this->con->query("$str");

			$data=mysql_fetch_array($rt);


			$diff=microtime(true)-$a;

			$this->log->addLog('获取了用户'.$userId.'最新的订单在'.date("Y:m:d H:i:s",time()).'用时'.$diff);

			return $data;

		}


		public function update_order_info($orderId,$key,$value)
		{
			$str="update wx_order set $key = '$value' where wx_order_id = '$orderId'";
			$this->log->addLog('数据库操作'.$str);

			$rt= $this->con->query($str);
		
		$this->log->addLog('更新了订单'.$orderId.'的信息在'.date("Y:m:d H:i:s",time()).'用时'.$diff);
			
			if ($rt) {
				return true;

			}else{


				return false;
			}
		}
		public function get_all_order()
		{
			# code...
		}


		
		public function delete_order()
		{
			# code...
		}


		public function get_latestThreeOrders($wx_order_userId)
		{
			$a= microtime();
			$str="select * from wx_order where wx_order_userId ='$wx_order_userId' and wx_order_valid = '1' order by wx_order_time desc limit 3";
			$this->log->addLog('数据库操作'.$str);	
			$rt=$this->con->query($str);
			$diff=microtime()-$a;
			$this->log->addLog('查询了最近的3个成功订单在'.date("Y:m:d H:i:s",time()).'用时'.$diff);
			$dataArray = array();
			while ($temp=mysql_fetch_array($rt)) {
					
					//$dataArray[] .= array('wx_order_id'=>$temp['wx_order_id'],'wx_order_storeId'=>$temp['wx_order_storeId'],'wx_order_foodId'=>$temp['wx_order_foodId']);
					//array_push($dataArray, array('wx_order_id'=>$temp['wx_order_id'],'wx_order_storeId'=>$temp['wx_order_storeId'],'wx_order_foodId'=>$temp['wx_order_foodId']));
					array_push($dataArray, $temp);
			}

			//echo json_encode($dataArray);
			if (count($dataArray)==0) {
				$dataArray = '';
			}
		$this->log->addLog('二位数组'.count($dataArray).'数据'.$dataArray[0]['wx_order_storeId'].','.$dataArray[0]['wx_order_foodId']);

			return $dataArray;

		}

	}


// $order=new Order();
// $order->get_latestThreeOrders('4');
 ?>