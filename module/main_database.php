<?php
require_once SYS_ROOT."config/config_main.php";
require_once SYS_ROOT."model/class/databases.php";
if($_config["sqldb"]["type"] == "mysql"){
	$_sitedb = new model_mysql_database($_config["sqldb"]["server"],$_config["sqldb"]["username"],$_config["sqldb"]["password"],$_config["sqldb"]["dbname"]);
} else {
	trigger_error("UNSUPPORTED DATABASE TYPE");
}
?>