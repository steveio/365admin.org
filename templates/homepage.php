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

<div class="homepage row-fluid">
<section id="" class="col-12">
<div class="row">

    <section class="news">
    <div class="col-12">
    	<div class="banner-img"><img id="" class="img-responsive img-rounded" src="/images/gap_year_banner.jpg" width="100%" alt='' /></div>
        <div class="overlay">
            <div class="search-panel">
            <h1><?= $oHomepageArticle->GetTitle(); ?></h1>
            <p><?= $oHomepageArticle->GetDescShort(); ?></p>
            <?= $oSearchPanel->Render(); ?>
            </div>
        </div>
    </div>
    </section>


    <div class="row">
    <div class="col-12 my-3">
    <div class="col-8 sharethis-inline-share-buttons" style="display: block; float: right;"></div>
    </div>
    </div>


    <section class="blog">
        <div class="row"><?php
        if (is_array($oBlogArticle->GetArticleCollection()->Get()))
        {
            $aArticle = $oBlogArticle->GetArticleCollection()->Get(); 
            
            foreach($aArticle as $oArticle)
            { ?>
            <div class="col-sm-12 col-md-6 col-lg-6 my-3">
            	<div class="pull-right">
            	<?
            	if (is_object($oArticle->GetImage(0)) && $oArticle->GetImage(0)->GetHtml("_l",'')) {
            		print $oArticle->GetImage(0)->GetHtml("_l",$oArticle->GetTitle());
            	} elseif (is_object($oArticle->GetImage(0)) && $oArticle->GetImage(0)->GetHtml("_mf",'')) {
            		print $oArticle->GetImage(0)->GetHtml("_mf",$oArticle->GetTitle());
            	}
            	?>
            	</div>
            
            	<h1><?= $oArticle->GetTitle(); ?></h1>
            
            	<p><?= strip_tags($oArticle->GetDescShort()); ?></p>
            
            	<div>	
            	<p><?= Article::convertCkEditorFont2Html($oArticle->GetDescFull(),"h3"); ?> </p>
            	</div>
            	
            </div><?
            }
        } ?>
        </div>
    </section>

    <section class="row my-3">
        <div class="col-12">
        <?= $oHomepageArticle->GetDescFull(); ?>
        </div>    
    </section>

</div>
</section>
</div>