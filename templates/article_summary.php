<?php 

$oArticle = $this->Get("ARTICLE_OBJECT");

?>

<div class="col-sm-12 col-md-4 col-lg-4 my-2">
    <? if (is_object($oArticle->GetImage(0))) { ?>
	<div class="my-3">
        <a title="<?= $oArticle->GetTitle(); ?>" href="<?= $oArticle->GetUrl(); ?>">
            <?= $oArticle->GetImage(0)->GetHtml("_mf",$oArticle->GetTitle(),""); ?>
        </a>
    </div>
    <? } ?>

	<div class="col-8">
        <h3><a class="title-summary" href="<?= $oArticle->GetUrl(); ?>" title="<?= $oArticle->GetTitle(); ?>"><?= $oArticle->GetTitle(); ?></a></h3>
        <p class=""><?= $oArticle->GetDescShortPlaintext(120); ?></p>
		<p class=""><small class="text-muted"><?= $oArticle->GetPublishedDate(); ?></small></p>
	</div>
</div>