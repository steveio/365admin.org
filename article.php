<?php

/*
 * Handles display of Articles 
 * 
 * 
*/

require_once("./conf/config.php");
require_once("./init.php");
require_once("./conf/brand_config.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


$id = $_REQUEST['id'];

if(!is_numeric($id)) AppError::StopRedirect($sUrl = $_CONFIG['url'],$sMsg = "ERROR : Article not found.");


/* retrieve the article */
$oArticle = new Article();
$oArticle->GetById($id);


$oArticle->LoadTemplate("article_01.php",$aOptions = array());





print $oHeader->Render();
?>


<?= $oArticle->Render(); ?>


<?
print $oFooter->Render();
?>
