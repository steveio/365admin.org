<?php


require_once("./conf/config.php");
require_once("./init.php");
require_once("./conf/brand_config.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/client_login.php",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");


if ($oAuth->oUser->isAdmin) {
	
	$oActivity = new Activity($db);
	$oCategory = new Category($db);	
	$oWebsite = new Website($db);
	
	$iSiteId = (is_numeric($_REQUEST['website_id'])) ? $_REQUEST['website_id'] : $_CONFIG['site_id']; 
	
	
	/* update category mappings */
	if (isset($_POST['update_website_category'])) {
		if (!Mapping::Update($bAdminRequired = true,$sTbl = "website_category_map",$sKey = "website_id",$iId = $iSiteId,$aFormValues = $_POST,$sFormKeyPrefix = "cat_",$sKey2 = "category_id")) {
			$response['msg'] = "Error : An problem occured updating the category mapping.  The dude has been notified...";
		} else {
			$response['msg'] = "Success : Updated website mappings.";
		}
	}
	
	/* update activity mappings */
	if (isset($_POST['update_website_activity'])) {
		if(!Mapping::Update($bAdminRequired = true,$sTbl = "website_activity_map",$sKey = "website_id",$iId = $iSiteId,$aFormValues = $_POST,$sFormKeyPrefix = "act_",$sKey2 = "activity_id")) {
			$response['msg'] = "Error : An problem occured updating the activity mapping.  The dude has been notified...";	
		} else {
			$response['msg'] = "Success : Updated website mappings.";
		}
	}
	
		
	
	$aSelected = $oCategory->GetSelected("website",$iSiteId);
	$sCatCheckBoxListHtml = $oCategory->GetCategoryLinkList($mode = "input",$aSelected,$slash = true,$all = true);
		
	$aSelected = $oActivity->GetSelected("website",$iSiteId);
	$sActivityCheckBoxListHtml = $oActivity->GetActivityLinkList($mode = "input",$aSelected,$slash = true,$all = true);

	
} // end is admin check


print $oHeader->Render();


?>

<form enctype="multipart/form-data" name="edit_website" id="edit_website" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">

<div class="container">
<div class="align-items-center justify-content-center">


<? if (isset($response['msg']) && strlen($response['msg']) >= 1) { ?>
<div id="msgtext" class="alert alert-warning" role="alert">
<?= $response['msg'];  ?>
</div>
<? } ?>



<? if ($oAuth->oUser->isAdmin) { ?>


	<h1>Website Admin :</h1>

	<div class="row">
	<?= $oWebsite->GetSiteDropDownList($iSiteId) ?>
	</div> 
	
	<div class="row">
		<p>Categories</p>
		<div class="boxItem">
		<?= $sCatCheckBoxListHtml ?> 
    		<div style="float: right;" >
        		<div style="text-align: right;">Update categories : <input type="submit" name="update_website_category" value="go" /></div>
    		</div>
		</div>
	</div>
	
	
	<div class="row">
		<p>Activities</p>
		<div class="boxItem">
		<?= $sActivityCheckBoxListHtml ?> 
    		<div style="float: right;" >
    			<div style="text-align: right;"><p>Update activities : <input type="submit" name="update_website_activity" value="go" /></p></div>
    		</div>
		</div>
	</div>


			
</div>
</div>

<? } ?>

</form>
<?


print $oFooter->Render();

?>