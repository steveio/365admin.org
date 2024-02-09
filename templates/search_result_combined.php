<?php

$aArticle = $this->Get('aArticle');
$aProfile = $this->Get('aProfile');

?>


<div class="container">
<div class="align-items-center justify-content-center">


<div class="row">

<?php

for($i=0; $i<= count($aArticle); $i++)
{
?>
<div class="row">
	<div class="col-8">

    	<div class="col">
    	<?
    	$oArticle = array_shift($aArticle);

    	//if (!is_object($oArticle)) continue;
    	
    	if (is_object($oArticle->GetImage(0)) && $oArticle->GetImage(0)->GetHtml("_l",'')) {
    		print $oArticle->GetImage(0)->GetHtml("_l",$oArticle->GetTitle());
    	} elseif (is_object($oArticle->GetImage(0)) && $oArticle->GetImage(0)->GetHtml("_mf",'')) {
    		print $oArticle->GetImage(0)->GetHtml("_mf",$oArticle->GetTitle());
    	}
    	?>
    	</div>
    
    	<div class="col-9 my-3">
            <h1><a href="<?= $oArticle->GetUrl(); ?>" title="<?= $oArticle->GetTitle(); ?>"><?= $oArticle->GetTitle(); ?></a></h1>
        	<p><?= htmlUtils::convertToPlainText($oArticle->GetDescShort()); ?></p>
        	<p><small class="text-muted"><?= $oArticle->GetLastUpdated(); ?></small></p>
    	</div>

	</div>

    <div class="col-3">
    
    <?php 
    if (is_array($aProfile))
    {
    	for($i=0; $i<2; $i++)
    	{
        	$oProfile = array_shift($aProfile);
        	if (!is_object($oProfile)) continue;

        	?>
            <div class="row">
            	<div class="col-4">
            	<?
            	if (is_object($oProfile->GetImage(0,LOGO_IMAGE))) {
            	    
            	    print $oProfile->GetImage(0,LOGO_IMAGE)->GetHtml("_sm",$oProfile->GetTitle());
            	
            	} elseif (is_object($oProfile->GetImage(0)) && $oProfile->GetImage(0)->GetHtml("_mf",'')) {
            	    print $oProfile->GetImage(0)->GetHtml("_mf",$oProfile->GetTitle());
            	}
            	?>
            	</div>
           	</div>
           	<div class="row">
            	<div class="col-9 my-2">
            	<h1><a href="<?= $oProfile->GetProfileUrl(); ?>" title="<?= $oProfile->GetTitle(); ?>"><?= $oProfile->GetTitle(); ?></a></h1>
            	<p><?= htmlUtils::convertToPlainText($oProfile->GetDescShort()); ?></p>
            	</div>
            </div><?php 
    	} 
    } 
    ?>
    </div>

</div><?php 
}
?>



</div>
</div>
</div>