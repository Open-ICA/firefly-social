<?php
require_once SYS_ROOT."model/class/user.php";
require_once SYS_ROOT."model/utils/curl.php";

interface model_inotes{
	function getSenderObject():model_profile|null;
	function getReplyTo():model_inotes|null;
	function getContent():string;
	function getTitle():string;
	function getURI():string;
	function getSenderURI():string;
	function getSenderNickName():string;
	function getSenderDisplayName():string;
	function getDomain():string;
	function getSenderDomain():string;
	function getAPObject():array;
	function getSendtime():int;
}

class model_local_note implements model_inotes{
	private int $tid;
	function __construct($tid){
		$this->tid = $tid;
	}
	function getSenderObject():model_profile|null{
		return userObjectProvider::getUserByURL($this->getSenderURI());
	}
	function getReplyTo():model_inotes|null{
		$ap = $this->getAPObject();
		if(isset($ap["inReplyTo"])){
			return noteObjectProvider::getObjectByURL($ap["inReplyTo"]);
		}
		return null;
	}
	function getContent():string{
		$ap = $this->getAPObject();
		if(isset($ap["content"])){
			return $ap["content"];
		}
		return "";
	}
	function getTitle():string{
		$ap = $this->getAPObject();
		if(isset($ap["name"])){
			return $ap["name"];
		}
		return "";
	}
	function getSenderURI():string{
		global $_config;
		$domain = $_config["site"]["domain"];
		return "https://$domain/user/".$this->getSenderUID();
	}
	function getURI():string{
		global $_config;
		$domain = $_config["site"]["domain"];
		return "https://$domain/status/".$this->tid;
	}
	function getSenderNickName():string{
		$sender = getSenderObject();
		if($sender == null){
			return "";
		}
		return $sender->getNickName();
	}
	function getSenderDisplayName():string{
		$sender = getSenderObject();
		if($sender == null){
			return "";
		}
		return $sender->getDisplayName();
	}
	function getDomain():string{
		global $_config;
		return $_config["site"]["domain"];
	}
	function getSenderDomain():string{
		global $_config;
		return $_config["site"]["domain"];
	}
	function getAPObject():array{
		global $_sitedb;
		global $_config;
		$tid = $this->tid;
		$result = $_sitedb->query("SELECT * FROM local_notes WHERE tid=$tid LIMIT 1");
		$domain = $_config["site"]["domain"];
		if(!isset($result["data"][0])){
			return [];
		}
		$tmpvar1 = $result["data"][0];
		$obj = [
			"@context"=>["https://www.w3.org/ns/activitystreams"],
			"id"=>"https://$domain/status/$tid",
			"url"=>"https://$domain/status/$tid",
			"to"=> ["https://www.w3.org/ns/activitystreams#Public"],
			"cc"=> ["https://lawrenceli.me/api/activitypub/followers"],
			"published"=>str_replace("+0000","GMT",date("r",$tmpvar1["sendtimestamp"])),
			"attributedTo"=>$this->getSenderURI(),
			"content"=>$tmpvar1["content"],
			"type"=>($tmpvar1["title"]==""?"Note":"Article"),
			"name"=>$tmpvar1["title"]
		];
		if($tmpvar1["replyto"] != ""){
			$obj["inReplyTo"] = $tmpvar1["replyto"];
		}
		return $obj;
	}
	function getSenderUID():int{
		global $_sitedb;
		$tid = $this->tid;
		$result = $_sitedb->query("SELECT sender FROM local_notes WHERE tid=$tid LIMIT 1");
		if(count($result["data"]) > 0){
			return $result["data"][0]["sender"];
		}
		return 0;
	}
	function getSendtime():int{
		global $_sitedb;
		$tid = $this->tid;
		$result = $_sitedb->query("SELECT * FROM local_notes WHERE tid=$tid LIMIT 1");
		if(!isset($result["data"][0])){
			return 0;
		}
		return $result["data"][0]["sendtimestamp"];
	}
}

