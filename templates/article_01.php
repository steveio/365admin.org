<!-- BEGIN article_01 -->
<?

$oArticle = $this->Get('oArticle');
$aPageOptions = $this->Get('aPageOptions');

?>


<?php if ($aPageOptions[ARTICLE_DISPLAY_OPT_ADS] != "f") { ?>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
 //(adsbygoogle = window.adsbygoogle || []).push({
 //     google_ad_client: "ca-pub-9874604497476880",
 //     enable_page_level_ads: true
 //});
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


<div class="row-fluid my-3">
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

	<h1><?= $oArticle->GetTitle(); ?></h1>

	<p class="lead"><?= strip_tags($oArticle->GetDescShort()); ?></p>

        <?
    	if ($aPageOptions[ARTICLE_DISPLAY_OPT_PLACEMENT] != "f") {
                    $oSearchResultPanel = $this->Get('oSearchResult');
                    if (is_object($oSearchResultPanel))
                            print $oSearchResultPanel->Render();
    	}
        ?>

		<div class="">
					<?
					  // insert related profiles into article body
						$strArticleBody = Article::convertCkEditorFont2Html($oArticle->GetDescFull(),"h3");

						$aH2Blocks = explode("<h2>",$strArticleBody);
						$aH3Blocks = explode("<h3>",$strArticleBody);
						$aBlocks = (count($aH2Blocks) > count($aH3Blocks)) ? $aH2Blocks : $aH3Blocks;
						$strHeaderTag = (count($aH2Blocks) > count($aH3Blocks)) ? "<h2>" : "<h3>";

						if ($aPageOptions[ARTICLE_DISPLAY_OPT_PROFILE] != "f") {
    						// get related placements
    						$aProfile = $oArticle->GetAttachedProfile();
						} else {
						     $aProfile = array();   
						}

						$i = 0; // block index
						$iAdsInserted = 0;
						$lineCount = 0;
						for($i=0; $i<count($aBlocks);$i++)
						{

                            if ($i >= 1) print $strHeaderTag;
                            print $aBlocks[$i];
                            $lineCount += count(explode("\n",$aBlocks[$i]));
                            
                            // insert ads every nth block (except when block line count less than minimum)
                            if ($lineCount > 20 && count($aProfile) >= 1 && ($i > 1) && ($i % 4 == 0))
                            {
                            
                            	//$strTemplate = (($iAdsInserted % 2) == 0) ? "featured_project_list_col3.php" : "featured_project_list_sm.php";
                            	//$strProfileGroupName = (($iAdsInserted % 2) == 0) ? "aProfile" : "aCompany";
                            	$strTemplate = "profile_related.php";
                            	$strProfileGroupName = "aProfile";
                            
                            	$aProfileGroup = array();
                                $iNumProfiles = 3;
                                for($j=0;$j<$iNumProfiles;$j++)
                                {
                                    $aProfileGroup[] = array_shift($$strProfileGroupName);
                                }
                            
                                $oTemplate = new Template();
                                $oTemplate->Set("PROFILE_ARRAY",$aProfileGroup);
                            	$oTemplate->Set("PROFILE_TYPE",$strProfileGroupName);
                                $oTemplate->LoadTemplate($strTemplate);
                                print $oTemplate->Render();
                            	$iAdsInserted++;
                            	$lineCount = 0;
                            }
				   }

			    ?>
    </div>
	</div>
</div>
</div>

<?
if ($aPageOptions[ARTICLE_DISPLAY_OPT_REVIEW] != "f")
{ 
    $oReviewTemplate = $this->Get('oReviewTemplate');
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
if ($aPageOptions[ARTICLE_DISPLAY_OPT_ARTICLE] != "f")
{
    if (is_array($oArticle->GetArticleCollection()->Get()) && count($oArticle->GetArticleCollection()->Get()) >= 1) {
?>
    <!--  BEGIN Related Article -->

    <div class="row-fluid " style="margin-top: 10px;">
        <h3>Related Articles</h3>
        <div class="span12"><?

            $aArticle = $oArticle->GetArticleCollection()->Get();
            $limit = 4;

            for ($i=0;$i<$limit;$i++) {
                    if (is_object($aArticle[$i])) {
                            $aArticle[$i]->SetImgDisplay(FALSE);
                            $aArticle[$i]->LoadTemplate("article_related.php");
                            print $aArticle[$i]->Render();
                    }
            } ?>
    </div>
    </div>

    <!--  END Related Article -->
    <?php
    }
}
?>



</div>
</div>
<!--  END article_01 -->
