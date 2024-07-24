<?php
namespace template\usercenter\frame;
require_once SYS_ROOT."model/class/user.php";

function get(string $innerHTML,\model_auth_user $user,string $apibase){
	$gid = "@".$user->getNickName()."@".$user->getDomain();
	return <<<EOF
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Firefly User Center</title>
		<style>
			a.clpdec3s{
				padding:2px;
				display:block;
				width:calc(100% - 4px);
				color:black;
				margin-top:2px;
				margin-bottom:2px;
				text-decoration:none;
			}
			a.clpdec3s:hover{
				padding:3px;
				background-color:blue;
				color:white;
				width:calc(100% - 6px);
				border-radius:2px;
				margin-top:3px;
				margin-bottom:3px;
			}
			hr.hrbloi{
				color:white;
				border:1px solid white;
				margin-top:3px;
				margin-bottom:2px;
			}
		</style>
		<link rel="stylesheet" type="text/css" href="/static/css/library.css"/>
	</head>
	<body style="padding:0px;margin:0px;">
		<div style="padding:2px;position:fixed;top:0px;left:0px;height:50px;width:100%;font-size:34px;background-color:#44aacc;colorblack;user-select:none;">
			Firefly 用户中心
		</div>
		<div style="padding:4px;position:fixed;top:54px;left:0px;height:calc(100% - 54px);width:186px;background-color:#66ccff;color:black;user-select:none;">
			<a class="clpdec3s" href="/user">主页</a>
			<a class="clpdec3s" href="/user/profile">个人资料</a>
			<a class="clpdec3s" href="/user/status">帖子管理</a>
			<hr class="hrbloi"/>
			<a class="clpdec3s" href="/">返回前台</a>
			<a class="clpdec3s" href="/$gid">个人主页</a>
			<a class="clpdec3s" href="$apibase/unlogin">退出登录</a>
		</div>
		<div style="margin-top:54px;margin-left:194px;padding:6px;">$innerHTML</div>
	</body>
</html>
EOF;
}
?>