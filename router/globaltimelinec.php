<?php
require_once SYS_ROOT."template/commons.php";
require_once SYS_ROOT."module/timeline.php";
require_once SYS_ROOT."template/notes.php";

function router_globaltimelinec(){
	global $_sitedb;
	echo template\common\header\get();
	echo "<span style='user-select:none;'><a style='text-decoration:none;' href='/'>🏠</a> &gt; 全网时间线</span>";
	$query = federatedTimeline::getURLArray();
	foreach($query as $i){
		$object = noteObjectProvider::getObjectByURL($i);
		if($object == null){
			continue;
		}
		echo "<hr/>".template\notes\display\normal\get($object,true);
	}
	if(count($query["data"]) == 0){
		echo "<hr/>看起来还没人发帖呢...";
	}
	echo template\common\footer\get();
}
?>