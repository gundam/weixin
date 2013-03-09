<?php 
  /**
  * 商店类
  * 商店编号1
  * 1.查询商店id
  * 2.查询商店名称
  * 3.查询商店号码
  * 4.查询商店经纬度
  * 5.查询商店地址
  * 搜索功能
  * 7.*根据经纬度获取附近商家
  * 8.根据食物名模糊查找商家
  * //给后台
  * 列出所有商店名称
  * 增加店家
  * 删除店家			
  */
  require_once 'database.php';
  require_once 'log.php';
  class Store 
  {	
  	private $con;
    private $log;
  	
  	function Store()
  	{
  		$this->con=new Database();
      $this->log=new Log();
     
  	}

  	//@1查询商家id
  	//
  	public function get_store_id($wxNumber)
  	{
      $a=microtime(true);
  		$storeinfo=$this->con->select("wx_store","wx_store_wxNumber",$wxNumber);
      $diff=microtime(true)-$a;
      

      
      $this->log->addLog("微信用户".$wxNumber."查询了店家id"."在".date("Y:m:d H:i:s",time())."用时".$diff."查询结果id为".$storeinfo['wx_store_id']."   测试");
      return $storeinfo['wx_store_id'];
  		
  	}

  	// 查询商店名称
  	// 
  	public function get_store_wxName($id)
  	{  
      $a=microtime(true);
      $data=$this->con->select("wx_store","wx_store_id",$id);
      $diff=microtime(true)-$a;
      $this->log->addLog("查询了店家id为".$id."在".date("Y:m:d H:i:s",time())."用时".$diff.'结果为'.$data['wx_store_wxName']);

      return $data['wx_store_wxName'];

  		
  	}



  	// 查询商店号码
  	// 
  	public function get_store_wxNumber()
  	{
  		
  	}

  	// 查询商店经纬度
  	// 
  	
  	public function get_store_location()
  	{
  		# code...
  	}
  	// 查询商店地址
  	// 

  	// 后台
  	// 列出所有商店名称
  	// 
  	public function get_all_store_wxName()
  	{
  		# code...
  	}


  	//根据经纬度获取附近商家
  	//
  	public function get_srore_byLocation($maxLat,$minLat,$maxLng,$minLng)
  	{

         $str="select wx_store_wxName from wx_store where wx_store_longitude between '1' and '1111' and wx_store_latitude between '1' and  '1111'";
          $sql=$this->con->query($str);   

        // $store_name[]="";
         while ($temp=mysql_fetch_array($sql)) {
           # code...
           $store_name[]=$temp;
         }
         if (!isset($store_name)) {
           $store_name[]="";
         }
         return $store_name;

  	}

    //取出该商店id的食物列表
    public function get_food_byStore($wx_store_id)
    {
      
    

        
      $str="select wx_food_id,wx_food_name,wx_food_price,concat('编号:',wx_food_id,',',wx_food_name,',',wx_food_price,'元') as string,wx_food_type from wx_food where wx_food_belong=$wx_store_id order by wx_food_type";
      
      $this->log->addLog('数据库操作'.$str);
      $sql=$this->con->query($str);

      while ($temp=mysql_fetch_array($sql)) {
        if (!isset($type)) {

          $type=$temp['wx_food_type'];
           $dataArray[] .=$temp['wx_food_type'];
        }
        if ($type!=$temp['wx_food_type']) {
          $type=$temp['wx_food_type'];
          $dataArray[] .="\n\n\n".$temp['wx_food_type'];
        }
        $dataArray[] .=$temp['string'];
      }

      if (!isset($dataArray)) {
          $dataArray='';
      }
      return $dataArray;



    }


    //搜索商店
    public function get_store_bySearch($likeStoreName)
    {
        $a=microtime(true);
         $queryStoreStr="select * from wx_store where wx_store_wxName like '%$likeStoreName%' or wx_store_id = '$likeStoreName'";
         $sql=$this->con->query($queryStoreStr);
        $diff=microtime(true)-$a;


        if (mysql_num_rows($sql)) {
          $storeInfoStr="餐馆是:\n";
          while ($temp=mysql_fetch_array($sql)) {
            // code...
            $storeInfoStr .=$temp['wx_store_id'].":".$temp['wx_store_wxName']."\n";

          }

       $this->log->addLog("模糊查询了餐馆关键字为".$likeStoreName."在".date("Y:m:d H:i:s")."结果是".$storeInfoStr."用时".$diff);

        }else
        {

        $storeInfoStr="没有餐馆";

       $this->log->addLog("模糊查询了餐馆关键字为".$likeStoreName."在".date("Y:m:d H:i:s")."结果是".$storeInfoStr."用时".$diff);

        }


        return $storeInfoStr;


    }

    //根据id找商店所有信息
    public function get_storeInfo_byID($store_id)
    {
      
    }



  }

// new Store();


 ?>