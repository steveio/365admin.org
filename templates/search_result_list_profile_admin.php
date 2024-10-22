<table  id="report" class="display" cellspacing="2" cellpadding="0" border="0" width="" class="table table-striped">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col">Type</th>
	<th scope="col">Title</th>
	<th scope="col">Company</th>
	<th scope="col">Url</th>
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
		<td valign="top"><?= $oResult->content_type; ?></td>
		<td valign="top"><?= $oResult->title; ?></td>
		<td valign="top"><?= $oResult->company; ?></td>
		<td valign="top"><a href="<?= $this->Get('WEBSITE_URL') . $oResult->url ?>"><?= $oResult->url; ?></td>
		<td valign="top"><?= $oResult->added; ?></td>
		<td valign="top"><?= $oResult->last_updated; ?></td>
		<td>
			<a class="btn btn-primary rounded-pill px-3" role="button" href="<?= $this->Get('WEBSITE_URL') . $oResult->url ?>" title="View">View</a>
		</td>		
		<td>
			<a class="btn btn-primary rounded-pill px-3" role="button"  href="<?= $oResult->url ?>/edit" title="Edit">Edit</a>
		</td>
		<td>
			<button id="delete" class="btn btn-primary rounded-pill px-3" type="submit" value="delete"  onclick="deleteProfile('<?= $oResult->url ?>/delete'); return false;">delete</button>
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
    	"bSort" : true
    });

});

</script>
