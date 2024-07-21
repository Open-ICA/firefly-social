<?php
require_once SYS_ROOT."model/class/note.php";
require_once SYS_ROOT."template/commons.php";
require_once SYS_ROOT."template/notes.php";

function router_notec($note){
	$sender = $note->getSenderObject();
	echo template\common\header\get();
	$usergid = $sender->getNickName()."@".$sender->getDomain();
	echo "<span style='user-select:none;'><a style='text-decoration:none;' href='/'>🏠</a> &gt; ";
	echo "<a style='text-decoration:none;color:black;' href='/@$usergid'>@$usergid</a> &gt; 帖子</span><hr/>";
	echo template\notes\display\normal\get($note);
	echo template\common\footer\get();
}
?>