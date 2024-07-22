<?php
require_once SYS_ROOT."config/config_main.php";
require_once SYS_ROOT."module/main_database.php";

class federatedTimeline{
	static function checkURLExists(string $url):bool{
		global $_sitedb;
		$result = $_sitedb->query("SELECT id FROM timeline_cache WHERE url=\"".addslashes($url)."\"");
		return count($result["data"])>0;
	}
	static function addRow(string $url,int $sendtime):bool{
		global $_sitedb;
		if(federatedTimeline::checkURLExists($url)){
			return true;
		}
		$result = $_sitedb->query("INSERT INTO timeline_cache (url,sendtimestamp) VALUES (\"".addslashes($url)."\",$sendtime)");
		return $result["status"]==1;
	}
	static function deleteRow(string $url):bool{
		global $_sitedb;
		$result = $_sitedb->query("DELETE id FROM timeline_cache WHERE url=\"".addslashes($url)."\"");
		return $result["status"]==1;
	}
	static function getURLArray(int $num=55):array{
		global $_sitedb;
		$result = $_sitedb->query("SELECT id,url FROM timeline_cache ORDER BY sendtimestamp DESC LIMIT $num");
		$array1 = [];
		foreach($result["data"] as $c){
			$array1[] = $c["url"];
		}
		return $array1;
	}
}