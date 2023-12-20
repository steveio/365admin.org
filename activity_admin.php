<?php

ini_set('display_errors',0);
ini_set('log_errors', 1);
ini_set('error_log', '/www/vhosts/365admin.org/logs/365admin_error.log');
error_reporting(E_ALL & ~E_NOTICE & ~ E_STRICT);

require_once("./conf/config.php");
require_once("./init.php");
require_once("./conf/brand_config.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/client_login.php",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");


if ($oAuth->oUser->isAdmin) {
	
	$oActivity = new Activity($db);
	
	
	/* add an activity */
	if (isset($_POST['add_activity'])) {
	
		if ((strlen($_POST['activity_name']) < 1) || (strlen($_POST['activity_desc']) < 1)) $response['msg'] = "Error : New activity name and short description must be supplied.";
		if (strlen($_POST['activity_name']) > 40) $response['msg'] = "Error : Activity name should be 40 chars or less.";	
		if (strlen($_POST['activity_desc']) >256) $response['msg'] = "Error : Activity short description should be 256 chars or less.";	
		if ($oActivity->ActivityExists($_POST['activity_name'])) $response['msg'] = "Error : An activity with the name ".$_POST['activity_name']." already exists.";
		
		if (strlen($response['msg']) < 1) {
			if ($oActivity->Add($_POST['activity_name'],$_POST['activity_desc'],$_POST['category_id'])) {
				$response['msg'] = "Added new activity : ".$_POST['activity_name'];
				$_POST['activity_name'] = '';
				$_POST['activity_desc'] = '';
			} else {
				$response['msg'] = "Error : Something went wrong...";
			}
		}	 
	}
	
	/* update an activity */
	if (isset($_POST['update_activity'])) {
		
		if (!is_numeric($_POST['a_id'])) $response['msg'] = "Error : Select an activity to edit.";
		
		if (strlen($response['msg']) < 1) {
			if ($oActivity->Update($_POST['a_id'],$_POST['activity_name'],$_POST['activity_url_name'],$_POST['activity_desc'],$_POST['activity_img_url'],$_POST['category_id'])) {
				$response['msg'] = "Success : Updated Activity : ". $_POST['activity_name'];
			} else {
				$response['msg'] = "Error : An error occured updating activity ". $_POST['activity_name'];
			}
		}
		/* clear activity data */
		$_POST['activity_id'] = $_POST['a_id'];
		$sMode = "EDIT_ACTIVITY";
	}


	/* get an activity to edit */
	if ((isset($_POST['activity_admin']) && $_POST['activity_admin'] == "edit_activity") && (is_numeric($_POST['activity_id']))) {
		
		if (!is_numeric($_POST['activity_id'])) $response['msg'] = "Error : Select an activity to edit.";
	
		if (strlen($response['msg']) < 1) {
			$oRes = $oActivity->GetById($_POST['activity_id']);			
				
			$_POST['activity_id'] = $oRes->id;
			$_POST['activity_name'] = $oRes->name; 
			$_POST['activity_desc'] = $oRes->description;
			$_POST['category_id'] = $oRes->category_id;
			$sMode = "EDIT_ACTIVITY";
		}
	}


	/* delete an activity */
	if (isset($_POST['activity_admin']) && $_POST['activity_admin'] == "delete_activity") {

		if (!is_numeric($_POST['activity_id'])) $response['msg'] = "Error : Select an activity to delete.";
	
		if (strlen($response['msg']) < 1) {
			if ($oActivity->Delete($_POST['activity_id'])) {
				$response['msg'] = "Success : Deleted activity";
			} else {
				$response['msg'] = "Error : Something went wrong...";
			}
		}	 
	}
	
		
	
} // end is admin check



print $oHeader->Render();

?>

<form enctype="multipart/form-data" name="edit_website" id="edit_website" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">

<div id="container" style="float: left; width: 800px;">

<div id="msgtext" style="color: red;">
<?= $response['msg'];  ?>
</div>


<? if ($oAuth->oUser->isAdmin) { ?>

	<div class="boxItem" style="width: 300px;">
	<div id="activity_mgr">
	<div style="text-align: right;"><h1>Manage Activities :</h1> 
		<label>activity name: </label><input type="text" name="activity_name" style="width: 300px;" maxlength="40" value="<?= stripslashes($_POST['activity_name']); ?>" /><br />
		<label>short desc: </label><textarea name="activity_desc" style="width: 300px; height: 80px;"><?= stripslashes($_POST['activity_desc']); ?></textarea><br />
		<input type="hidden" name="a_id" value="<?= $_POST['activity_id']; ?>" />
		<input type="hidden" name="activity_url_name" value="<?= $_POST['activity_url_name']; ?>" />
		<label>category: </label>		
		<?
			$oCategory = new Category($db);
			print $oCategory->GetDDList("category_id",$_POST['category_id']);
		?>
		<br />
		<input type="submit" name="<?= $sLabel = ($sMode == "EDIT_ACTIVITY") ? "update_activity" : "add_activity"; ?>" value="<?= $sLabel = ($sMode == "EDIT_ACTIVITY") ? "Edit" : "Add"; ?>" />
	</div>
	<div style="text-align: right;"><p>Activity Admin:</p>
		<?= $oActivity->GetDDList(); ?><br />
		Edit<input type="radio" name="activity_admin" value="edit_activity" checked/><br />
		Delete<input type="radio" name="activity_admin" value="delete_activity" /><br />
		<input type="submit" name="admin_activity" value="go" />
	</div> 	
	</div><!-- end activity mgr -->
	</div><!-- end box item -->

	<? if ($sMode == "EDIT_ACTIVITY") {  ?>
	<div class="boxItem" style="width: 300px; margin-left: 20px;">
		<div id='activity_panel_item'>
		<? if (strlen($_POST['activity_img_url']) >1 ) {  ?>
		<div id='activity_panel_img'><a href='".$_CONFIG['url']."/"<?= $_POST['activity_url_name'] ?>"' title='activity : <?= $_POST['activity_name'] ?>'><img src='<?= $_POST['activity_img_url'] ?>' alt='<?= $_POST['activity_name'] ?>' width='140' height='100' border=0></a></div>
		<? }  ?>
		<a class='activity_panel_title' href='".$_CONFIG['url']."/"<?= $_POST['activity_url_name'] ?>"' title='activity : <?= $_POST['activity_name'] ?>'><?= $_POST['activity_name'] ?></a><br />
		<p class='activity_panel_desc'><?= $_POST['activity_desc'] ?></p>
		</div>
	</div><!-- end box item -->
	<? } ?>

	
	</div> <!--  end container -->

<? 
} else {
	print "<div class='msgtext'>ERROR : Only admin is permitted to manage websites.  Are you logged in as admin?</div>";
}
?>

</form>
<?
print $oFooter->Render();
?>
