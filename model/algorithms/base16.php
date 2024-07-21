<?php
namespace algorithm\authcrypto;

/*
Copyright © 2024 爱家家的卡卡
平台：博客园

原始文章：https://www.cnblogs.com/kaka0318/p/14668520.html
*/

function base16encode($string)
{
    $encode = '';
    $chars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];
    for ($i = 0; $i < strlen($string); $i++) {
        $encode .= $chars[(ord($string[$i]) & 0b11110000) >> 4] . $chars[ord($string[$i]) & 0b00001111];
    }
    return $encode;
}

function base16decode($encode)
{
    $result = '';
    for ($i = 0; $i < strlen($encode) / 2; $i++) {
        $result .= chr(intval(substr($encode, $i * 2, 2), 16));
    }
    return $result;
}
?>