<?php
namespace template\common\loginpage;
function get(string $apibase){
	return <<<EOF
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Login</title>
		<style>
			#main{
				position:fixed;
				top:calc(50% - 150px);
				height:300px;
				background-color:white;
				border: 1px solid black;
			}
			@media screen and (max-width: 368px) {
				#main{
					width:calc(100% - 8px);
					left:3px;
				}
			}
			@media screen and (min-width: 369px) {
				#main{
					width:360px;
					left:calc(50% - 181px);
				}
			}
			#main>div#cin{
				padding:5px;
			}
		</style>
	</head>
	<body style="background-color:#66ccff;">
		<div id="main">
			<div id="cin">
				<a href="/"><img alt="icon" title="icon" src="/static/image/icon-title.png" style="width:calc(100% - 40px);display:block;user-select:none;padding:20px;padding-bottom:2px"/></a>
				<h2 style="user-select:none;text-align:center;padding:3px">登录</h2>
				<form method="post" action="$apibase/login">
					<input style="margin-bottom:5px;width:calc(100% - 10px);padding:3px;" placeholder="用户名" name="username"/>
					<input style="margin-bottom:10px;width:calc(100% - 10px);padding:3px;" placeholder="密码" type="password" name="password"/>
					<div style="position:absolute;bottom:10px;left:0px;width:100%;text-align:center;">
						<input style="padding:3px 10px;" value="登录" type="submit"/>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>
EOF;
}