<? 
$oArticle = $this->Get('oArticle'); 
$iTotalMatchedArticle = $this->Get('iTotalMatchedArticle');
$iPageSize = $this->Get('iPageSize');

?>
<div class="container">
<div class="align-items-center justify-content-center">



<div class="row">
<div class="col-12 my-3">
<div class="col-8 sharethis-inline-share-buttons" style="display: block; float: right;"></div>
</div>
</div>


<div class="row">
	<div class="pull-right image">
	<?
	if (is_object($oArticle->GetImage(0)) && $oArticle->GetImage(0)->GetHtml("_lf",'')) {
		print $oArticle->GetImage(0)->GetHtml("_lf",$oArticle->GetTitle());
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
	
</div>


<div class="row">
        <?
        $oPager = new PagedResultSet();
        $oPager->SetResultsPerPage($iPageSize);
        $oPager->GetByCount($iTotalMatchedArticle,"page");
        
        if (is_array($oArticle->GetArticleCollection()->Get()))
        {
            foreach($oArticle->GetArticleCollection()->Get() as $oArticle)
            {
        ?>        
        <div class="card-group col-sm-4">
            <div class="card border-0  py-2 my-2">
            
                <? if (is_object($oArticle->GetImage(0))) { ?>
                    <a title="<?= $oArticle->GetTitle() ?>" href="<?= $oArticle->GetUrl() ?>">
                        <?= $oArticle->GetImage(0)->GetHtml("_lf",$oArticle->GetTitle(),"card-img-top"); ?>
                    </a>
                <? } else { 
                    // try to grab an image from article body text
                    $html = $oArticle->GetDescFull();
                    $arrImgUrl = array();
                    preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $arrImgUrl );
                    if (count($arrImgUrl[1]) >= 1)
                    { ?>
                	<a title="<?= $oArticle->GetTitle() ?>" href="<?= $oArticle->GetUrl() ?>">
                		<img class='img-responsive img-rounded' src='<?= $arrImgUrl[1][0] ?>' alt='<?= $oArticle->GetTitle(); ?>' border='0' />
                	</a><?php 
                    }
                }?>
            
                <div class="card-body">
                	<div class="">
                        <h2 class="card-title"><a class="blue" href="<?= $oArticle->GetUrl(); ?>" title="<?= $oArticle->GetTitle(); ?>"><?= $oArticle->GetTitle(); ?></a></h2>
                        <p class="card-text"><?= $oArticle->GetDescShort(); ?></p>
                		<p class="card-text"><small class="text-muted"><?= $oArticle->GetPublishedDate(); ?></small></p>
            		</div>        
                </div>
            </div>
        </div> 

        <?php 
            }
        }
        ?>
</div>

<div class="row">
		<div id="pager" class="row pagination pagination-large pagination-centered">	
		<?= $oPager->RenderHTML(); ?>
		</div>
</div>


</div>
</div>
