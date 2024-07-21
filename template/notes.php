<?php
namespace template\notes\display\normal;
require_once SYS_ROOT."model/class/note.php";
require_once SYS_ROOT."model/algorithms/safehtml.php";

function get(\model_inotes $note,bool $aslink=false){
	$output = "";
	if($note->getReplyTo() != ""){
		$parent = $note->getReplyTo();
		if($parent != null){
			$tdr = \template\notes\display\hideparent\get($parent,true);
			$output .= "<div style=\"padding:4px;margin:3px;margin-bottom:5px;border:1px solid black;border-radius:3px;\">".$tdr."</div>";
		}
	}
	$output .= \template\notes\display\hideparent\get($note,$aslink);
	return $output;
}

namespace template\notes\display\hideparent;

function get(\model_inotes $note,bool $aslink=false){
	$sender = $note->getSenderObject();
	$header_url = $sender->getHeadImageURI();
	$usergid = $sender->getNickName()."@".$note->getDomain();
	$sendtimec = date("Y-m-d h:i:s",$note->getSendtime());
	$displayname = $sender->getDisplayName();
	$content = \algorithm\safehtml\process($note->getContent());
	$title = $note->getTitle();
	if($title != ""){
		$content = "<h2 style=\"margin-top:4px;margin-bottom:4px;\">".str_replace(["<",">","\n"," "],["&lt;","&gt;","","&nbsp;"],$title)."</h2>".$content;
	} else {
		$content = "<div style=\"margin-top:4px;\">$content</div>";
	}
	if($aslink){
		$url = $note->getURI();
		$content = "<a href=\"$url\" style=\"color:black;text-decoration:none;\">$content</a>";
	}
	return <<<EOF
	<div style="position:relative;height:67px;">
		<a href="/@$usergid">
			<div style="position:absolute;top:1px;width:65px;height:65px;">
				<img src="$header_url" style="width:65px;height:65px;border:1px solid black;border-radius:100px;"/>
			</div>
		</a>
		<div style="padding-left:75px;">
			<a href="/@$usergid" style="color:black;text-decoration:none;"><h2 style="display:inline;">$displayname</h2><br/>
			@$usergid</a><br/>于 $sendtimec 发布
		</div>
	</div>
	<div>
		$content
	</div>
	EOF;
}
?>