<?php

require_once("./conf/config.php");
require_once("./init.php");
require_once("./conf/brand_config.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = "/",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");

$response = array();
$response['msg'] = "";
$response['status'] = "warning";
$oRefdataType = new RefdataType();
$aRefdataType = $oRefdataType->GetAll();

$iRefdataTypeId = isset($_POST['refdata_type']) ? $_POST['refdata_type'] : null;

$oRefdataTypeSelect = new Select('refdata_type_id','refdata_type','form-select',$aRefdataType,$bKeysSameAsValues = false,$iRefdataTypeId);

if (isset($_POST['insert_refdata']))
{
    $oRefdata = new Refdata(null);
    $oRefdata->SetType($_POST['refdata_type']);
    $oRefdata->SetName($_POST['refdata_name']);
    $bResult = $oRefdata->Insert();
    if ($bResult)
    {
        $response['status'] = "success";
        $response['msg'] = "Success - inserted new refdata: ".$newValue;
    } else {
        $response['msg'] = "Error - failed to insert refdata";
    }

}

if (isset($_POST['edit_refdata']) && is_numeric($_POST['refdata_id']))
{
    if (array_key_exists("refdata_id_".$_POST['refdata_id'], $_POST))
    {
        $newValue = $_POST["refdata_id_".$_POST['refdata_id']];
        $oRefdata = new Refdata(null);
        $oRefdata->SetId($_POST['refdata_id']);
        $oRefdata->SetName($newValue);
        $bResult = $oRefdata->Update();
        if ($bResult)
        {
            $response['status'] = "success";
            $response['msg'] = "Success - updated refdata: ".$newValue;
        } else {
            $response['msg'] = "Error - failed to update refdata";
        }
    }
}

if (is_numeric($iRefdataTypeId))
{
    $oRefdataList = new Refdata($_POST['refdata_type']);
    $aRefdataList = $oRefdataList->GetByType();
}


/*
print_r("<pre>");
print_r($_POST);
print_r("</pre>");
*/

print $oHeader->Render();

?>


<div class="container">
<div class="align-items-center justify-content-center">

<h1>Refdata Admin</h1>

<? if (isset($response['msg']) && strlen($response['msg']) >= 1) { ?>
<div id="msgtext" class="alert alert-<?= $response['status']; ?>" role="alert">
<?= $response['msg'];  ?>
</div>
<? } ?>


<form enctype="multipart/form-data" name="select_refdata_type" id="select_refdata_type" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">

<div class="row my-3">
	<h2>Select Refdata List</h2>
	<div class="col-3">
		<?= $oRefdataTypeSelect->GetHtml(); ?>
	</div>
    <div class="col-3">
		<button class="btn btn-primary rounded-pill px-3" type="submit" name="list_refdata" id="submit" value="Submit">submit</button>
    </div>

	</div>
</div>

</form>



<form enctype="multipart/form-data" name="edit_refdata" id="edit_refdata" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">

<input type="hidden" name="refdata_type" value="<?= $iRefdataTypeId; ?>" />
<input type="hidden" name="edit_refdata" value="true" />

<div class="row my-3">

<h2>View / Edit Refdata</h2>

<table id="report" cellspacing='0' cellpadding='0' border='0' class="table table-striped">
  <thead>
    <tr>
    <th scope="col">Id</th>
    <th scope="col">Value</th>
    <th scope="col">Edit</th>
    </tr>
  </thead>			
  <tbody>
    <?
    if ((is_array($aRefdataList)) && (count($aRefdataList) >= 1)) { 
        foreach($aRefdataList as $id => $value) {  
    ?>
    	<td><?= $id; ?></td>
    	<td><input id="refdata_id" name="refdata_id_<?= $id; ?>" class="form-control" value="<?= stripslashes($value); ?>"  /></td>
    	
    	<td>
			<button class="btn btn-primary rounded-pill px-3" type="submit" name="refdata_id" value="<?= $id ?>">update</button>
    	</td>
    	</tr>		
    <?  
    	} // end foreach
    } ?>
    </tbody>		
</table>

</div>
</form>


<form enctype="multipart/form-data" name="select_refdata_type" id="select_refdata_type" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">

<input type="hidden" name="insert_refdata" value="true" />

<div class="row my-3">
	<h2>Add New Refdata</h2>
	<div class="col-6">
    	<span class="label_col"><label for="" style="">Type<span class="red"> *</span></label></span>
    	<span class="input_col">
			<?= $oRefdataTypeSelect->GetHtml(); ?>
		</span>
	</div>
	
	<div class="col-6">
    	<span class="label_col"><label for="" style="">Name / Value<span class="red"> *</span></label></span>
    	<span class="input_col">
			<input id="refdata_name" name="refdata_name" class="form-control" value=""  />
		</span>
	</div>
	
    <div class="col-3 my-2">
		<button class="btn btn-primary rounded-pill px-3" type="submit" name="insert_refdata" id="submit" value="Submit">submit</button>
    </div>

	</div>
</div>

</form>

	
</div>
</div>


<?
print $oFooter->Render();
?>
