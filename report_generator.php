<?php 

include_once("./includes/header_fullwidth.php");
include_once("./includes/footer.php");


print $oHeader->Render();


$arrTable = $db->getTableList();

print_r("<pre>");
print_r($_REQUEST);

if (isset($_REQUEST['report_tablelist']))
{
    $arrTableSchema = $db->getTableSchema($_REQUEST['report_tablelist']);

    //print_r($arrTableSchema);
}

print_r("</pre>");

?>

<div class="page_content content-wrap clear">
<div class="row pad-tbl clear">


<form enctype="multipart/form-data" name="report" id="report" action="" method="POST">

<label for="daterange">Select Table:</label>
<select id="" name="report_tablelist">
<?php 
foreach($arrTable as $aRow) {
?>
	<option value="<?= $aRow['table_name'] ?>" <?= ($_REQUEST['report_tablelist'] == $aRow['table_name']) ? "selected" : ""; ?>><?= $aRow['table_name'] ?></option>
<?php 
}
?>
</select>


<input type="submit" name="report_generate" value="go" onClick="this.form.submit()" />


<? if (strlen($strMessage) >= 1) { ?>
    <div style="font-weight: bold; margin: 20px 0px 20px 0px;">
    <h3><img src="/images/icon_green_tick.png" border="0" /><?= $strMessage; ?></h3>
    </div>
<?php } ?>

<?php 
if (isset($_REQUEST['report_tablelist']))
{
?>
<table id="report" class="display" cellspacing="2" cellpadding="4" border="0">	

<thead>
<tr>
<?php 
$aSchema = $arrTableSchema[0];

foreach($aSchema as $key => $val) { ?>
	<th><?= $key ?></th>
<?php 
} ?>
</tr>
</thead>

<tbody>
<? 
foreach($arrTableSchema as $idx => $aSchema) { ?>
<tr>
<?php 
    foreach($aSchema as $key => $val) { ?>
	<td width="" valign="top"><?= $val ?></td>
<?php 
    } ?>
</tr>
<? 
} ?>

</tbody>
</table>
<?php 
}
?>

</form>

</div>
</div>

<?php 

$strPHPClass = <<<'EOF'

class $classname {

    /**
     * $property_name
     *
     * @access protected
     * @var $property_type`
     */
    protected \$$property_name;
    
    /**
     * Constructor
     *
     */
    public function __construct() {
    }
    
    /**
     * Set $property_name
     *
     * @param $property_type $property_name
     */
    public function set$property_name(\$value) {
        \$this->$property_name = \$value;
    }

    /**
     * Get $property_name
     *
     */
    public function get$property_name() {
        if (isset(\$this->$property_name))
            return \$this->$property_name;
    }

}
EOF;



$strReportTemplate = <<<'EOF'

<?php

include_once("./includes/header_fullwidth.php");
include_once("./includes/footer.php");

