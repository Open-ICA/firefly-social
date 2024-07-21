<?php
namespace algorithm\authcrypto;
require_once SYS_ROOT."model/algorithms/base16.php";

function password_cci_hash(int $uid,string $nickname,string $password){
	$cycle = md5(md5($username).md5($password).$uid);
	$gent = array(
		array("a","@"),array("b","#"),array("n","$"),array("v","%"),
		array("c","^"),array("k","*"),array("h",")"),array("z",":"),
		array("p","("),array("T","+"),array("R","-"),array("E","!"),
		array("5","?"),array("6","]"),array("J","["),array("M","~"),
		array("G",'"'),array("Q","&"),array("l","{"),array("L","{")
	);
	for ($s=1; $s<=3; $s++){
		for ($i=1; $i<=22; $i++){
			$cycle .= md5(md5($username.$cycle).$password);
			$cycle = md5($cycle.md5($uid.":".$cycle)).$cycle;
			$cycle = substr($cycle,(7+$i<30)?(7+$i):30);
		}
	}
	$ret = "";
	for ($i=1; $i<=7; $i++){
		$ret .= md5($cycle.$username.md5($ret).md5($uid."/".$username)).md5($cycle.$password.md5($ret)).md5("UDX".md5($username).md5($ret).md5($password));
	}
	$shall = base64_encode(base16decode(strtoupper($ret)));
	foreach($gent as $ig){
		$shall = preg_replace("~".$ig[0]."[B-X2-6]~",$ig[1],$shall);
	}
	return $shall;
}
?>