<?php
require_once SYS_ROOT."config/config_main.php";
require_once SYS_ROOT."module/main_database.php";
require_once SYS_ROOT."model/algorithms/authcrypto.php";
require_once SYS_ROOT."model/utils/curl.php";
require_once SYS_ROOT."model/class/note.php";

interface model_profile{
	function getAPOutbox():array;
	function getAPOutboxContents():array;
	function getDisplayName():string;
	function getNickName():string;
	function getHeadImageURI():string;
	function getHomepage():string;
	function getAPProfile():array;
	function getAPPublicKey():string;
	function getDomain():string;
	function getSummary():string;
	function getOutbox():array;
}

class model_auth_user implements model_profile{
	private int $uid=0;
	private string $nickname="";
	function __construct($uid){
		$this->uid = $uid;
	}
	function getOutbox():array{
		global $_sitedb;
		global $_config;
		$uid = $this->uid;
		$result = $_sitedb->query("SELECT tid FROM local_notes WHERE sender=$uid");
		$domain = $_config["site"]["domain"];
		$outbox = array();
		foreach($result["data"] as $tmpvar1){
			$outbox[] = noteObjectProvider::getObjectByLocalTid($tmpvar1["tid"]);
		}
		return $outbox;
	}
	function getAPOutboxContents():array{
		global $_sitedb;
		global $_config;
		$uid = $this->uid;
		$result = $_sitedb->query("SELECT * FROM local_notes WHERE sender=$uid");
		$domain = $_config["site"]["domain"];
		$outbox = array();
		foreach($result["data"] as $tmpvar1){
			$tid = $tmpvar1["tid"];
			$outbox[] = [
				"@context"=>["https://www.w3.org/ns/activitystreams"],
				"id"=>"https://$domain/status/$tid",
				"url"=>"https://$domain/status/$tid",
				"to"=> ["https://www.w3.org/ns/activitystreams#Public"],
				"cc"=> ["https://lawrenceli.me/api/activitypub/followers"],
				"published"=>str_replace("+0000","GMT",date("r",$tmpvar1["sendtimestamp"])),
				"attributedTo"=>$this->getHomepage(),
				"content"=>$tmpvar1["content"],
				"type"=>"Note"
			];
		}
		return $outbox;
	}
	function getHomepage():string{
		global $_config;
		$domain = $_config["site"]["domain"];
		$uid = $this->uid;
		return "https://$domain/user/$uid";
	}
	function getAPProfile():array{
		global $_config;
		$domain = $_config["site"]["domain"];
		$nickname = $this->getNickName();
		$uid = $this->uid;
		return [
			"@context"=> ["https://www.w3.org/ns/activitystreams", "https://w3id.org/security/v1"],
			"id"=> "https://$domain/user/$uid",
			"type"=> "Person",
			"name"=> $this->getDisplayName(),
			"preferredUsername"=> $nickname,
			"summary"=> "",
			"inbox"=> "https://$domain/user/$uid/inbox",
			"outbox"=> "https://$domain/user/$uid/outbox",
			"followers"=> "https://$domain/user/$uid/followers",
			"icon"=> [$this->getHeadImageURI()],
			"publicKey"=> [
				"id"=> "https://$domain/@$nickname@$domain#main-key",
				"type"=>"Key",
				"owner"=> "https://$domain/user/$uid",
				"publicKeyPem"=> $this->getAPPublicKey()
			]
		];
	}
	function getAPOutbox():array{
		global $_config;
		$domain = $_config["site"]["domain"];
		$uid = $this->uid;
		$outboxContent = $this->getAPOutboxContents();
		return [
			"@context"=> "https://www.w3.org/ns/activitystreams",
			"id"=> "https://$domain/user/$uid/outbox",
			"summary"=> "Blog",
			"type"=> "OrderedCollection",
			"totalItems"=> count($outboxContent),
			"orderedItems"=> $outboxContent
		];
	}
	function getAPPublicKey():string{
		global $_sitedb;
		$uid = $this->uid;
		$result = $_sitedb->query("SELECT appubkey FROM local_user_auth WHERE uid=$uid");
		if(count($result["data"]) > 0){
			return $result["data"][0]["appubkey"];
		}
		return "";
	}
	function getAPPrivateKey():string{
		global $_sitedb;
		$uid = $this->uid;
		$result = $_sitedb->query("SELECT apprivkey FROM local_user_auth WHERE uid=$uid");
		if(count($result["data"]) > 0){
			return $result["data"][0]["apprivkey"];
		}
		return "";
	}
	function getDisplayName():string{
		global $_sitedb;
		$uid = $this->uid;
		$result = $_sitedb->query("SELECT displayname FROM local_user_auth WHERE uid=$uid");
		if(count($result["data"]) > 0){
			return $result["data"][0]["displayname"];
		}
		return "";
	}
	function getSummary():string{
		global $_sitedb;
		$uid = $this->uid;
		$result = $_sitedb->query("SELECT summary FROM local_user_auth WHERE uid=$uid");
		if(count($result["data"]) > 0){
			return $result["data"][0]["summary"];
		}
		return "";
	}
	function getNickName():string{
		if($this->nickname == ""){
			global $_sitedb;
			$uid = $this->uid;
			$result = $_sitedb->query("SELECT nickname FROM local_user_auth WHERE uid=$uid");
			if(count($result["data"]) > 0){
				$this->nickname = $result["data"][0]["nickname"];
			}
		}
		return $this->nickname;
	}
	function getUID():int{
		$uid = $this->uid;
		return $uid;
	}
	function getHeadImageURI():string{
		global $_sitedb;
		global $_config;
		$uid = $this->uid;
		$result = $_sitedb->query("SELECT headerurl FROM local_user_auth WHERE uid=$uid");
		if(count($result["data"]) > 0 && $result["data"][0]["headerurl"] != ""){
			return $result["data"][0]["headerurl"];
		}
		return $_config["display"]["default_header"];
	}
	function checkPassword(string $password):bool{
		global $_sitedb;
		$uid = $this->uid;
		$result = $_sitedb->query("SELECT nickname,passwordcci FROM local_user_auth WHERE uid=$uid");
		if(count($result["data"]) > 0){
			$nickname = $result["data"][0]["nickname"];
			$this->nickname = $nickname;
			$passcci = $result["data"][0]["passwordcci"];
			$inputcci = password_cci_hash($uid,$nickname,$password);
			return $passcci == $inputcci;
		}
		return false;
	}
	function getDomain():string{
		global $_config;
		return $_config["site"]["domain"];
	}
}