if (!$oAuth->oUser->isValidUser || !$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");
    
    
$oReport = new $strClass();
    
if ($oAuth->oUser->isAdmin) {
    
	$aApprove = array();
	$aReject  = array();
    
	foreach($_REQUEST as $k => $v) {
		if (preg_match("/enq_/",$k)) {
			$id = preg_replace("/enq_/","",$k);
			if (isset($_REQUEST['bulk_action']) && $_REQUEST['bulk_action'] != "") {
				if ($_REQUEST['bulk_action'] == "approve") $aApprove[] = $id;
				if ($_REQUEST['bulk_action'] == "reject") $aReject[] = $id;
			} else {
				if ($v == "approve") $aApprove[] = $id;
				if ($v == "reject") $aReject[] = $id;
			}
		}
	}
				    
	$iApproved = 0;
	$iRejected = 0;
				    
	if (count($aApprove) >= 1) {
		foreach($aApprove as $id) {
			if ($oReview->SetStatus($id,1))
			    $iApproved++;
		}
	}
				    
	if (count($aReject) >= 1) {
		foreach($aReject as $id) {
			if ($oReview->SetStatus($id,2))
			    $iRejected++;
		}
	}
	$strMessage = "";
	if ($iApproved >= 1)
        $strMessage = "Approved ".$iApproved." row(s) \n";
    if ($iRejected >= 1)
        $strMessage .= "Rejected ".$iRejected." row(s) ";
				    
}
				    
$aOptions = array();
$aOptions['report_date_from'] = "";
$aOptions['report_date_to'] = "";
if (isset($_REQUEST['report_status']) && $_REQUEST['report_status'] != "ALL")
    $aOptions['report_status'] = $_REQUEST['report_status'];
        
if (!isset($_REQUEST['report_all']))
{
    $strDateRange = isset($_REQUEST['daterange']) ? $_REQUEST['daterange'] : date("d-m-Y",strtotime("-3 month"))." - ".date("d-m-Y");
    $aDate = explode(" - ", $strDateRange);
    $aOptions['report_date_from'] = preg_replace("/\//","-",$aDate[0]);
    $aOptions['report_date_to'] = preg_replace("/\//","-",$aDate[1]);
}
        
$aReport = $oReview->GetReport($aOptions);
        
        
print $oHeader->Render();
        
?>
        
<!-- BEGIN Page Content Container -->
<div class="">
<div class="">
        
        
<form name="report_filter" enctype="multipart/form-data" action="" method="POST">
        
<label for="daterange">Date range:</label>
<input type="text" name="daterange" value="<?= $strDateRange; ?>" />
        
<label for="daterange">Select all:</label>
<input type="checkbox" id="" name="report_all" <?= (isset($_REQUEST['report_all'])) ? "checked" : ""; ?>/>
    
<label for="daterange">By status:</label>
<select id="" name="report_status">
	<option value="ALL">ALL</option>
	<option value="0" <?= ($_REQUEST['report_status'] == "0") ? "selected" : ""; ?>>PENDING</option>
	<option value="1" <?= ($_REQUEST['report_status'] == "1") ? "selected" : ""; ?>>APPROVED</option>
	<option value="2"<?= ($_REQUEST['report_status'] == "2") ? "selected" : ""; ?>>REJECTED</option>
</select>
<input type="submit" name="report_filter" value="go" onClick="this.form.submit()" />
	    
	    
<? if (strlen($strMessage) >= 1) { ?>
    <div style="font-weight: bold; margin: 20px 0px 20px 0px;">
    <h3><img src="/images/icon_green_tick.png" border="0" /><?= $strMessage; ?></h3>
    </div>
<?php } ?>
	    
	    
<div id='' class="span12" style='margin-top: 40px;'>
	    
<h1>Reviews</h1>
	    
	    
<div style="clear: both;">
<div style="float: right; margin-bottom: 20px;">
		<select name="bulk_action">
			<option value="">select</option>
			<option value="approve">approve selected</option>
			<option value="reject">reject selected</option>
		</select>
		<input type="button" name="go_batch" value="go" onClick="this.form.submit()" />
</div>
</div>
	    
<table id="report" class="display" cellspacing="2" cellpadding="4" border="0">
	    
	<thead>
	<tr><?
$aRow = $aReport[0];
	    
$aKeys = array_keys($aRow);
foreach($aKeys as $idx => $key) { ?>
	<th><?= $key; ?></th><?
} ?>
	<th>edit</th>
	<th>approve</th>
	<th>reject</th>
	<th>bulk</th>
	</tr>
	</thead>
	    
	<tbody>
	    
<?
foreach($aReport as $aRow) { ?>
	<tr><?php
    foreach($aRow as $key => $value)
    { ?>
		<td id="<?= $key; ?>" valign="top"><?= $value; ?></td><?
    } ?>
	<td width="20px"><a href="../edit_review/?&id=<?= $aRow['post_id'] ?>" target="_new">edit</a></td>
	<td width="20px"><input type="submit" onclick="javascript: return confirm('Are you sure you wish to approve this review?');" name="enq_<?= $aRow['post_id'] ?>" value="approve" /></td>
	<td width="20px"><input type="submit" onclick="javascript: return confirm('Are you sure you wish to reject this review?');" name="enq_<?= $aRow['post_id'] ?>" value="reject" /></td>
	<td width="20px" valign="top"><input type="checkbox" id="enq_<?= $aRow['post_id'] ?>" name="enq_<?= $aRow['post_id'] ?>" value="approve" /></td>
	    
	</tr><?
} ?>
</tbody>
</table>
	    
</form>
	    
</div>
	    
	    
<script>
	    
$(document).ready(function() {
	    
    $(function() {
      $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        locale: {
            format: 'DD-MM-YYYY'
        }
      }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      });
    });
	    
    $('#report').DataTable({
    	"pageLength": 100,
    	"bSort" : false
    });
	    
});
	    
</script>
	    
</div>
</div>
<!-- END Page Content Container -->
	    
<?
print $oFooter->Render();
?>
	    
EOF;

?>