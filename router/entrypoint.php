<?php
require_once SYS_ROOT."config/config_main.php";
require_once SYS_ROOT."module/main_database.php";
require_once SYS_ROOT."model/class/user.php";
require_once SYS_ROOT."model/utils/curl.php";
require_once SYS_ROOT."model/class/note.php";

function router_entrypoint(){
	global $_config;
	$headers = getallheaders();
	// Index page display
	if(SYS_REQPATH == "/"){
		require_once SYS_ROOT."router/indexc.php";
		router_indexc();
		die();
	}
	// Timeline page display
	if(SYS_REQPATH == "/local"){
		require_once SYS_ROOT."router/localtimelinec.php";
		router_localtimelinec();
		die();
	}
	if(SYS_REQPATH == "/network"){
		require_once SYS_ROOT."router/globaltimelinec.php";
		router_globaltimelinec();
		die();
	}
	// Webfinger
	if(SYS_REQPATH == "/.well-known/webfinger"){
		require_once SYS_ROOT."router/webfinger.php";
		router_webfinger();
		die();
	}
	// User profile api
	if(preg_match("/^\/user\/([1-9][0-9]*)$/",SYS_REQPATH,$ix)){
		$user = userObjectProvider::getUserByLocalUID((int)($ix[1]));
		if($user == null){
			http_response_code(404);
			die();
		}
		if($headers["Accept"] == "application/activity+json"){
			header("Content-Type: application/activity+json");
			die(json_encode($user->getAPProfile()));
		}
		header("Location: /@".$user->getNickName());
		die();
	}
	// User profile display
	if(preg_match("/^\/@([A-Za-z0-9_\-\.]+)(@([A-Za-z0-9_\-\.]+))?$/",SYS_REQPATH,$ix)){
		$user = userObjectProvider::getUserByHandle($ix[1],isset($ix[3])?$ix[3]:$_config["site"]["domain"]);
		if($user == null){
			http_response_code(404);
			die();
		}
		if($headers["Accept"] == "application/activity+json"){
			header("Content-Type: application/activity+json");
			die(json_encode($user->getAPProfile()));
		}
		require_once SYS_ROOT."router/profilec.php";
		router_profilec($user);
		die();
	}
	// User Outbox api
	if(preg_match("/^\/user\/([1-9][0-9]*)\/outbox$/",SYS_REQPATH,$ix)){
		$user = userObjectProvider::getUserByLocalUID((int)($ix[1]));
		if($user == null){
			http_response_code(404);
			die();
		}
		header("Content-Type: application/activity+json");
		die(json_encode($user->getAPOutbox()));
	}
	// Notes api
	if(preg_match("/^\/status\/([1-9][0-9]*)$/",SYS_REQPATH,$ix)){
		$note = noteObjectProvider::getObjectByLocalTid((int)($ix[1]));
		if($note == null){
			http_response_code(404);
			die();
		}
		if($headers["Accept"] == "application/activity+json"){
			header("Content-Type: application/activity+json");
			die(json_encode($note->getAPObject()));
		}
		$purl = preg_replace("~^https?:\/\/~","",$note->getURI());
		header("Location: /notes/".str_replace(array("=","+","/"),array("","-","*"),base64_encode($purl)));
	}
	// Notes display
	if(preg_match("/^\/notes\/([A-Za-z0-9\*\-]+)$/",SYS_REQPATH,$ix)){
		$url = "https://".base64_decode(str_replace(array("-","*"),array("+","/"),$ix[1]));
		$note = noteObjectProvider::getObjectByURL($url);
		if($headers["Accept"] == "application/activity+json"){
			header("Content-Type: application/activity+json");
			die(json_encode($note->getAPObject()));
		}
		require_once SYS_ROOT."router/notec.php";
		router_notec($note);
		die();
	}
}
?>