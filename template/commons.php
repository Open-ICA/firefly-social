<?php
namespace template\common\header;
function get($subtitle=""){
	$subtitle = $subtitle==""?"":" | ".$subtitle;
	return <<<EOF
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Firefly$subtitle</title>
		<style>
			body{
				padding:0px;
				margin:0px;
				margin-top:2px;
			}
			@media screen and (max-width: 1006px) {
				#firefly_common_main{
					margin:3px;
					width:100%;
				}
			}
			@media screen and (min-width: 1007px) {
				#firefly_common_main{
					width: 1000px;
					margin:auto;
				}
			}
			.normilink{
				color:black;
			}
			.normilink:hover{
				text-decoration:none;
			}
		</style>
	</head>
	<body>
	<div id="firefly_common_main">
		<a href="/"><img src="/static/image/icon-title.png"/></a>
		<hr/>
EOF;
}

namespace template\common\footer;
function get(){
	return <<<EOF
	<hr/>
	Powered by Firefly.
	</div>
	</body>
</html>
EOF;
}