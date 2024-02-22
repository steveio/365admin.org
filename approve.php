<?php

require_once("./conf/config.php");
require_once("./init.php");
require_once("./conf/brand_config.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = "/",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");





/* retrieve account application id from url */

foreach($_REQUEST as $account_str => $v) { // qs = ?ac_1262=approve 
	if (preg_match("/ac_/",$account_str)) {
		$aBits = explode("_",$account_str);
		$id = $aBits[1];
		$mode = $v; /* approve | reject */ 
		if (!is_numeric($id)) die("Error : Oops something seems to have gone wrong");
		break;
	}
}


/* get details of the account pending approval */
$oAccountService = new AccountApplication();
$oAccount = $oAccountService->GetById($id);


$status = "PENDING";
$response = array();


if (isset($_POST['do_approve'])) {
	if ($oAccountService->Approve($oAccount,$oAccount->email,$oAccount->password,$response)) {
		$response['msg'] = "Success : Approved New User Account";
		$status = "APPROVED";
	}
}

if ($mode == "reject") {
	if ($oAccountService->Reject($oAccount,$response)) {
		$response['msg'] = "Success : Rejected User Account";
		$status = "REJECTED";
		unset($mode);
	} else {
		print $response['msg'];
	}
}


print $oHeader->Render();


?>

<div class="container">
<div class="align-items-center justify-content-center">



<div class="row">

<? if ($status == "PENDING") { ?>

<h1>Approve Account</h1>


<h2>Request Details</h2>

<table cellspacing='2' cellpadding='4' border='0' class="table table-striped">
<thead>
</thead>

<tbody>

<tr>
<th class="col-2">Name</th><td><?= $oAccount->name ?></td>
</tr>			

<tr>
<th>Role</th><td><?= $oAccount->role ?></td>
</tr>			

<tr>
<th>Email</th><td><?= $oAccount->email ?></td>
</tr>			

<tr>
<th>Tel</th><td><?= $oAccount->tel ?></td>
</tr>			

<tr>
<th>Country</th><td><?= $oAccount->country_name ?></td>
</tr>

<tr>
<th>Comments</th><td colspan="3"><?= $oAccount->comments ?></td>
</tr>
</tbody>		
</table>


<h2>Company Details</h2>

<table cellspacing='2' cellpadding='4' border='0' class="table table-striped">
<tr>
<th  class="col-2">Name</th><td><?= $oAccount->company_name ?></td>
</tr>
<tr>
<th>View</th><td><a href="<?= $_CONFIG['url']."/company/".$oAccount->comp_url_name."/edit/" ?>" target="_new" title="view company"><?= $_CONFIG['url']."/company/".$oAccount->comp_url_name; ?></a></td>
</tr>
<tr>
<th>Edit</th><td><a href="/company/<?= $oAccount->comp_url_name; ?>/edit/" target="_new" title="edit company">http://admin.oneworld365.org/company/<?= $oAccount->comp_url_name; ?>/edit/</a></td>
</tr>

<tr>
<th>Status</th><td><?= $oAccount->status ?></td>
</tr>


</table>

<h2>Username / Password</h2>

<? if (isset($response['msg']) && strlen($response['msg']) >= 1) { ?>
<div id="msgtext" class="alert alert-warning" role="alert">
<?= $response['msg'];  ?>
</div>
<? } ?>

<form enctype="multipart/form-data" name="" action="#" method="POST">
<input type="hidden" name="<?= $account_str ?>" value="<?= $mode ?>" />

<table cellspacing='0' cellpadding='4' border='0'  class="table table-striped">
<tr>
<td class="col-2">User Name: </td><td><?= $oAccount->email ?></td>
</tr>
<tr>	
<td>Password: </td><td><?= $oAccount->password ?></td>
</tr>
</table>

<div class="row">
	<div class="col-2">
	<button class="btn btn-primary rounded-pill px-3" type="submit" name="do_approve" id="submit" value="Confirm">Confirm</button>
	</div>
</div>

</form>	
<? } ?>



<? if ($status == "APPROVED") { ?>
<div class="alert alert-success" role="alert">
	<h1>Account Approved</h1>
	<p>A new account has been setup for <?= $oAccount->name ?> from <?= $oAccount->company_name ?>.<br /><br />
	Username : <?= $oAccount->email ?><br />
	Password : <?= $oAccount->password ?>
	</p>
</div>
<? } ?>


<? if ($status == "REJECTED") { ?>
<div class="alert alert-warning" role="alert">
	<h1>Account Rejected</h1>
	<p>Requested account for <?= $oAccount->name ?> from <?= $oAccount->company_name ?> has been rejected.</p>
</div>
<? } ?>


</div>


</div>
</div>

<?= $oFooter->Render();?>