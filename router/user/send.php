<?php
require_once SYS_ROOT."model/class/user.php";
require_once SYS_ROOT."template/usercenter/frame.php";

function router_user_send(\model_auth_user $user,string $apibase){
	$irc = <<<EOF
<h2 style='margin:0px'>发帖</h2>
<form method="post" action="$apibase/send">
	标题：<input style="width:320px;" name="title" placeholder="无标题"/>
	<select name="method">
		<option value="plain">纯文本</option>
		<option value="markdown">Markdown</option>
		<option value="html">HTML</option>
	</select>
	<input value="发布" type="submit"/><br/>
	<textarea name="content" style="width:calc(100% - 5px);margin-top:3px;height:320px;resize:vertical"></textarea>
</form>
EOF;
	echo template\usercenter\frame\get($irc,$user,$apibase);
}