<?php
require_once SYS_ROOT."model/class/user.php";
require_once SYS_ROOT."template/usercenter/frame.php";
require_once SYS_ROOT."template/notes.php";

function router_user_tiezic(\model_auth_user $user,string $apibase){
	global $_config;
	$irc = "<h2 style='margin:0px'>帖子管理</h2>";
	$outbox = $user->getOutbox();
	foreach($outbox as $i){
		if($i == null){
			continue;
		}
		$irc .= "<hr/>";
		$irc .= template\notes\display\normal\get($i,true);
		$tid = $i->getTid();
		$irc .= <<<EOF
<hr/>
<a href="$apibase/delnote?tid=$tid"><button>删除</button></a>
EOF;
	}
	if(count($outbox) == 0){
		$irc .= "<hr/>啥也没有";
	}
	echo template\usercenter\frame\get($irc,$user,$apibase);
}
?>