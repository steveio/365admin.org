<?php

/*
 * Controller for rendering / display of Articles 
 * 
 * 
*/

require_once("./conf/config.php");
require_once("./conf/brand_config.php");
require_once("./init.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");

$aResponse = array();


try {

    $article_path = "";

    $oArticleAssembler = new ArticleAssembler();    

    // 1.  Extract Article Path from URI (Published Articles)
    if (count($request_array) > 2)
    {
        unset($request_array[0]); // 
        unset($request_array[1]); // /article
        $article_path = implode("/",$request_array);
        $article_path = "/".$article_path;
        
        $oArticle = $oArticleAssembler->GetByPath($article_path, $oBrand->GetSiteId());

    } else {

        // 2.  Extract Article ID from $_REQUEST (UnPublished Articles)
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        if(!is_numeric($id)) throw new Exception("ERROR: Invalid Article ID");

        $oArticle = $oArticleAssembler->GetById($id);
        
    }

} catch (Exception $e) {
    $aResponse['msg'] = "ERROR: ".$e->getMessage();
    $aResponse['status'] = "danger";
}




print $oHeader->Render();
?>

<div class="container">
<div class="align-items-center justify-content-center">
<?


if (isset($aResponse['msg']) && strlen($aResponse['msg']) >= 1) {
?>
<div class="alert alert-<?= (isset($aResponse['status'])) ? $aResponse['status'] : "warning";  ?>" role="alert">
    <?= $aResponse['msg'];  ?>
</div> <? 
} else {

    print $oArticle->Render();
}

?>
</div>
</div>

<?
print $oFooter->Render();
?>