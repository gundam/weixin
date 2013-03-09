<?php 



$mem = new Memcache;
$mem->connect("202.119.236.249", 12000);
$mem->set('key', 'This is a test!', 0, 60);
$val = $mem->get('key');
echo $val;


 ?>