<?php
define("SYS_ROOT",substr(dirname(__FILE__),0,-6));
define("SYS_REQPATH",explode("?",$_SERVER["REQUEST_URI"])[0]);
require_once SYS_ROOT."config/config_main.php";
require_once SYS_ROOT."module/main_database.php";
require_once SYS_ROOT."module/authenticate.php";
require_once SYS_ROOT."router/entrypoint.php";
router_entrypoint();
?>