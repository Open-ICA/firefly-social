<?php
require_once SYS_ROOT."model/class/user.php";

class AuthenticatePod{
	static private int $uid=0;
	static private int $username=0;
	static function getUID():int{
		return AuthenticatePod::$uid;
	}
	static function getUserObject():model_auth_user{
		return userObjectProvider::getUserByLocalUID(AuthenticatePod::$uid);
	}
	static function getUserName():string{
		return AuthenticatePod::$username;
	}
	static function setLoginStatusByUID(int $uid){
		AuthenticatePod::$uid = $uid;
		if($uid == 0){
			AuthenticatePod::$username = "";
			return;
		}
		$object = AuthenticatePod::getUserObject();
		if($object == null){
			AuthenticatePod::$uid = 0;
			AuthenticatePod::$username = "";
			return;
		}
		AuthenticatePod::$username = $object->getNickName();
	}
	static function setLoginStatusByUserName(string $nickname){
		AuthenticatePod::$username = $username;
		if($username == ""){
			AuthenticatePod::$uid = 0;
			return;
		}
		$object = userObjectProvider::getUserByLocalNickname($username);
		if($object == null){
			AuthenticatePod::$username = "";
			AuthenticatePod::$uid = 0;
			return;
		}
		AuthenticatePod::$uid = $object->getUID();
	}
}
?>