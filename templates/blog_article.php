<?php 
$oArticle = $this->Get('oArticle');
?>
<div class="card border-0  py-2 my-2">

    <? if (is_object($oArticle->GetImage(0))) { ?>
        <a title="<?= $oArticle->GetTitle() ?>" href="<?= $oArticle->GetUrl(); ?>">
            <?= $oArticle->GetImage(0)->GetHtml("_l",$oArticle->GetTotle(),"card-img-top"); ?>
        </a>
    <? } else { 
        // try to grab an image from article body text
        $html = $oArticle->GetDescFull();
        $arrImgUrl = array();
        preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $arrImgUrl );
        if (count($arrImgUrl[1]) >= 1)
        { ?>
    	<a title="<?= $oArticle->GetTitle(); ?>" href="<?= $oArticle->GetUrl(); ?>">
    		<img class='img-responsive img-rounded' src='<?= $arrImgUrl[1][0] ?>' alt='<?= $oArticle->GetTitle(); ?>' border='0' />
    	</a><?php 
        }
    }?>

    <div class="card-body col-4 card-img-overlay">
    	<div class="box box2">
            <h2 class="card-title"><a class="blue" style="font-weight: bold;" href="<?= $oArticle->GetUrl();; ?>" title="<?= $oArticle->GetTitle();; ?>"><?= $oArticle->GetTitle();; ?></a></h2>
            <p class="card-text"><?= $oArticle->GetDescShortPlaintext(120); ?></p>
    		<!-- <p class="card-text"><small class="text-muted"><?= $oArticle->GetPublishedDate(); ?></small></p> -->
		</div>        
    </div>
</div>