class model_remote_user implements model_profile{
	private string $url="";
	private string $nickname="";
	private string $domain="";
	private string $outboxurl="";
	function getAPOutbox():array{
		if($this->outboxurl == ""){
			$ics = $this->getAPProfile();
			if(isset($ics["outbox"])){
				$this->outboxurl = $ics["outbox"];
			}
		}
		if($this->outboxurl == ""){
			return "";
		}
		return json_decode(curl_request($this->outboxurl),true);
	}
	function getAPOutboxContents():array{
		$outbox = $this->getAPOutbox();
		if(isset($outbox["first"])){
			$firstPage = json_decode(curl_request($outbox["first"]),true);
			return isset($firstPage["orderedItems"])?$firstPage["orderedItems"]:array();
		}
		return isset($outbox["orderedItems"])?$outbox["orderedItems"]:array();
	}
	function getDisplayName():string{
		$ics = $this->getAPProfile();
		if(!isset($ics["name"])){
			return "";
		}
		return $ics["name"];
	}
	function getNickName():string{
		if($this->nickname == ""){
			$ics = $this->getAPProfile();
			if(isset($ics["preferredUsername"])){
				$this->nickname = $ics["preferredUsername"];
			}
		}
		return $this->nickname;
	}
	function getHeadImageURI():string{
		$ics = $this->getAPProfile();
		if(!isset($ics["icon"][0])){
			if(!isset($ics["icon"]["url"])){
				global $_config;
				return $_config["display"]["default_header"];
			}
			return $ics["icon"]["url"];
		}
		return $ics["icon"][0];
	}
	function getHomepage():string{
		if($this->url == ""){
			if($this->domain == "" || $this->nickname == ""){
				return "";
			}
			$webfinger = json_decode(curl_request("https://".$this->domain."/.well-known/webfinger?resource=acct:".$this->nickname."@".$this->domain),true);
			if(!isset($webfinger["links"])){
				return "";
			}
			foreach($webfinger["links"] as $link){
				if($link["rel"] == "self" && $link["type"] == "application/activity+json"){
					$this->url = $link["href"];
					continue;
				}
			}
		}
		if($this->url == ""){
			return "";
		}
		return $this->url;
	}
	function getAPProfile():array{
		$url = $this->getHomepage();
		if($url == ""){
			return array();
		}
		$data = json_decode(curl_request($url,'','',array("Accept: application/activity+json")),true);
		return $data==null?[]:$data;
	}
	function getSummary():string{
		$APProfile = $this->getAPProfile();
		if(isset($APProfile["summary"])){
			return $APProfile["summary"];
		}
		return "";
	}
	function getAPPublicKey():string{
		$ics = $this->getAPProfile();
		if(!isset($ics["publicKey"]["publicKeyPem"])){
			return "";
		}
		return $ics["publicKey"]["publicKeyPem"];
	}
	function getDomain():string{
		if($this->domain != ""){
			return $this->domain;
		}elseif(preg_match("/^https?:\/\/([A-Za-z0-9\.\-_]+)\/(.*)/",$this->url,$kde)){
			$this->domain = $kde[1];
		}
		return $this->domain;
	}
	function initHandle(string $username,string $domain){
		$this->nickname = $username;
		$this->domain = $domain;
	}
	function initURL(string $url){
		$this->url = $url;
	}
	function getOutbox():array{
		$outbox = $this->getAPOutboxContents();
		$output = array();
		foreach($outbox as $i){
			$output[] = noteObjectProvider::getObjectByURL($i["id"]);
		}
		return $output;
	}
}

