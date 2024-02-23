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


    <section class="news">
        <!-- BEGIN blog articles -->
        <div class="row"><?php 
            if (is_array($oBlogArticle->GetArticleCollection()->Get()))
            {
                $aArticle = $oBlogArticle->GetArticleCollection()->Get();
            } ?>

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
	</section>


    <section class="row my-3">
        <div class="col-12">
        <?= $oHomepageArticle->GetDescFull(); ?>
        </div>    
    </section>

</div>
</section>
</div>