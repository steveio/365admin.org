<?php

require_once("./conf/config.php");
require_once("./init.php");
require_once("./conf/brand_config.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = "/",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");

$oCompany = new Company($db);
$oPlacement = new Placement($db);

$d['company_select_ddlist'] = $oCompany->getCompanyNameDropDown();


$oUser = new User($db);


// delete a user
if ($_GET['m'] == "del") {
	$id = $_GET['id'];
	$company_id = $_GET['p_company'];
	if ((is_numeric($id)) && (is_numeric($company_id))) {
		if ($oUser->deleteUser($id,$company_id)) {
			$response['msg'] = "Success : Deleted User Account";
		}
	}
}

// add a new user
if (isset($_POST['add_user'])) {
	$oUser->addUser();
	$response['msg'] = $oUser->msg;
}

// display edit user list
if (isset($_REQUEST['p_company'])) {
	$sUserEditListHTML = $oUser->getUserEditList($_REQUEST['p_company']);
}

/* account(s) pending */

$oAccount = new AccountApplication();
$aNewAccount = $oAccount->GetPendingList();
$aRecentAccount = $oAccount->GetRecentList();


/* recent activity */

$aCompany = $oCompany->GetCompanyList("RECENT",null,"OBJECTS");
$aPlacement = $oPlacement->GetPlacementById(null,$key = "recent",$ret_type = "objects");


print $oHeader->Render();


?>

<div class="container">
<div class="align-items-center justify-content-center">




<div id="edit_user">	

<? if (isset($response['msg']) && strlen($response['msg']) >= 1) { ?>
<div id="msgtext" class="alert alert-warning" role="alert">
<?= $response['msg'];  ?>
</div>
<? } ?>





<div class="row my-3">	

<h1>Add / Edit User</h1>
<p><i>Select company to edit existing users :</i></p>
<form action="#" id="user_edit_form" method="post">
	<div class="row">
		<div class="col-9">
            <?= $d['company_select_ddlist']; ?>
        </div>
        <div class="col-3">
    		<button class="btn btn-primary rounded-pill px-3" type="submit" name="list_user" id="submit" value="Submit">submit</button>
        </div>
	</div>
	<div class="row">
        <div id="user_edit_list" class="col-12">
                <?= $sUserEditListHTML ?>
        </div>
     </div>
</form>

<br />

<div id="user_form" class="row form-group my-3">

<p><i>Or add a new user account :</i></p>
<form action="#" id="add_user_form" method="post">
	<div class="row">
		<div class="col-6">
    		<label for="name">Name</label> 
	    	<input type="text" id="title" class="form-control" name="p_name" value="<?= $_POST['p_name']; ?>" />
        </div>
		<div class="col-6">        
            <label for="email">Email</label> 
            <input type="text" id="title" class="form-control" name="p_email" value="<?= $_POST['p_email']; ?>" />
        </div>
	</div>        
	<div class="row">
		<div class="col-6">
            <label for="uname">Username</label> 
            <input type="text" id="title" class="form-control" name="p_uname" value="<?= $_POST['p_uname']; ?>" />
		</div>
		<div class="col-6">
            <label for="pass">Password</label> 
            <input type="text" id="title" class="form-control" name="p_pass" value="<?= $_POST['p_pass']; ?>" />
		</div>
	</div>
	<div class="row">
        <label for="company">Company</label>
        <?= $d['company_select_ddlist']; ?>
    </div>
    <div class="row my-3">
    	<div class="col-2">
    		<button class="btn btn-primary rounded-pill px-3" type="submit" name="add_user" id="submit" value="Submit">submit</button>
    	</div>
   	</div>
    </div>
</form>
</div>

</div>



<form action="/approve" id="user_edit_form" method="get">

<div class="row">	

<h1>Approve New Account Requests</h1>

