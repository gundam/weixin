
<?php
/**
  * 微信消息接口wechatCallbackapiTest
  * //自带类
  * 验证方法valid
  * 回调方法responseMsg
  * 验证token  checkSignature
  *
  * //添加类
*  解析post过来的信息getPostInfo
*  回复文本的模板textXmlTpl
*  回复图文的模板newsXmlTpl
*  给商家的文本发送模板givStoreXmlTpl
*  
*
*  命令参考
*  
* 
*  下单:+店家+食物+地址+姓名/(时间)/手机号，形成订单，插入订单表，返回订单包括订单号
*  确认+用户回复确认 订单号 回复成功
*  查询
*  推荐
*
*  编号:
*  商店1
*  用户2
*  食物3
*  订单4
  */
//var_dump($_SERVER);
//define your token

require_once('common/database.php');

require_once('./common/store.php');
 require_once('./common/hlfc.php');
 require_once('./common/food.php');
 require_once('./common/user.php');
 require_once('./common/order.php');
 require_once("./common/log.php");
define("TOKEN", "weixin");

session_start();

       



$wechatObj = new wechatCallbackapiTest();
// $wechatObj->valid();
$wechatObj->getPostInfo();
$wechatObj->responseMsg();


class wechatCallbackapiTest
{

    private $wx_fromUsername;
    private $wx_toUsername;
    private $wx_keyword;
    private $wx_createTime;
    private $wx_msgType;
    private $wx_content;//text属性
   

    //location属性
    private $wx_location_X;//纬度
    private $wx_location_Y;//精度
    private $wx_scale;
    private $wx_label;

    //图文消息回复属性
    private $wx_articleCount;
    private $wx_title;
    private $wx_discription;
    private $wx_picUrl;
    private $wx_url;

    //图片消息回复属性
    private $wx_image_PicUrl;
    //
    //星标标志
    private $wx_funcFlag;

    private $wx_postObj;//post过来的数据对象

    private $order_number;//订单号，用于确认

    private $con;


    private $log;
   
    // private $memcache;
    
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
    
