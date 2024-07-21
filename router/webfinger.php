<?php
require_once SYS_ROOT."config/config_main.php";
require_once SYS_ROOT."module/main_database.php";
require_once SYS_ROOT."model/class/user.php";
require_once SYS_ROOT."model/utils/curl.php";
require_once SYS_ROOT."model/class/note.php";

function router_webfinger(){
	header("Content-Type: application/jrd+json");
	global $_config;
	if(!isset($_GET["resource"])){
		http_response_code(404);
		die();
	}elseif(preg_match("/^acct:([A-Za-z0-9\-\._]+)@([A-Za-z0-9\-\._]+)$/",$_GET["resource"],$idx)){
		if($idx[2] != $_config["site"]["domain"]){
			http_response_code(404);
			die();
		}
		$nickname = $idx[1];
		$userobj = userObjectProvider::getUserByLocalNickname($nickname);
		if($userobj == null){
			http_response_code(404);
			die();
		}
		die(json_encode([
			"subject"=>$_GET["resource"],
			"aliases"=>[],
			"links"=>[
				[
					"rel"=> "http://webfinger.net/rel/profile-page",
					"type"=> "text/html",
					"href"=> $userobj->getHomepage()
				],
				[
					"rel"=> "self",
					"type"=> "application/activity+json",
					"href"=> $userobj->getHomepage()
				]
			]
		]));
	}else{
		http_response_code(404);
		die();
	}
}
?>