<table id="report" cellspacing='0' cellpadding='0' border='0' class="table table-striped">
  <thead>
    <tr>
    <th scope="col">&nbsp;</th>
    <th scope="col">Site</th>
    <th scope="col">Name</th>
    <th scope="col">Country</th>
    <th scope="col">Comp Name</th>
    <th scope="col">Comp Type</th>	
    <th scope="col">Type</th>
    <th scope="col">Comments</th>
    <th scope="col">Approve</th>
    <th scope="col">Reject</th>
    </tr>
  </thead>			
  <tbody>
    <?
    
    $i = 0;
    
    if ((is_array($aNewAccount)) && (count($aNewAccount) >= 1)) { 
    	foreach($aNewAccount as $oAccount) {
    		//$class = ($class == "hi") ? "" : "hi";  
    ?>
    	<tr class="<?= $class ?>">
    	<td width="10px"><?= ++$i; ?></td>
    	<td><?= $oAccount->website_name ?></td>
    	<td width="80px"><?= $oAccount->name ?> <br /><?= $oAccount->role ?><br /><?= $oAccount->email ?> <br />tel: <?= $oAccount->tel ?></td>
    	<td><?= $oAccount->country_name ?></td>
    	<td><a href="<?= $oAccount->company_profile_link ?>" target="_new" title="view company profile" ><?= $oAccount->company_name ?></a></td>
    	<td><?= $oAccount->company_type ?></td>
    	<td><?= $oAccount->account_name ?></td>
    	<td width="200px"><?= $oAccount->comments ?></td>
    	<td>
			<button class="btn btn-primary rounded-pill px-3" type="submit" name="ac_<?= $oAccount->id ?>" value="approve">approve</button>
    	</td>
    	<td>
    		<button class="btn btn-primary rounded-pill px-3" type="submit" onclick="javscript: return confirm('Are you sure you wish to reject this application?');" name="ac_<?= $oAccount->id ?>" value="reject">reject</button>
    	</td>
    	</tr>		
    <?  
    	} // end foreach
    } else { 
    
    ?>
    	<tr><td colspan='6'>There are 0 accounts waiting to be approved.</td></tr>
    <? } ?>
    </tbody>		
</table>

</div>
</form>

<div class="row my-3">

<div id="recent_account_list" class="table-border">

<h1>Recent Account Requests</h1>

<table id="report" cellspacing='0' cellpadding='0' border='0' class="table table-striped">

<thead>

    <tr>
    <th>&nbsp;</th>
    <th>Site</th>
    <th>Name</th>
    <th>Comp Name</th>
    <th>Comp Approved</th>
    <th>Comments</th>
    <th>Account</th>
    <th>Account Status</th>
    <th>Recieved Date</th>
    </tr>
</thead>
<tbody>
<?

$i = 0;


foreach($aRecentAccount as $oAccount) {
        $class = ($class == "hi") ? "" : "hi";
?>
        <tr class="<?= $class ?>">
        <td><?= ++$i; ?></td>
        <td><?= $oAccount->website_name; ?></td>
        <td><?= $oAccount->name ?><br /><?= $oAccount->role ?><br /><?= $oAccount->country_name ?><br /><?= $oAccount->email ?><br />tel: <?= $oAccount->tel ?></td>
        <td><a href="<?= $oAccount->company_profile_link ?>" target="_new" title="view company profile" ><?= $oAccount->company_name ?></a><br /><?= $oAccount->company_type ?></td>                 
        <td><?= ($oAccount->comp_status == 1) ? "Yes" : "No"; ?></td>
        <td width="200px"><?= $oAccount->comments ?></td>
        <td><?= $oAccount->account_name ?></td>
        <td><?= $oAccount->status ?></td>
        <td><?= $oAccount->receieved ?></td>
        </tr>
<?
        } // end foreach
?>
</tbody>
</table>

</div>



</div>


<script>

$(document).ready(function() {
	
    $('#report').DataTable({
    	"pageLength": 100,
    	"bSort" : true
    });

});

</script>


<?php 
print $oFooter->Render();
?>
