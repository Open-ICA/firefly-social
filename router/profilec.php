<?php
require_once SYS_ROOT."model/class/user.php";
require_once SYS_ROOT."template/commons.php";
require_once SYS_ROOT."template/profile.php";
require_once SYS_ROOT."template/notes.php";

function router_profilec($user){
	global $_config;
	echo template\common\header\get();
	$usergid = $user->getNickName()."@".$user->getDomain();
	echo "<span style='user-select:none;'><a style='text-decoration:none;' href='/'>🏠</a>  &gt; @$usergid</span><hr/>";
	echo template\profile\normal\get($user);
	$outbox = $user->getOutbox();
	foreach($outbox as $i){
		if($i == null){
			continue;
		}
		echo "<hr/>";
		echo template\notes\display\normal\get($i,true);
	}
	if(count($outbox) == 0 && $user->getDomain() == $_config["site"]["domain"]){
		echo "<hr/>啥也没有";
	}
	if($user->getDomain() != $_config["site"]["domain"]){
		echo "<hr/>该用户位于另一站点，显示信息可能不完整。<a class='normilink' href='".$user->getHomepage()."'>跳转到原页面</a>";
	}
	echo template\common\footer\get();
}
?>