<?php
require_once SYS_ROOT."template/commons.php";

function router_indexc(){
	echo template\common\header\get();
	echo "<span style='user-select:none;'>🏠 &gt; 首页</span><hr/>";
	echo "欢迎来到Firefly！<br/>这是碧蓝航线官方的百合讨论网络（去中心化的！）<br/>基于W3C的ActivityPub协议。<br/>";
	echo "<div style='margin-top:3px;'>功能：<a class='normilink' href='/local'>本站时间线</a> ";
	echo "<a class='normilink' href='/network'>全网时间线</a> ";
	echo "<a class='normilink' href='/user'>用户中心</a></div>";
	echo template\common\footer\get();
}
?>