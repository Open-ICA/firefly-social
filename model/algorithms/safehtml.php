<?php
namespace algorithm\safehtml;

function process($input) {
	$allowedTags = '<iframe><div><p><h1><h2><h3><h4><h5><h6><img><video><audio><source><span><br><i><b><u><a><button>';
    // 允许特定的HTML标签
    $safeHtmlContent = strip_tags($input, $allowedTags);
    return $safeHtmlContent;
}