class model_remote_note implements model_inotes{
	private string $url="";
	private string $sendernick="";
	private string $domain="";
	private string $senderurl="";
	function __construct($url){
		$this->url = $url;
		$ap = $this->getAPObject();
		if(isset($ap["id"])){
			$this->url = $ap["id"];
		}
	}
	function getURI():string{
		return $this->url;
	}
	function getSenderObject():model_profile|null{
		return userObjectProvider::getUserByURL($this->getSenderURI());
	}
	function getReplyTo():model_inotes|null{
		$ap = $this->getAPObject();
		if(isset($ap["inReplyTo"])){
			return noteObjectProvider::getObjectByURL($ap["inReplyTo"]);
		}
		return null;
	}
	function getContent():string{
		$ap = $this->getAPObject();
		if(!isset($ap["type"]) || !in_array($ap["type"],array("Note","Article"))){
			return "";
		}
		if(isset($ap["content"])){
			return $ap["content"];
		}
		return "";
	}
	function getTitle():string{
		$ap = $this->getAPObject();
		if(isset($ap["name"])){
			return $ap["name"];
		}
		return "";
	}
	function getSenderURI():string{
		if($this->senderurl != ""){
			return $this->senderurl;
		}
		$ap = $this->getAPObject();
		if(isset($ap["attributedTo"])){
			$this->senderurl = $ap["attributedTo"];
			return $ap["attributedTo"];
		}
		return "";
	}
	function getSenderNickName():string{
		$sender = $this->getSenderObject();
		if($sender != null){
			return $sender->getNickName();
		}
		return "";
	}
	function getSenderDisplayName():string{
		$sender = $this->getSenderObject();
		if($sender != null){
			return $sender->getDisplayName();
		}
		return "";
	}
	function getSenderDomain():string{
		if($this->domain != ""){
			return $this->domain;
		}elseif(preg_match("/^https?:\/\/([A-Za-z0-9\.\-_]+)\/(.*)/",$this->url,$kde)){
			$this->domain = $kde[1];
			return $this->domain;
		}
		return "";
	}
	function getDomain():string{
		return $this->getSenderDomain();
	}
	function getAPObject():array{
		return json_decode(curl_request($this->url,'','',array("Accept: application/activity+json")),true);
	}
	function getSendtime():int{
		$ap = $this->getAPObject();
		if(!isset($ap["published"])){
			return 0;
		}
		return strtotime($ap["published"]);
	}
}

class noteObjectProvider{
	static $urlObjectCache=array();
	static function getObjectByLocalTid($tid):model_inotes|null{
		global $_config;
		$domain = $_config["site"]["domain"];
		if(isset($urlObjectCache["https://$domain/status/$tid"])){
			return $urlObjectCache["https://$domain/status/$tid"];
		}
		$object = new model_local_note($tid);
		if($object->getContent() == ""){
			$urlObjectCache["https://$domain/status/$tid"] = null;
			return null;
		}
		$urlObjectCache["https://$domain/status/$tid"] = $object;
		return $object;
	}
	static function getObjectByURL($url):model_inotes|null{
		global $_config;
		$url = preg_replace("~^http:~","https:",$url);
		if(preg_match("/^https:\/\/([A-Za-z0-9\.\-_]+)\/status\/([1-9][0-9]*)$/",$url,$adx)){
			if($adx[1] == $_config["site"]["domain"]){
				return noteObjectProvider::getObjectByLocalTid((int)($adx[2]));
			}
		}
		$tieobj = new model_remote_note($url);
		if($tieobj->getContent() == ""){
			$urlObjectCache[$url] = null;
			return null;
		}
		if(preg_match("/^https:\/\/([A-Za-z0-9\.\-_]+)\/status\/([1-9][0-9]*)$/",$tieobj->getURI(),$adx)){
			if($adx[1] == $_config["site"]["domain"]){
				return noteObjectProvider::getObjectByLocalTid((int)($adx[2]));
			}
		}
		$urlObjectCache[$url] = $tieobj;
		$urlObjectCache[$tieobj->getURI()] = $tieobj;
		return $tieobj;
	}
}
?>