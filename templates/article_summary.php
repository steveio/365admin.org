<?php 
$oArticle = $this->Get("oArticle");

$css_class_col = $this->Get("CSS_CLASS_COL");
$bHidePublishedDate = $this->Get("bHidePublishedDate");
$bHideDescShort = $this->Get("bHideDescShort");
$img_size = ($this->Get("IMG_FORMAT") == "") ? "" : "_lf";

?>

<div class="<?= $css_class_col; ?> my-2">
	<div class="col-sm-12 my-3">
    <? if (is_object($oArticle->GetImage(0))) { ?>
        <a title="<?= $oArticle->GetDescShortPlaintext(120); ?>" href="<?= $oArticle->GetUrl(); ?>">
            <?= $oArticle->GetImage(0)->GetHtml($img_size,$oArticle->GetTitle(),"img-fluid img-blog rounded mb-3"); ?>
        </a>
    <? } else { 
        $html = $oArticle->GetDescFull();
        $aImgUrl = array();
        preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $aImgUrl );
        
        if (count($aImgUrl[0]) >= 1)
        {
            print "<img src='".$aImgUrl[1][0]."' class='img-fluid img-blog rounded mb-3' alt='' border='0' />";
        }
    } ?>
	</div>

	<div class="col-sm-12 col-lg-8 col-md-8">
        <h3><a class="title-summary" href="<?= $oArticle->GetUrl(); ?>" title="<?= $oArticle->GetDescShortPlaintext(120); ?>"><?= $oArticle->GetTitle(); ?></a></h3>
        <? if (!$bHideDescShort) { ?>
        <p class=""><?= $oArticle->GetDescShortPlaintext(120); ?></p>
        <? } ?>
        <? if (!$bHidePublishedDate) { ?>
		<p class=""><small class="text-muted"><?= $oArticle->GetPublishedDate(); ?></small></p>
		<? } ?>
	</div>
</div>
