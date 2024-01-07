<?php

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

<div class="container">
<div class="align-items-center justify-content-center">

<h1>Activity Admin</h1>

<? if (isset($response['msg']) && strlen($response['msg']) >= 1) { ?>
<div id="msgtext" class="alert alert-warning" role="alert">
<?= $response['msg'];  ?>
</div>
<? } ?>


<? if ($oAuth->oUser->isAdmin) { ?>

<div class="row">
	<h2>Add Activity</h2>
	<div class="row">
		<label>Activity name: </label>
		<input type="text" name="activity_name" class="form-control" maxlength="40" value="<?= stripslashes($_POST['activity_name']); ?>" />
	</div>
	<div class="row">	
		<label>Short desc: </label>
		<textarea name="activity_desc" class="form-control"><?= stripslashes($_POST['activity_desc']); ?></textarea>
	</div>
	<div class="row">
		<input type="hidden" name="a_id" value="<?= $_POST['activity_id']; ?>" />
		<input type="hidden" name="activity_url_name" value="<?= $_POST['activity_url_name']; ?>" />
		<label>category: </label>		
		<?
			$oCategory = new Category($db);
			print $oCategory->GetDDList("category_id",$_POST['category_id']);
		?>
	</div>
	<div class="row my-3">
	<div class="col-3">		
		<button class="btn btn-primary rounded-pill px-3" type="submit" name="<?= $sLabel = ($sMode == "EDIT_ACTIVITY") ? "update_activity" : "add_activity"; ?>" value="<?= $sLabel = ($sMode == "EDIT_ACTIVITY") ? "Edit" : "Add"; ?>">submit</button>
	</div>
	</div>
</div>

</form>


<form enctype="multipart/form-data" name="edit_website" id="edit_website" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">

<div class="row">

    <h2>Activity Admin</h2>
   	<div class="row">
		<?= $oActivity->GetDDList(); ?><br />
	</div>
	<div class="row my-3">
		<div class="col-3">
    		Edit<input type="radio" name="activity_admin" value="edit_activity" checked/>
    		Delete<input type="radio" name="activity_admin" value="delete_activity" />
		</div>
	</div> 	

	<div class="row my-3">
	<div class="col-3">		
		<button class="btn btn-primary rounded-pill px-3" type="submit" name="admin_activity" value="go">submit</button>
	</div>
	</div>

</div>


	
</div>
</div>

<? 
}
?>

<?
print $oFooter->Render();
?>
