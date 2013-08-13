<?php  
$redis = new redis();  
$redis->connect('127.0.0.1', 6379);  

//$nextUserId = $redis->incr(1);

/*$redis->set("user:1:username","jack"); 
$redis->set("user:username:uid","1"); 
$redis->set("user:1:password","jack");
$redis->set("user:1:authorId","2");*/
//$redis->get("user:1:username");
//$username = $redis->get('user:$nextUserId:username');
//$redis->set("user:$username:id",$nextUserId);



for ($i= -50;  $i<50;$i++){
	$redis->delete("user:$i:username");
}

?>