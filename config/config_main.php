<?php
$_config = array();
// 数据库设置
$_config["sqldb"]["type"] = "mysql"; // 数据库类型，目前只支持mysql
$_config["sqldb"]["server"] = "127.0.0.1"; // 数据库地址
$_config["sqldb"]["dbname"] = "firefly"; // 数据库名
$_config["sqldb"]["username"] = "root"; // 用户名
$_config["sqldb"]["password"] = ""; // 密码

// 网站信息
$_config["site"]["domain"] = "127.0.0.1"; // 域名，必须与网站访问域名一致
$_config["site"]["name"] = "碧蓝航线官方百合站"; // 网站默认标题

// 显示设置
$_config["display"]["default_header"] = "https://s21.ax1x.com/2024/06/17/pk0NpTg.png"; // 设置默认头像

// 系统配置
$_config["system"]["use_req_cronjob"] = true; // 在用户页面中执行Cronjob（AJAX），主机配置了worker任务建议关闭，没有配置worker必须打开
?>