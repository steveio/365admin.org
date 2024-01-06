<table  id="report" class="display" cellspacing="2" cellpadding="0" border="0" width="" class="table table-striped">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col">Title</th>
<? if ($this->Get('RESULT_TYPE') == "PLACEMENT") { ?>
	<th scope="col">Company</th>
<? } ?>
	<th scope="col">Desc</th>
	<th scope="col">Url</th>
	<th scope="col">Listing</th>
<? if ($this->Get('RESULT_TYPE') == "COMPANY") { ?>	
	<th scope="col">Num Profile</th>
<? } ?>
	<th scope="col">Added</th>
	<th scope="col">Updated</th>
	<th scope="col">&nbsp;</th>
	<th scope="col">&nbsp;</th>
</tr>
</thead>
<tbody>
<? 
$i = 1;

$aResult = $this->Get('RESULT_ARRAY');

if ((is_array($aResult)) && (count($aResult) >= 1)) {
    foreach($aResult as $oResult) {
	?>
	<? $class = ($class == "hi") ? "" : "hi"; ?>
	<tr class='<?= $class ?>'>
		<td valign="top"><?= $i++ ?></td>
		<td valign="top"><?= $oResult->title; ?></td>
        <? if ($this->Get('RESULT_TYPE') == "PLACEMENT") { ?>
		<td valign="top"><a href="<?= $this->Get('WEBSITE_URL') . $oResult->comp_url_name ?>"><?= $oResult->comp_title ?></td>
        <? } ?>
		<td valign="top"><?= htmlspecialchars_decode(strip_tags($oResult->desc_short)); ?></td>
		<td valign="top"><a href="<?= $this->Get('WEBSITE_URL') . $oResult->url_name ?>"><?= $oResult->url_name ?></td>
		<td valign="top"><?= $oResult->listing; ?></td>
		<? if ($this->Get('RESULT_TYPE') == "COMPANY") { ?>	
		<td valign="top"><?= $oResult->num_profile; ?></td>
		<? } ?>
		<td valign="top"><?= $oResult->added; ?></td>
		<td valign="top"><?= $oResult->last_updated; ?></td>
		<td>
			<a class="btn btn-primary rounded-pill px-3" role="button" href="<?= $this->Get('WEBSITE_URL') . $oResult->url_name ?>" title="View">View</a>
		</td>		
		<td>
			<a class="btn btn-primary rounded-pill px-3" role="button"  href="<?= $oResult->url_name ?>/edit" title="Edit">Edit</a>
		</td>
	</tr>
<? 
	}
?>
<?
} else {
	print "<tr><td colspan=5>There are 0 results found.</tr>";
}
?>
</tbody>
</table>


<script>

$(document).ready(function() {

    $('#report').DataTable({
    	"pageLength": 100,
    	"bSort" : false
    });

});

</script>
