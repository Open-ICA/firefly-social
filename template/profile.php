<?php
namespace template\profile\normal;
require_once SYS_ROOT."model/class/user.php";

function get(\model_profile $user){
	$header_url = $user->getHeadImageURI();
	$usergid = $user->getNickName()."@".$user->getDomain();
	$displayname = $user->getDisplayName();
	return <<<EOF
<div style="position:relative;clear:all;vertical-align:text-top">
	<div style="height:100px;width:100px;display:inline-block;vertical-align:text-top">
		<div style="position:absolute;top:1px;width:100px;height:100px;">
			<img src="$header_url" style="width:100px;height:100px;border:1px solid black;border-radius:100px;"/>
		</div>
	</div>
	<div style="padding-left:3px;display:inline-block;vertical-align:text-top">
		<h2 style="display:inline;">$displayname</h2><br/>
		@$usergid
	</div>
</div>
EOF;
}
?>