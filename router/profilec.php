<?php
require_once SYS_ROOT."model/class/user.php";
require_once SYS_ROOT."template/commons.php";
require_once SYS_ROOT."template/profile.php";
require_once SYS_ROOT."template/notes.php";

function router_profilec($user){
	echo template\common\header\get();
	$usergid = $user->getNickName()."@".$user->getDomain();
	echo "<span style='user-select:none;'><a style='text-decoration:none;' href='/'>🏠</a>  &gt; @$usergid</span><hr/>";
	echo template\profile\normal\get($user);
	$outbox = $user->getOutbox();
	foreach($outbox as $i){
		echo "<hr/>";
		echo template\notes\display\normal\get($i,true);
	}
	if(count($outbox) == 0){
		echo "<hr/>啥也没有";
	}
	echo template\common\footer\get();
}
?>