class userObjectProvider{
	static private $cacheHeap=array("null"=>null);
	static private $uidCache=array();
	static private $gidCache=array();
	static private $uriCache=array();
	static function getUserByLocalUID(int $uid):model_auth_user|null{
		global $_config;
		$domain = $_config["site"]["domain"];
		if(isset(userObjectProvider::$uidCache[$uid])){
			return userObjectProvider::$cacheHeap[userObjectProvider::$uidCache[$uid]];
		}
		$user = new model_auth_user($uid);
		$nickname = $user->getNickname();
		if($nickname == ""){
			userObjectProvider::$uidCache[$uid] = "null";
			userObjectProvider::$uriCache["https://$domain/user/$uid"] = "null";
			return null;
		}
		$saveto = md5("user:local:$uid");
		userObjectProvider::$uidCache[$uid] = $saveto;
		userObjectProvider::$uriCache["https://$domain/user/$uid"] = $saveto;
		userObjectProvider::$gidCache["$nickname@$domain"] = $saveto;
		userObjectProvider::$cacheHeap[$saveto] = $user;
		return userObjectProvider::$cacheHeap[$saveto];
	}
	static function getUserByLocalNickname(string $nickname):model_auth_user|null{
		if(!preg_match("/^[A-Za-z0-9_\-\.]+$/",$nickname)){
			return null;
		}
		global $_config;
		$domain = $_config["site"]["domain"];
		if(isset(userObjectProvider::$gidCache["$nickname@$domain"])){
			return userObjectProvider::$cacheHeap[userObjectProvider::$gidCache["$nickname@$domain"]];
		}
		global $_sitedb;
		$result = $_sitedb->query("SELECT uid FROM local_user_auth WHERE nickname=\"".$nickname."\"");
		if(count($result["data"]) == 0){
			userObjectProvider::$gidCache["$nickname@$domain"] = "null";
			return null;
		}
		return userObjectProvider::getUserByLocalUID($result["data"][0]["uid"]);
	}
	static function getUserByHandle(string $nickname,string $domain=""):model_profile|null{
		if(isset(userObjectProvider::$gidCache["$nickname@$domain"])){
			return userObjectProvider::$cacheHeap[userObjectProvider::$gidCache["$nickname@$domain"]];
		}
		global $_config;
		if($domain == "" || $domain == $_config["site"]["domain"]){
			return userObjectProvider::getUserByLocalNickname($nickname);
		}
		$remote = new model_remote_user();
		$remote->initHandle($nickname,$domain);
		$url = $remote->getHomepage();
		if($url == ""){
			userObjectProvider::$gidCache["$nickname@$domain"] = "null";
		}
		$heapPos = md5("ap:remoteuser:$nickname@$domain");
		userObjectProvider::$gidCache["$nickname@$domain"] = $heapPos;
		userObjectProvider::$uriCache[$url] = $heapPos;
		userObjectProvider::$cacheHeap[$heapPos] = $remote;
		return $remote;
	}
	static function getUserByURL(string $url):model_profile|null{
		global $_config;
		if(isset(userObjectProvider::$urlCache[$url])){
			return userObjectProvider::$cacheHeap[userObjectProvider::$urlCache[$url]];
		}
		if(preg_match("/^https?:\/\/([A-Za-z0-9\.\-_]+)\/(.*)/",$url,$kde)){
			if($kde[1] == $_config["site"]["domain"]){
				if(preg_match("/^https?:\/\/([A-Za-z0-9\.\-_]+)\/user\/([1-9][0-9]*)$/",$url,$kde)){
					return userObjectProvider::getUserByLocalUID((int)($kde));
				} elseif(preg_match("/^https?:\/\/([A-Za-z0-9\.\-_]+)\/@([A-Za-z0-9\.\-_]+)/",$url,$kde)){
					return userObjectProvider::getUserByLocalNickname($kde[2]);
				} elseif(preg_match("/^https?:\/\/([A-Za-z0-9\.\-_]+)\/@([A-Za-z0-9\.\-_]+)@([A-Za-z0-9\.\-_]+)/",$url,$kde)){
					return userObjectProvider::getUserByHandle($kde[2],$kde[3]);
				} else {
					return null;
				}
			}
		} else {
			return null;
		}
		$remote = new model_remote_user();
		$remote->initURL($url);
		$nickname = $remote->getNickName();
		if($nickname == ""){
			userObjectProvider::$uriCache[$url] = "null";
			return null;
		}
		$idurl = $remote->getAPProfile()["id"];
		if(preg_match("/^https?:\/\/([A-Za-z0-9\.\-_]+)\/user\/([1-9][0-9]*)$/",$idurl,$kde)){
			return userObjectProvider::getUserByLocalUID((int)($kde));
		}
		$remote->initURL($idurl);
		$domain = $remote->getDomain();
		$heapPos = md5("ap:remoteuser:$nickname@$domain");
		userObjectProvider::$gidCache["$nickname@$domain"] = $heapPos;
		userObjectProvider::$uriCache[$url] = $heapPos;
		userObjectProvider::$uriCache[$idurl] = $heapPos;
		userObjectProvider::$cacheHeap[$heapPos] = $remote;
		return $remote;
	}
}
?>