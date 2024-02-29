<?

$oArticle = $this->Get('oArticle');
$aPageOptions = $this->Get('aPageOptions');

$aArticle = $this->Get('aArticle');

$aAttachedArticle = $this->Get('aAttachedArticle');
$aRelatedProfile = $this->Get('aRelatedProfile');
$aRelatedArticle = $this->Get('aRelatedArticle');

?>


<?php if ($aPageOptions[ARTICLE_DISPLAY_OPT_ADS] != "f") { ?>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
 (adsbygoogle = window.adsbygoogle || []).push({
      google_ad_client: "ca-pub-9874604497476880",
      enable_page_level_ads: true
 });
</script> 
<?php  } ?>


<div class="container">
<div class="align-items-center justify-content-center">


<?php if ($aPageOptions[ARTICLE_DISPLAY_OPT_SOCIAL] != "f") { ?>
<div class="row">
    <div class="col-12 my-3">
    <div class="col-8 sharethis-inline-share-buttons" style="display: block; float: right;"></div>
    </div>
</div>
<?php } ?>


<div class="row my-3">
    <div class="col-12">
    
        <? if ($aPageOptions[ARTICLE_DISPLAY_OPT_IMG] != "f") { ?>
    	<div class="float-end col-lg-6 col-sm-12 m-3">
    	<?
    	if (is_object($oArticle->GetImage(0)) && $oArticle->GetImage(0)->GetHtml("",'')) {
    		print $oArticle->GetImage(0)->GetHtml("",$oArticle->GetTitle());
    	} elseif (is_object($oArticle->GetImage(0)) && $oArticle->GetImage(0)->GetHtml("_mf",'')) {
    		print $oArticle->GetImage(0)->GetHtml("_mf",$oArticle->GetTitle());
    	}
    	?>
    	</div>
    
    	<?php } ?>
    
    	<div class="my-3">
    	<h1><?= $oArticle->GetTitle(); ?></h1>
    	</div>
    
    	<div class="my-3">
    	<p class="lead"><?= $oArticle->GetDescShortPlaintext(); ?></p>
    	</div>

      <?
        if ($aPageOptions[ARTICLE_DISPLAY_OPT_SEARCH_PANEL] != "f") {
            $oSearchResultPanel = $this->Get('oSearchResult');
            if (is_object($oSearchResultPanel))
            {
                    print $oSearchResultPanel->Render();
            }
        }
        ?>
    
    	<div class="article-body my-3">
    	<p><?= $oArticle->GetDescLongClean();?></p>
    	</div>
 
     	<?
     	if ($aPageOptions[ARTICLE_DISPLAY_OPT_BLOG] == "t") {
            if (is_array($aArticle))
        	{ ?>
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
        
                            <div class="col-8 my-2">
                            <h2><a href="<?= $oArticle->GetUrl(); ?>" title="<?= $oArticle->GetTitle(); ?>"><?= $oArticle->GetTitle(); ?></a></h2>
                            <p><?= $oArticle->GetDescShort(160); ?></p>
                            </div>
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
                </div><? 
        	} 
     	} ?>
    
    </div>
</div>


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
           $oTemplate->LoadTemplate("article_summary.php");
           print $oTemplate->Render();
        } ?>
    	</div>
    </div><?
    }
?>



<?
if ($aPageOptions[ARTICLE_DISPLAY_OPT_REVIEW] != "f")
{ 
    $oReviewTemplate = $this->Get('oReviewTemplate');
    if (is_object($oReviewTemplate)) 
    {
    ?>
    <div class="row my-3">
    <h2>Comments</h2>
    <?php 
    print $oReviewTemplate->Render();
    ?>
    </div><?
    }
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
</div>
