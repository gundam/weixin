<?php 

	/**
	 * 食品代号为3
	* 食品类
	*  获取食品id
	*  获取食品单价
	*  获取食品店家
	*  获取食品名称
	* .根据商店找食品
	* 搜索功能
	* 
	* //后台
	* 增加食品
	* 删除食品
	* 查询食品
	* 
	*/

	require_once 'database.php';
	require_once 'log.php';
	require_once 'store.php';

	class Food
	{
		private $con;
		private $log;
		private $store;
		function Food()
		{
			# code...
			$this->con=new Database();
			$this->log=new Log();

			$this->store=new Store();
		}

		// 获取食品id
		public function get_food_id()
		{
			
		}
		// 获取食品单价
		public function get_food_price($wx_food_id)
		{	
			$a=microtime(true);
			$str="select * from wx_food where wx_food_id=$wx_food_id";
			$rt=$this->con->query($str);
			$diff=microtime(true)-$a;
			$this->log->addLog("查询了食物id为".$wx_food_id."的价格"."在".date("Y:m:d H:i:s")."用时".$diff);
			if (mysql_fetch_array($rt)) {
				# code...
			}
			$content=mysql_fetch_array($rt);

			return $content['wx_food_price'];



		}

		// 获取食品名称
		// 
		public function get_food_name($wx_food_id)
		{	
				$a=microtime(true);
				$str="select wx_food_name from wx_food where wx_food_id= '$wx_food_id'";
				$sql=$this->con->query($str);
				$diff=microtime(true)-$a;

				
				

				$temp=mysql_fetch_array($sql);

				$this->log->addLog("查询了食物id为".$wx_food_id."的名称"."在".date("Y:m:d H:i:s",time())."用时".$diff.'  结果是'.$temp['wx_food_name']);
				return $temp['wx_food_name'];


		}

		//获取食品店家
		//
		public function get_food_belong($wx_food_id)
		{
				$str="select wx_food_belong from wx_food where wx_food_id=$wx_food_id";
				$sql=$this->con->query($str);
				$this->log->addLog("查询了食物id为".$wx_food_id."的所属商家"."在".date("Y:m:d H:i:s")."用时".$diff);
				$temp=mysql_fetch_array($sql);
				return $temp['wx_food_belong'];
		}

		




		//增加食品

		public function add_food($wx_food_name,$wx_food_belong,$wx_food_price,$wx_food_img)
		{
			$a=microtime(true);
			$data=array("wx_food_name"=>$wx_food_name,"wx_food_belong"=>$wx_food_belong,"wx_food_price"=>$wx_food_price,"wx_food_img"=>$wx_food_img);
			$diff=microtime(true)-$a;
			$this->log->addLog("管理员增加了食品".$wx_food_name."在".date("Y:m:d H:i:s")."用时".$diff);
			return $this->con->insert("wx_food",$data);

		}


		// 删除食品
		// 
		public function delete_food($value='')
		{
			
		}


		// 查询食品
		// 
		public function find_food($value='')
		{
			
		}


		//得到一种食品信息
		//
		public function get_one_food($id)
		{
			$data=$this->con->select("wx_food","wx_food_id",$id);


			return $data;
		}

		//搜索食品
		//
		public function get_food_bySearch($likeFoodName)
		{
			$a=microtime(true);
			$queryFoodStr="select * from wx_food where wx_food_name like '%$likeFoodName%' or wx_food_id = '$likeFoodName' order by wx_food_type";
     		$this->log->addLog('数据库操作'.$queryFoodStr);
     		$sql=$this->con->query($queryFoodStr);
			$diff=microtime(true)-$a;
        if (mysql_num_rows($sql)) {


           $foodInfoStr="食物:\n";
          while ($temp=mysql_fetch_array($sql)) {
          	if (!isset($type)) {
          		$type=$temp['wx_order_type'];
          	}
          	if ($type!=$temp['wx_order_type']) {
          		$foodInfoStr .="\n\n\n";
          		$type=$temp['wx_order_type'];
          	}
          	$this->log->addLog('类型是'.$type);

            $storeName=$this->store->get_store_wxName($temp['wx_food_belong']);
            $foodInfoStr .=$temp['wx_food_id'].":".$temp['wx_food_name'].",属于:".$temp['wx_food_belong'].':'.$storeName.",价格: ".$temp['wx_food_price']."\n";
          }

          $this->log->addLog("模糊查询了食物关键字为".$likeFoodName."在".date("Y:m:d H:i:s")."结果是".$foodInfoStr."用时".$diff);
           }else
          {

           $foodInfoStr="没有食物\n";

		 $this->log->addLog("模糊查询了食物关键字为".$likeFoodName."在".date("Y:m:d H:i:s")."结果是".$foodInfoStr."用时".$diff);

           }

           return $foodInfoStr;
		}

		public function get_random_food()
		{	
			$totalNumber=$this->con->get_total_number('wx_food');
			for ($i=0; $i < 3; $i++) { 
				
				$num[] .= rand(2,$totalNumber);
			}

			$this->log->addLog('随机寻找了3三种食品'.$num[0].'    '.$num[1].'    '.$num[2]);


			$str="select * from wx_food where wx_food_id ='3".$num[0]."'or wx_food_id ='3".$num[1]."'or wx_food_id='3".$num[2]."'";

			
			$a=microtime(true);
			$rt=$this->con->query($str);
			$diff=microtime(true)- $a;
			$this->log->addLog('数据库操作'.$str.'用时'.$diff);
			$dataArray= array();
			 while ($temp=mysql_fetch_array($rt)) {
			 	array_push($dataArray, $temp);
			 }

			 return $dataArray;
		}



	}



 ?>