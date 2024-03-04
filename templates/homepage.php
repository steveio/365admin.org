<?php 

$aPageOptions = $this->Get('aPageOptions');
$oMainArticle = $this->Get('oArticle');

$aArticle = $this->Get('aArticle');
$aAttachedArticle = $this->Get('aAttachedArticle');

$aRelatedProfile = $this->Get('aRelatedProfile');
$aRelatedArticle = $this->Get('aRelatedArticle');

$oSearchPanel = $this->Get('oSearchPanel');

?>

<div class="container">
<div class="align-items-center justify-content-center">

<div class="homepage row-fluid">
<section id="" class="col-12">
<div class="row">

	<? if (is_object($oMainArticle))
	{ ?>
    <section class="news">
    <div class="col-12">
        <div class="overlay">
            <div class="search-panel">
            <h1><?= $oMainArticle->GetTitle(); ?></h1>
            <p><?= $oMainArticle->GetDescShort(); ?></p>
            <? if ($aPageOptions[ARTICLE_DISPLAY_OPT_PROFILE] != "f" && is_object($oSearchPanel)) { ?>
            <?= $oSearchPanel->Render(); ?>
            <? } ?>
            </div>
        </div>
    </div>
    </section><?
	} ?>


    <div class="row">
    <div class="col-12 my-3">
    <div class="col-sm-12 col-lg-8 col-md-8 sharethis-inline-share-buttons" style="display: block; float: right;"></div>
    </div>
    </div>


	<? if (is_array($aArticle))
	{ ?>
    <section class="news">
        <!-- BEGIN articles -->
        <div class="row">
		   <div class="col-sm-12 col-md-8 col-lg-8"><?
	        $limit = 5;
	        for ($i=0;$i<$limit;$i++) {
                $oArticle = array_shift($aArticle);
            	if (!is_object($oArticle)) continue;
                $oArticle->SetAttachedImages(); ?>
                <div class="row my-3" style="">
                    <? if (is_object($oArticle->GetImage(0))) { ?>
                    <div class="">
                      <a title="<?= $oArticle->GetTitle(); ?>" href="<?= $oArticle->GetUrl(); ?>">
                            <?= $oArticle->GetImage(0)->GetHtml("",$oArticle->GetTitle()); ?>
                      </a>
                     </div><? } ?>

                    <div class="my-2"></div>
                    <h2><a href="<?= $oArticle->GetUrl(); ?>" title="<?= $oArticle->GetTitle(); ?>"><?= $oArticle->GetTitle(); ?></a></h2>
                    <p><?= $oArticle->GetDescShort(160); ?></p>
                </div><?
            } ?>
            </div>

		   <div class="col-sm-12 col-md-3 col-lg-3 float-sm-none float-md-end float-lg-end"><?
	        $limit = 7;
	        for ($i=0;$i<$limit;$i++) { 
                $oArticle = array_shift($aArticle);
            	if (!is_object($oArticle)) continue;
                $oArticle->SetAttachedImages(); ?>
                <div class="row my-3">                                
                    <? if (is_object($oArticle->GetImage(0))) { ?>
                    <div>
                      <a title="<?= $oArticle->GetTitle(); ?>" href="<?= $oArticle->GetUrl(); ?>">
                            <?= $oArticle->GetImage(0)->GetHtml("",$oArticle->GetTitle()); ?>
                      </a>
                     </div><? } ?>
                    <div class="my-2"></div>
                    <h2><a href="<?= $oArticle->GetUrl(); ?>" title="<?= $oArticle->GetTitle(); ?>"><?= $oArticle->GetTitle(); ?></a></h2>
                    <p><?= $oArticle->GetDescShort(160); ?></p>
                </div><?
            } ?>
            </div>

        </div>
	</section><? 
	} ?>


    <section class="row my-3">
        <div class="col-12">
        <?= $oMainArticle->GetDescFull(); ?>
        </div>    
    </section>


<?php 
    if (is_array($aAttachedArticle) && count($aAttachedArticle) >= 1)
    { ?>
    <div class="row my-3">
    	<div class="row my-3">
    	<?php 
    	foreach($aAttachedArticle as $oArticle) 
    	{
    	   $oTemplate = new Template();
           $oTemplate->Set("oArticle", $oArticle);
           $oTemplate->Set("bHidePublishedDate", true);
           $oTemplate->Set("bHideDescShort", true);
           $oTemplate->LoadTemplate("article_summary.php");
           print $oTemplate->Render();
        } ?>
    	</div>
    </div><?
    }
?>



<?
if ($aPageOptions[ARTICLE_DISPLAY_OPT_REVIEW] == "t")
{ 
    $oReviewTemplate = $this->Get('oReviewTemplate');
    die(__FILE__."::".__LINE__);
    ?>
    <div class="row my-3">
    <h2>Comments</h2>
    <?php 
    print $oReviewTemplate->Render();
    ?>
    </div><?
}
?>


<?
if ($aPageOptions[ARTICLE_DISPLAY_OPT_ARTICLE] == "t")
{
    if (is_array($aRelatedProfile) && count($aRelatedProfile) >= 1)
    { ?>
    <div class="row my-3">
    	<h2>Related Opportunities</h2>
    	<div class="row my-3">
    	<?php 
    	foreach($aRelatedProfile as $oProfile) 
    	{
    	   $oTemplate = new Template();
           $oTemplate->Set("oProfile", $oProfile);
           $oTemplate->LoadTemplate("profile_summary.php");
           print $oTemplate->Render();
        } ?>
    	</div>
    </div><?
    }
}
?>

<?
if ($aPageOptions[ARTICLE_DISPLAY_OPT_ARTICLE] == "t")
{
    if (is_array($aRelatedArticle) && count($aRelatedArticle) >= 1)
    { ?>
    <div class="row" style="my-3">
        <h3>Related Articles</h3>
        <div class="row"><?
        foreach($aRelatedArticle as $oArticle)
        {
                if (is_object($oArticle)) 
                {
                    $oArticle->LoadTemplate("article_summary.php");
                    print $oArticle->Render();
                }
        } ?>
        </div>
    </div><?php
    }
}
?>


</div>
</section>
</div>

</div>
</div>
