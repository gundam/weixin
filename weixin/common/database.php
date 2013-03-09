<?php 
   
/**
* 	数据路接口
*   1.连接数据库
*   2.插入
*   3.删除
*   4.更新
*   5.查询
*   
*/
header("Content-Type:text/html;charset=utf-8");
require_once 'log.php';
class Database 
{
	private $host='localhost';
	private $username='root';
	private $password='861120';
  private $database='weixin';
  private $pconnect=0;
  private $con;
  private $result;

  private $feilds;
  private $values;

  private $log;

	//构造函数.连接数据库	
  public function Database()
  {
  
  	$this->connect($this->host,$this->username,$this->password,$this->database,$this->pconnect);
    $this->log= new Log();

  }

  public function connect($host,$username,$password,$database,$pconnect)
  {
  	
    #判断持久连接
    if ($pconnect===0) {
       # code...
       $this->con=mysql_connect($host,$username,$password);
       if (!$this->con) {
         // code...
         $this->result="connectfaild";
       }
       else{
        mysql_select_db($database);
        mysql_set_charset("utf-8");
        $this->result="connectsuccess";

       }
       
     }
     else{
        $this->con=mysql_pconnect($host,$username,$password);
        if (!$this->con) {
          # code...
          $this->result="connectfaild";
        }else{
          mysql_select_db($database);
          mysql_set_charset("utf-8");
          $this->result="pconnectsuccess";

        }




     } 
  }

  public function insert($table,$data)
  { 

    
  	foreach ($data as $key => $value) {
      $this->feilds .="$key,";
      $this->values .="'$value',";
    }

    $feilds=substr($this->feilds, 0,-1);
    
    $values=substr($this->values, 0,-1);
    $this->log->addLog('数据库操作  '."insert into $table ($feilds) values ($values)");
    $sql=$this->query("insert into $table ($feilds) values ($values)");


    if ($sql) {
      return $this->result=true;
    }else{

      return $this->result=true;
    }


  }

  //select一条记录
  //
  //返回值 一条数据库记录 或者 false
  public function select($table,$key,$value)
  {

    $a=microtime(true);
    $str="select * from $table where $key = '$value'";

    $rt=$this->query($str);

    $diff=microtime(true)-$a;
    if($temp=mysql_fetch_array($rt)){

      $data=$temp;

    }else{

      $data=false;
    }
    $this->log->addLog("数据库操作".$str.'使用Database类select方法用时'.$diff.'结果为'.$data);
    return $data;
  }

  public function delete($table,$data)
  {
  	
  }

  public function update($table,$key,$value,$key1,$value1)
  {
	  $str="update $table set $key = '$value' where $key1 = '$value1' ";
    $rt=$this->query($str);
    return $str;
  }

  public function query($str)
  { 
    $query=mysql_query($str);
    

    return $query;
	   
  }

  public function serchStore_by_location($minLng,$maxLng,$minLat,$maxLat)
  {
    //范围sql语句
    //SELECT * FROM `wx_store`   WHERE wx_store_longitude between '0' and '1000' and wx_store_latitude between '0' and  '1000'
   
   $str="select wx_store_wxName from wx_store where wx_store_longitude between '1' and '1111' and wx_store_latitude between '1' and  '1111'";
   // $str="select wx_store_wxName from wx_store where wx_store_longitude between '$minLng' and '$maxLng' and wx_store_latitude between '$minLat' and  '$maxLat'";

    $Rt=$this->query($str);
    $store_name[]="";
    while ($temp=mysql_fetch_array($Rt)) {
      $store_name[]=$temp;
    }
    return $store_name;
  }
  //获取记录总数  
  public function get_total_number($table)
  {
    $str="select * from $table";
    $rt=$this->query($str);
    return mysql_num_rows($rt);


  }

  //检查函数，对下单资料进行检查
  public function checkFun($table,$key,$value)
  {
    $str="select * from $table where $key = '$value'";

    $this->log->addLog('数据库操作'.$str);
    $rt=$this->query($str);
    if (mysql_num_rows($rt)==1) {
      return mysql_fetch_array($rt);
    }else{

      return false;
    }
  }

}

 ?>