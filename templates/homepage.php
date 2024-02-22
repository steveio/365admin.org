<?php 

$oSearchPanel = $this->Get('oSearchPanel');
$oHomepageArticle = $this->Get('oHomepageArticle');
$oBlogArticle = $this->Get('oBlogArticle');

?>
<?  if ($display_404) { ?>
<div class="row">
<div class="search-page-notify span12">

        <div class="alert alert-error">
        <h2>404 Not Found - Sorry the requested page was not found.</h2>
        </div>
</div>
</div>
<?php } ?>

<div class="homepage row">
<section id="" class="span12">
<div class="intro row-fluid">

    <section class="news">
    <div class="span12 container">
    	<div class="banner-img"><img id="" class="img-responsive img-rounded" src="/images/gapyearslider/v2/gap_year_banner.jpg" width="100%" alt='' /></div>
        <div class="overlay">
            <div class="search-panel">
            <h1><?= $oHomepageArticle->GetTitle(); ?></h1>
            <p><?= $oHomepageArticle->GetDescShort(); ?></p>
            <?= $oSearchPanel->Render(); ?>
            </div>
        </div>
    </div>
    </section>

    <div class="span12" style="margin-top: 20px;">
    <div class="pull-right sharethis-inline-share-buttons"></div>
    </div>


    <section class="blog">
        <div class="row-fluid featured"><?php
        if (is_array($oBlogArticle->GetArticleCollection()->Get()))
        {
            $aArticle = $oBlogArticle->GetArticleCollection()->Get();
        } ?>
        
        <div class="span8"><?
        $limit = 5;
        for ($i=0;$i<$limit;$i++) 
        {
            $oArticle = array_shift($aArticle);
            if (!is_object($oArticle)) continue;
            $oArticle->SetAttachedImages();
            $css_class = "span12 row-fluid";
            ?>
            <div class="<?= $css_class; ?>" style="">
            <? if (is_object($oArticle->GetImage(0))) 
            { ?>
            <div class="img-responsive img-rounded">
              <a title="<?= $oArticle->GetTitle(); ?>" href="<?= $oArticle->GetUrl(); ?>">
              <?= $oArticle->GetImage(0)->GetHtml("",$oArticle->GetTitle()); ?>
              </a>
            </div><? 
            } ?>

            <div class="pad-b"></div>
                <h2><a href="<?= $oArticle->GetUrl(); ?>" title="<?= $oArticle->GetTitle(); ?>"><?= $oArticle->GetTitle(); ?></a></h2>
                <p><?= $oArticle->GetDescShort(160); ?></p>
            </div><?php
        } ?>
        </div>        
        </div>
    </section>

</div>
</section>
</div>