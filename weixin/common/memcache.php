<?php 


	/**
	* memcache类
	*/
	class MemcahcheClass
	{
		private $mCon;		
		function __construct()
		{
			$this->mCon=new Memcache;
			 $this->mCon->connect("202.119.236.249",12000) or die ("could not connect");
		}


	}

	



 ?>