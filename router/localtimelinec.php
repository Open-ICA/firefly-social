<?php
require_once SYS_ROOT."template/commons.php";
require_once SYS_ROOT."module/main_database.php";
require_once SYS_ROOT."template/notes.php";

function router_localtimelinec(){
	global $_sitedb;
	echo template\common\header\get();
	echo "<span style='user-select:none;'><a style='text-decoration:none;' href='/'>🏠</a> &gt; 本站时间线</span>";
	$query = $_sitedb->query("SELECT tid FROM local_notes ORDER BY sendtimestamp DESC LIMIT 63");
	foreach($query["data"] as $i){
		echo "<hr/>".template\notes\display\normal\get(noteObjectProvider::getObjectByLocalTid($i["tid"]),true);
	}
	if(count($query["data"]) == 0){
		echo "<hr/>看起来还没人发帖呢...";
	}
	echo template\common\footer\get();
}
?>