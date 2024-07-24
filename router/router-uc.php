<?php
require_once SYS_ROOT."model/class/user.php";
require_once SYS_ROOT."module/main_database.php";

function ucrouter(){
	global $_config;
	global $_sitedb;
	session_start();
	$session_nonce = isset($_SESSION["nonce"])?$_SESSION["nonce"]:0;
	$apibase = "/user/api/".md5($session_nonce.":".session_id());
	$user = null;
	if(isset($_SESSION["uid"]) && isset($_SESSION["name"]) && isset($_SESSION["ucode"])){
		$user = userObjectProvider::witUserSession($_SESSION["uid"],$_SESSION["name"],$_SESSION["ucode"]);
	}
	if($user == null){
		if(SYS_REQPATH == $apibase."/login"){
			if(isset($_REQUEST["username"]) && isset($_REQUEST["password"])){
				if(preg_match("/[A-Za-z0-9\.\-_]+/",$_REQUEST["username"])){
					$user = userObjectProvider::getUserByLocalNickname($_REQUEST["username"]);
					if($user->checkPassword($_REQUEST["password"])){
						$_SESSION["uid"] = $user->getUID();
						$_SESSION["name"] = $user->getNickName();
						$_SESSION["ucode"] = $user->getSessionCode();
						header("Location: /user");
						die();
					}
				}
			} else {
				header("Location: /user/login");
				die();
			}
		}
		if(substr(SYS_REQPATH,0,9) == "/user/api"){
			http_response_code(404);
			die();
		}
		if(SYS_REQPATH != "/user/login"){
			header("Location: /user/login");
			die();
		}
		require_once SYS_ROOT."template/usercenter/login.php";
		echo template\common\loginpage\get($apibase);
		die();
	}
	require_once SYS_ROOT."template/usercenter/frame.php";
	if(SYS_REQPATH == "/user/login"){
		header("Location: /user");
		die();
	}
	if(SYS_REQPATH == "/user"){
		$content = "<img src='/static/image/icon-title.png'/><br/>";
		$content .= "欢迎来到位于".$_config["site"]["domain"]."的Firefly站点，".$user->getDisplayName()."（".$user->getNickName()."）！";
		echo template\usercenter\frame\get($content,$user,$apibase);
		die();
	}
	if(SYS_REQPATH == "/user/profile"){
		require_once SYS_ROOT."template/usercenter/profile.php";
		$content = template\usercenter\profilepage\get($user,$apibase);
		echo template\usercenter\frame\get($content,$user,$apibase);
		die();
	}
	if(SYS_REQPATH == "/user/status"){
		require_once SYS_ROOT."router/user/tiezic.php";
		router_user_tiezic($user,$apibase);
		die();
	}
	if(SYS_REQPATH == "/user/send"){
		require_once SYS_ROOT."router/user/send.php";
		router_user_send($user,$apibase);
		die();
	}
	if(SYS_REQPATH == $apibase."/unlogin"){
		unset($_SESSION["uid"]);
		$_SESSION["nonce"] = $session_nonce+1;
		header("Location: /user/login");
		die();
	}
	if(SYS_REQPATH == $apibase."/delnote" && isset($_REQUEST["tid"])){
		$tid = $_REQUEST["tid"];
		$uid = $user->getUID();
		$_sitedb->query("DELETE FROM local_notes WHERE tid=$tid AND sender=$uid");
		header("Location: /user/status");
		die();
	}
	if(SYS_REQPATH == $apibase."/reprofile"){
		$_SESSION["nonce"] = $session_nonce+1;
		if(isset($_REQUEST["displayname"]) && $_REQUEST["displayname"] != ""){
			$_sitedb->query("UPDATE local_user_auth SET displayname=\"".addslashes($_REQUEST["displayname"])."\" WHERE uid=".$user->getUID());
		}
		if(isset($_REQUEST["headeruri"]) && $_REQUEST["headeruri"] != ""){
			$_sitedb->query("UPDATE local_user_auth SET headerurl=\"".addslashes($_REQUEST["headeruri"])."\" WHERE uid=".$user->getUID());
		}
		header("Location: /user/profile");
		die();
	}
	if(SYS_REQPATH == $apibase."/send" && isset($_REQUEST["method"]) && isset($_REQUEST["content"]) && $_REQUEST["content"] != ""){
		$contentText = $_REQUEST["content"];
		$contentHTML = "";
		switch($_REQUEST["method"]){
			case "html":
				$contentHTML = $contentText;
			break;
			case "plain":
				$contentHTML = str_replace(["<",">"," ","&","\n"],["&lt;","&gt;","&nbsp;","&amp;","<br/>"],$contentText);
			break;
			case "markdown":
				require_once SYS_ROOT."model/algorithms/parsedown.php";
				$parsedown = new algorithm\parsedown\Parsedown();
				$Parsedown->setMarkupEscaped(true);
				$Parsedown->setSafeMode(true);
				$contentHTML = $parsedown->text($contentText);
			break;
			default:
				http_response_code(404);
				die();
		}
		$contentABI = addslashes($contentHTML);
		$titleABI = isset($_REQUEST["title"])?addslashes($_REQUEST["title"]):"";
		$replytoABI = isset($_REQUEST["replyto"])?addslashes($_REQUEST["replyto"]):"";
		$uid = $user->getUID();
		$time = time();
		$_sitedb->query("INSERT INTO local_notes (sender,sendtimestamp,title,content,replyto) VALUES ($uid,$time,\"$titleABI\",\"$contentABI\",\"$replytoABI\")");
		header("Location: /user/status");
		die();
	}
	http_response_code(404);
	die();
}
?>