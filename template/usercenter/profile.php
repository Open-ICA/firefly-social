<?php
namespace template\usercenter\profilepage;
require_once SYS_ROOT."model/class/user.php";

function get(\model_auth_user $user,string $apibase):string{
	$name = addslashes($user->getDisplayName());
	$header = addslashes($user->getHeadImageURI());
	$gid = "@".$user->getNickName()."@".$user->getDomain();
	return <<<EOF
<h2 style="margin:0px;">更改用户信息</h2>
<form method="post" action="$apibase/reprofile">
	<table>
		<tr>
			<td>账户名：</td><td>$gid</td>
		</tr>
		<tr>
			<td>昵称：</td><td><input name="displayname" value="$name" style="width:310px;padding:2px;font-size:0.9rem"/></td>
		</tr>
		<tr>
			<td>头像URL：</td><td><input name="headeruri" value="$header" style="width:310px;padding:2px;font-size:0.9rem"/></td>
		</tr>
		<tr>
			<td></td><td><input type="submit" value="确认"/></td>
		</tr>
	</table>
</form>
EOF;
}