    //解析数据
    public function getPostInfo()
    {       
      
        $this->log=new Log();
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {
             $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);    
             $this->wx_postObj=$postObj;
              //共有信息
              $this->wx_fromUsername=$postObj->FromUserName;//用户微信号
               $this->wx_toUsername=$postObj->ToUserName;//公众帐号微信号
               $this->wx_createTime=$postObj->CreateTime;//发送时间
           
            //文本消息获取文本信息
            if ($postObj->MsgType=="text") {
              $this->wx_msgType="text";
               
               $this->wx_content=trim($postObj->Content);//发送内容
              
               $this->log->addLog("微信号".$this->wx_fromUsername."在".date("Y-m-d H:i:s",time())."访问,内容是".$this->wx_content);
            }



            //位置消息获取位置
            if ($postObj->MsgType=='location') { 
              $this->wx_msgType="location";//设置msgtype
              $this->wx_location_X=$postObj->Location_X;//获取纬度
              $this->wx_location_Y=$postObj->Location_Y;//获取进度
              $this->wx_scale=$postObj->Scale;//获取放大倍数
              $this->wx_label=$postObj->Label;//获取地理信息


              $this->log->addLog('微信号'.$this->wx_fromUsername."在".date("Y:m:d H:i:s",time())."发送地址消息"."地址为".$this->wx_label."纬度:".$this->wx_location_X."经度:".$this->wx_location_Y);
            }

              //图片消息获取图片url
            if ($postObj->MsgType=="image") {

                $this->wx_image_PicUrl=$postObj->PicUrl;


            $this->log->addLog("微信号".$this->wx_fromUsername."在".date("Y:m:d H:i:s",time())."发送图片消息"."url为".$this->wx_image_PicUrl);

                



            }

        }



    }


    public function responseMsg()
    {
	
           $food=new Food();
           $store=new Store();
           $order=new Order();
           $user=new User();
           $sql=new Database();

          
		if($this->wx_postObj->MsgType=="text")
		{ 


          //检查是否是第一次登录
          //注册信息，填写地址，电话，姓名

          if ($this->wx_content=='h' || $this->wx_content=='H' || $this->wx_content == '帮助') {
              $this->log->addLog("微信号".$this->wx_fromUsername."键入命令 h");
            $str="详细帮助 \n 1.你可输入食物名或者商店名进行查询,会给你最相近的餐馆或者食物的信息,你可通过编号继续查询 \n 2.如果你记得食物或者餐馆编号,直接输入编号,商店编号以 1 开头,食物编号以 3 开头 \n 3.下单格式为 '下单+(商店编号)+(食物名或编号)',如 (  下单+111+31  )将以你默认地址，姓名，电话号码运送.\n 4.一句话下单格式, 下单+商店编号+食品编号+姓名+地址+号码\n5.修改运送信息的格式为  姓名+(姓名), 号码+(号码), 地址+(地址) 如 姓名+xxx.\n6.查询，你可以按照格式  查询+(订单号) 来查询你的订单";
          
            $this->test($str);
         

         }

          if ($this->wx_content === '收取') {

             $this->log->addLog('店家微信号'.$this->wx_fromUsername.'键入命令 收取');
            $storeID=$store->get_store_id($this->wx_fromUsername);
            if ($storeID=='') {
              $this->test('你不是商家，不能收取');
              
            }
            $getLatestOrder=$order->get_storeLatest_order($storeID);
            if ($getLatestOrder == '') {

               
              $this->test('没有新的订单');

            }else{


              $this->test($getLatestOrder);
            }


          }

          if ($this->wx_content == '下单') {

            $str='您要下单么～？我需要您下单的餐馆和食物的编号,如果你不知道,你可以尝试输入店名或者食物名获取，下单格式为  下单+(商店编号)+（食物编号)';
            
            $this->test($str);
            }

          if ($this->wx_content == '您好' || $this->wx_content=='你好') {

            $str='你好啊～请问你要吃什么，随便搜搜～';
            $this->test($str);
            }  


            //分析获取命令
          $command=$this->get_command();

          //获取用户id号
          //
           $getUserId=$user->get_user_id($this->wx_fromUsername);
           
        
          if ($getUserId=="false") { 
        //如果数据库没有，加入数据库
          //  echo $this->textXmlTpl("你没有注册,请先注册,请注册地址,格式为  地址:(你的地址名)");
            $userData=array("wx_user_wxNumber"=>$this->wx_fromUsername);//array数据加入数据库
            $user->add_user($userData);

          
          }
         //推荐命令
          if ($command == '推荐') {
             $this->log->addLog("微信号".$this->wx_fromUsername."键入命令 推荐");
              $dataArray=$food->get_random_food();
              $contentStr='我们向你推荐'."\n";
              for ($i=0; $i < 3; $i++) { 
               $storeName=  $store->get_store_wxName($dataArray[$i]['wx_food_belong']);
              
              $contentStr .= '编号:'.$dataArray[$i]['wx_food_id'].','.$dataArray[$i]['wx_food_name'].',属于编号'.$dataArray[$i]['wx_food_belong'].':'.$storeName."\n";

              }
              $contentStr .='请回复食物或者商店编号获得更多信息,如果你不满意我们的推荐,你可以继续 回复推荐获得另外的菜品';
            $this->test($contentStr);

            }
          if ($command=="地址") {

            $this->log->addLog("微信号".$this->wx_fromUsername."键入命令 地址");
          $orderArray=$this->analyseOrderInfo();
          $rt=$sql->update("wx_user","wx_user_address",$orderArray[0],"wx_user_wxNumber",$this->wx_fromUsername);
          $latestOrderInfo=$order->get_userLatest_orderInfo($getUserId);//获取最新订单所有信息
          $updateOrder= $order->update_order_info($latestOrderInfo['wx_order_id'],"wx_order_address",$orderArray[0]);

         
          if ($latestOrderInfo['wx_order_telphone']=='') {
            $this->test('你的订单中还没有手机号,请加入手机号,格式为 号码+(号码)');
          }

          if ($latestOrderInfo['wx_order_userName']=='') {
              $this->test("你的订单中还没有收货人姓名,请添加姓名,格式为 姓名+(姓名)");
            }
             $latestOrderInfo=$order->get_one_order($latestOrderInfo['wx_order_id']);
            $this->log->addLog('获取了id为'.$latestOrderInfo['wx_order_id'].'的订单');
            //获取更新后的信息

            $storeName=$store->get_store_wxName($latestOrderInfo['wx_order_storeId']);
            $foodName= $food->get_food_name($latestOrderInfo['wx_order_foodId']);

           $str='你的订单号是'.$latestOrderInfo['wx_order_id'].",是".$storeName.'的'.$foodName.',收货人姓名是'.$latestOrderInfo['wx_order_userName'].',地址是'.$latestOrderInfo['wx_order_address'].',电话是'.$latestOrderInfo['wx_order_telphone']."\n请回复 确认+(订单号) 来确认订单,如果你要修改地址，姓名，电话信息.请回复 h或帮助 查看.\n(你的姓名，电话，地址已经存为默认信息,下次你可以直接下单)"; 
        
           $this->test($str);
          }

        if ($command=="号码") {

          $this->log->addLog('微信号'.$this->wx_fromUsername."键入命令 号码");
          $orderArray=$this->analyseOrderInfo();
          $this->log->addLog('输入的手机号长度为'.strlen(trim($orderArray[0])));
          if(strlen(trim($orderArray[0]))!=11){

            $this->test('手机号长度不对,请重新输入  号码+(号码)');

          }
          $rt=$sql->update("wx_user","wx_user_telphone",$orderArray[0],"wx_user_wxNumber",$this->wx_fromUsername);
          
           $latestOrderInfo=$order->get_userLatest_orderInfo($getUserId);//获取最新订单所有信息
          $updateOrder= $order->update_order_info($latestOrderInfo['wx_order_id'],"wx_order_telphone",$orderArray[0]);

        
          if ($latestOrderInfo['wx_order_address']=='') {
            $this->test('你的订单中还没有地址,请加入地址,格式为 地址+(地址)');
          }

          if ($latestOrderInfo['wx_order_userName']=='') {
              $this->test("你的订单中还没有收货人姓名,请添加姓名,格式为 姓名+(姓名)");
            }

             $latestOrderInfo=$order->get_one_order($latestOrderInfo['wx_order_id']);
            $this->log->addLog('获取了id为'.$latestOrderInfo['wx_order_id'].'的订单');

            $storeName=$store->get_store_wxName($latestOrderInfo['wx_order_storeId']);
            $foodName= $food->get_food_name($latestOrderInfo['wx_order_foodId']);

           $str='你的订单号是'.$latestOrderInfo['wx_order_id'].",是".$storeName.'的'.$foodName.',收货人姓名是'.$latestOrderInfo['wx_order_userName'].',地址是'.$latestOrderInfo['wx_order_address'].',电话是'.$latestOrderInfo['wx_order_telphone']."\n请回复 确认+(订单号) 来确认订单,如果你要继续修改地址，姓名，电话信息.请回复 h或帮助 查看\n(你的姓名，电话，地址已经存为默认信息,下次你可以直接下单)"; 
        
           $this->test($str); 
        }

        if ($command=="姓名") {

          $this->log->addLog("微信号".$this->wx_fromUsername."键入命令 姓名");
          $orderArray=$this->analyseOrderInfo();
          $rt=$sql->update("wx_user","wx_user_name",$orderArray[0],"wx_user_wxNumber",$this->wx_fromUsername);
          
           $latestOrderInfo=$order->get_userLatest_orderInfo($getUserId);//获取最新订单所有信息
          $updateOrder= $order->update_order_info($latestOrderInfo['wx_order_id'],"wx_order_userName",$orderArray[0]);

          if ($latestOrderInfo['wx_order_telphone']=='') {
            $this->test('你的订单中还没有手机号,请加入手机号,格式为 号码+(号码)');
          }

          if ($latestOrderInfo['wx_order_address']=='') {
              $this->test('你的订单中还没有地址,请添加地址,格式为 地址+(地址)');
            }

            $latestOrderInfo=$order->get_one_order($latestOrderInfo['wx_order_id']);
            $this->log->addLog('获取了id为'.$latestOrderInfo['wx_order_id'].'的订单');

            $storeName=$store->get_store_wxName($latestOrderInfo['wx_order_storeId']);
            $foodName= $food->get_food_name($latestOrderInfo['wx_order_foodId']);

           $str='你的订单号是'.$latestOrderInfo['wx_order_id'].",是".$storeName.'的'.$foodName.',收货人姓名是'.$latestOrderInfo['wx_order_userName'].',地址是'.$latestOrderInfo['wx_order_address'].',电话是'.$latestOrderInfo['wx_order_telphone']."\n请回复 确认+(订单号) 来确认订单.\n(你的姓名，电话，地址已经存为默认信息,下次你可以直接下单)"; 
        
           $this->test($str);
        
      
          // $this->test("姓名注册成功,恭喜你注册成功,现在只要你输入想吃的东西或者商店,就能获取信息.更多信息请回复 h 查询～～");
        }
         
         //检查姓名
          // $nameCheck=$user->get_user_name($this->wx_fromUsername);
          
          // if ($nameCheck=="false") {
           
          //   $this->test("没有注册姓名");
          // }
       

          // $addressCheck=$user->get_user_address($this->wx_fromUsername);
         
          // if ($addressCheck=="false") {
           
          //   $this->test("没有注册地址");
          // }

          // $telphoneCheck=$user->get_user_telphone($this->wx_fromUsername);
          // if ($telphoneCheck=="false") {
            
           
          //   $this->test("没有注册号码");
          // }
          //文本消息分析(命令？？分词)
          //  
          //      命令形式

        if ($command=="下单") {

          $this->log->addLog("微信号".$this->wx_fromUsername."键入命令 下单");
        
           //获取商家id，食品id，姓名,地址，电话
           $orderArray=$this->analyseOrderInfo();
           
           // public function add_order($wx_order_storeId,$wx_order_foodId,$wx_order_userId,$wx_order_address,wx_order_telphone)
           // 如果分裂开来的数组长度为5,检查一遍check函数
           if (count($orderArray)==5) 
           {  
             $wx_order_storeId=$orderArray[0];

             $checkstore=$sql->checkFun("wx_store","wx_store_id",$orderArray[0]);//检查店名id
             
             if ($checkstore==false) {
               
                $this->test("店名id错误，请检查");
             }else{

              //获取店名
              $order_store_name_toUser=$checkstore['wx_store_wxName'];
             }

             $foodInfo=$food->get_one_food($orderArray[1]);

             $wx_order_foodId =$orderArray[1];

             //$checkfood=$sql->checkFun("wx_food","wx_food_id",$orderArray[1]);//检查食物名id
             
             if ($foodInfo==false ||  $foodInfo['wx_food_belong'] != $orderArray[0]) {
                
                $this->test("食物id错误，请检查");
             }else{

              $order_food_name_toUser= $foodInfo['wx_food_name'];

             }

             $wx_order_user=$orderArray[2];//通过微信号查找

             $wx_order_userId=$user->get_user_id($this->wx_fromUsername);
            

             // $wx_order_address=$orderArray[3];地址

             // $wx_order_telphone=$orderArray[4];电话

             $rt=$order->add_order($wx_order_storeId,$wx_order_userId,$wx_order_foodId,$orderArray[2],$orderArray[3],$orderArray[4],"0");

             if ($rt==false) {
            
               $this->test('发生错误');
             }else{


              $contentStr='你的订单号是'.$rt.',订单是'.$order_store_name_toUser.'的'.$order_food_name_toUser.',地址是'.$orderArray[3].',姓名是'.$orderArray[2].',电话是'.$orderArray[4].',请回复 确认+(订单号),如果你要修改地址，姓名，电话信息.请回复 h或帮助 查看\n(你的姓名，电话，地址已经存为默认信息,下次你可以直接下单)';
              
               $this->test($contentStr);
             }
           }
            

            if (count($orderArray)==2) {
             
             
                $wx_order_storeId=$orderArray[0];
             $checkstore=$sql->checkFun("wx_store","wx_store_id",$orderArray[0]);
             if ($checkstore==false) {
              
                $this->test('店名id错误，请检查');
             }else{

              //获取店名
              $order_store_name_toUser=$checkstore['wx_store_wxName'];
            
             }

             $foodInfo=$food->get_one_food($orderArray[1]);
             $wx_order_foodId =$orderArray[1];

             //$checkfood=$sql->checkFun('wx_food','wx_food_id',$orderArray[1]);
            
             if ($foodInfo==false ||  $foodInfo['wx_food_belong'] != $orderArray[0]) {
               
                $this->test('食物id错误或者食物不属于该店,请检查');
             }else{

              $order_food_name_toUser= $foodInfo['wx_food_name'];
              
             }

            
               $userInfo= $user->get_user_all_info($this->wx_fromUsername);
                //获取所有信息
             
              $wx_order_userId=$userInfo['wx_user_id'];
              $wx_order_user=$userInfo['wx_user_name'];
              $wx_order_address=$userInfo['wx_user_address'];
              $wx_order_telphone= $userInfo['wx_user_telphone'];

              $rt= $order->add_order($wx_order_storeId,$userInfo['wx_user_id'],$wx_order_foodId,$userInfo['wx_user_name'],$userInfo['wx_user_address'],$userInfo['wx_user_telphone'],'0');

               if ($rt==false) {
               
               $this->test('发生错误');
             }else{
                  if ($userInfo['wx_user_name']=='') {
                $this->test('没有订单姓名,请回复 姓名+(姓名)  的格式加入');
              }
                //检查姓名
                //
                 if ($userInfo['wx_user_address']=='') {
                $this->test('没有订单地址,请回复 地址+(地址)  的格式加入');
              }
              //检查地址
              //
              if ($userInfo['wx_user_telphone']=='') {
                $this->test('没有订单电话,请回复 电话+(号码)  的格式加入');
              }
              //检查号码
              //
              $contentStr="你的订单号是".$rt.',是'.$order_store_name_toUser.'的'.$order_food_name_toUser.',地址是'.$wx_order_address.',姓名是'.$wx_order_user.',电话是'.$wx_order_telphone."\n请回复 确认+(订单号),如果你要修改地址，姓名，电话信息.请回复 h或帮助 查看\n(你的姓名，电话，地址已经存为默认信息,下次你可以直接下单)";
              
               $this->test($contentStr);
             }
            }

        
        }

        if ($command=="确认") {
          $contentStr="选择确认命令";
          $this->log->addLog("微信号".$this->wx_fromUsername."键入命令 确认");
          $orderArray = $this->analyseOrderInfo();
          
          $orderInfo=$order->get_one_order($orderArray[0]);
          //检查订单时间差，判断有效
          $diff=time()-strtotime($orderInfo['wx_order_time']);
          if ($diff>(60*60*3)) {
            $this->test('该订单已经失效');
          }
          $this->log->addLog("该订单时间差".time().'-'.strtotime($orderInfo['wx_order_time']).'='.$diff.'秒');
          //$rt=$sql->checkFun("wx_order","wx_order_id",$orderArray[0]);
          $userId=$user->get_user_id($this->wx_fromUsername);
          //订单用户验证
          // $checkOrderUser=$sql->checkFun("wx_order","wx_order_userId",$userId);
          $this->log->addLog("确定订单".$orderArray[0].'验证信息订单存在'.$rt.'用户是否正确'.$checkOrderUser);
          
          if ($orderInfo==false) {
           
            $this->test("订单号错误");
          }else{

             if($userId==$orderInfo['wx_order_userId']){

               $order->create_order_status("1",$orderArray[0]);
            $storeWxname=$store->get_store_wxName($orderInfo['wx_order_storeId']);//获取店名
            $foodName=$food->get_food_name($orderInfo['wx_order_foodId']);//获取食物名
            $this->test("订单".$orderInfo['wx_order_id'].','.$storeWxname.'的'.$foodName."姓名是".$orderInfo['wx_order_userName'].',地址是'.$orderInfo['wx_order_address'].',电话是'.$orderInfo['wx_order_telphone'].",确认成功");


             } else{

              $this->test("不是你的订单");

             }
          


          }

        }
          //查询订单
          if ($command=="查询") {

             $this->log->addLog("微信号".$this->wx_fromUsername."键入命令 查询");
            
            $orderArray=$this->analyseOrderInfo();
            $userId=$user->get_user_id($this->wx_fromUsername);
           

            if ($orderArray[0]=='') {

              // $this->test('');
              $this->log->addLog('查询内容为空');

              $data=$order->get_latestThreeOrders($userId);
              if ($data == '') {
                $this->log->addLog('查询不到你以往的订单');
                $this->test("查询不到你以往的订单");
              }
              $echoData ='你的以往订单是'."\n";
              for ($i=0; $i < count($data); $i++) { 
                $storeName=$store->get_store_wxName($data[$i]['wx_order_storeId']);
                $foodName=$food->get_food_name($data[$i]['wx_order_foodId']);

                $echoData .= '订单号'.$data[$i]['wx_order_id'].'是'.$storeName.'的'.$foodName.'在'.$data[$i]['wx_order_time']."\n";
              }

              $this->test($echoData);

            }

            $orderInfo=$order->get_one_order($orderArray[0]);
            // $rt=$sql->checkFun("wx_order","wx_order_id",$orderArray[0]);
            
          if ($orderInfo==false  || $userId != $orderInfo['wx_order_userId']) {
           
            $this->test("订单号错误或者不是你的订单");
          }else{

              if ($orderInfo['wx_order_valid']==0) {

                $order_valid="没有确认";
              }else{

                $order_valid="已经确认";
              }
              
              $storeName=$store->get_store_wxName($orderInfo['wx_order_storeId']);
              $foodName=$food->get_food_name($orderInfo['wx_order_foodId']);

             $this->test('订单号为'.$orderInfo['wx_order_id'].','.$storeName.'的'.$foodName.',收货人是'.$orderInfo['wx_order_userName'].',地址是'.$orderInfo['wx_order_address'].',电话是'.$orderInfo['wx_order_telphone'].$order_valid);

          }
          
        }


         //ID搜寻
          if(is_numeric($this->wx_content)){

            $searchKey=substr($this->wx_content, 0,1);
            if ($searchKey=="1") {
              # code...商店具体的食物列表
             $foodList=$store->get_food_byStore($this->wx_content);
             for ($i=0; $i < count($foodList); $i++) { 
                $foodStr .=$foodList[$i]."\n"; 
             }
              $this->test($foodStr."\n你可以使用食物前面的  编号  获取更多信息 或者下单,下单格式: 下单+商店编号+食物编号");
            }
            if ($searchKey=="3") {
            
              //列出食物信息
             $foodName=$food->get_food_bySearch($this->wx_content);
            

             $this->test($foodName."\n你可以使用食物前面的  编号  获取更多信息 或者下单,下单格式: 下单+商店编号+食物编号");
            }

            $this->test("搜索错误");
            
          }
         
     
      //分词部分
       $test=new Hlfc($this->wx_content); 
        $temp=json_decode($test->outoutValue(),true);
       $a=$temp['response']['Result']['Resource']['AnalyzeResult']['Annotations']['Item'];
       for ($i=0; $i < count($a); $i++) { 
         $tempStr .=$a[$i]['@attributes']['ExtSign'].$a[$i]['@attributes']['Text']."    ";
         if ($a[$i]['@attributes']['ExtSign']=="1048576") {
           $fcKeyword[] .=$a[$i]['@attributes']['Text'];//获取名称关键词
         }
       }

        //只有一个关键词的情况下
       if(trim($tempStr)==""){

        $tempStr=$a['@attributes']['ExtSign'].$a['@attributes']['Text'];
        if ($a['@attributes']['ExtSign']=="1048576") {
           $fcKeyword[] .=$a['@attributes']['Text'];//获取名称关键词
         }
       }
      
      

         $this->log->addLog("关键名词个数为".count($fcKeyword));

      if (count($fcKeyword)==0) {

         
          $storeName=$store->get_store_bySearch($this->wx_content);
          $foodName=$food->get_food_bySearch($this->wx_content);

          $this->test($foodName.$storeName."\n你可以使用餐馆或者食物前面的  编号  获取更多信息或者  下单,下单格式: 下单+商店编号+食物编号");

      }
      //没有名词关键词的时候取整词搜索
      //有名词关键词的时候逐个寻找并输出
   
      for ($i=0; $i < count($fcKeyword); $i++) { 

         $storeName=$store->get_store_bySearch($fcKeyword[$i]);
        
         if ($storeName!="没有餐馆") {
          
            $storeStrToUser .=$storeName.'  ';
         }else{

          $storeStrToUser="没有餐馆\n";

         }

         $foodName=$food->get_food_bySearch($fcKeyword[$i]);
         if ($foodName!="没有食物") {
           # code...
          $foodStrToUser .=$foodName.'   ';

         }else{

          $foodStrToUser="没有食物\n";
         }

      }
 
          $this->test($storeStrToUser.$foodStrToUser."\n你可以使用餐馆或者食物前面的  编号  获取更多信息或者  下单,下单格式: 下单+商店编号+食物编号");


//分词部分结束

     
		}
		else{

       if ($this->wx_postObj->MsgType=="location") {
            
            $rangeArray=$this->calculate_range($this->wx_location_X,$this->wx_location_Y,1);
   
             $store=new Store();
             $storeInfo=$store->get_srore_byLocation($rangeArray['maxLat'],$rangeArray['minLat'],$rangeArray['maxLng'],$rangeArray['minLng']);
              $temp;
              foreach ($storeInfo as $key => $value) {
                $temp .="$value[0],";
              }
             
              
             $contentStr=$this->wx_location_X.'   '.$this->wx_location_Y.'      '.$this->wx_label.'  转化后:   '.$rangeArray['maxLat'].'   '.$rangeArray[
               'minLat'].'    '.$rangeArray['maxLng'].'      '.$rangeArray['minLng'];

               $this->test($contentStr);



       }

            if ($this->wx_postObj->MsgType=="image") {

            }

        }

    }
		
	private function checkSignature()
	{
          $signature = $_GET["signature"];
          $timestamp = $_GET["timestamp"];
          $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}


    // 文本消息回复模板
    public function textXmlTpl($contentStr)
    {
        
                $time = time();
                $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";             
     
                    $msgType = "text";
                          
                    $resultStr = sprintf($textTpl, $this->wx_fromUsername, $this->wx_toUsername, $time, $msgType, $contentStr);
                    return $resultStr;
             
         
    }

    

    // // 图文回复模板
    public function newsXmlTpl($title,$description,$picurl,$url)
    {

        # code...
        $time=time();
        $newsTpl="  <xml>
          <ToUserName><![CDATA[%s]]></ToUserName>
           <FromUserName><![CDATA[%s]]></FromUserName>
           <CreateTime>%s</CreateTime>
         <MsgType><![CDATA[%s]]></MsgType>
         <Content><![CDATA[%s]]></Content>
     <ArticleCount>1</ArticleCount>
     <Articles>
     <item>
     <Title><![CDATA[%s]]></Title>
     <Discription><![CDATA[%s]]></Discription>
     <PicUrl><![CDATA[%s]]></PicUrl>
     <Url><![CDATA[%s]]></Url>
     </item>
     
     
     </Articles>
     <FuncFlag>1</FuncFlag>
    </xml> ";

  
        $MsgType="news";    
       $resultStr=sprintf($newsTpl,$this->wx_fromUsername,$this->wx_toUsername,$time,$MsgType,$this->wx_content,$title,$description,$picurl,$url);

        return $resultStr;
        
    }


    //发送给商家的文本模板
    public function givStoreXmlTpl($contentStr,$toStoreName)
    {
      // $time=time();
      $time = time();
                $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";             
      
                    $msgType = "text";
                       
                    $resultStr = sprintf($textTpl,$toStoreName ,$this->wx_toUsername , $time, $msgType, $contentStr);
                   // $resultStr = sprintf($textTpl, $time, $msgType, $contentStr);
                    return $resultStr;

    }
     


    // 计算经纬度范围
    // 
    public function calculate_range($myLat,$myLng,$kilo)
    {
          $range = 180 / pi() * $kilo / 6372.797;     //里面的 1 就代表搜索 1km 之内，单位km  
         $lngR = $range / cos($myLat * pi() / 180);  
          $maxLat=bcadd($myLat,$range,15);  
         $minLat=bcsub($myLat, $range,15);
         $maxLng = bcadd($myLng, $lngR,15);//最大经度  
            $minLng = bcsub($myLng, $lngR,15); 
            $rangeArray=array("maxLat"=>$maxLat,"minLat"=>$minLat,"maxLng"=>$maxLng,"minLng"=>$minLng,"range"=>$range,"lngR"=>$lngR);
            return $rangeArray;
    }


    // 获取命令
    // 
    public function get_command()
    {
      # code...
      $str=$this->wx_content;
      $command=substr($str, 0,6);
      return $command;
    }

    //分析下单数据 （下单:店家编号（？名称），食物编号（？名称））？默认（命令）：地址，姓名，手机号
    public function analyseOrderInfo()
    {
      // code...分割使用explode函数,返回一个函数
      $str=$this->wx_content;
     $orderDataArray= explode('+', substr($str, 7));
      return $orderDataArray;

    }

      //消息输出
    public function test($value)
    {
      echo $this->textXmlTpl($value);
      $this->log->addLog("回复消息   ".$value);
      exit();
    }


}

?>
