<form enctype="multipart/form-data" id="recent_activity" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">

<table  id="report" class="display" cellspacing="2" cellpadding="4" border="0" width="" class="table table-striped">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col">Title</th>
	<th scope="col">Published To</th>
	<th scope="col">Created</th>
	<th scope="col">Last Updated</th>
	<th scope="col">&nbsp;</th>
	<th scope="col">&nbsp;</th>
	<th scope="col">&nbsp;</th>
	<th scope="col">&nbsp;</th>

</tr>
</thead>
<tbody>
<? 
$i = 1;

$aArticle = $this->Get('ARTICLE_ARRAY');

if ((is_array($aArticle)) && (count($aArticle) >= 1)) {
	foreach($aArticle as $oArticle) {
	?>
	<? $class = ($class == "hi") ? "" : "hi"; ?>
	<tr class='<?= $class ?>'>
		<td valign="top"><?= $i++ ?></td>
		<td valign="top"><?= $oArticle->GetTitle() ?></td>
		<td valign="top"><a href="<?= $oArticle->GetUrl() ?>"><?= $oArticle->GetRelativeUrl() ?></td>
		<td valign="top"><?= $oArticle->GetCreatedDate() ?></td>
		<td valign="top"><?= $oArticle->GetLastUpdated() ?></td>
		<td>
			<a class="btn btn-primary rounded-pill px-3" role="button" href="<?= $oArticle->GetUrl() ?>" title="View">View</a>
		</td>		
		<td>
			<a class="btn btn-primary rounded-pill px-3" role="button"  href="./article-editor?&id=<?= $oArticle->GetId() ?>" title="Edit">Edit</a>
		</td>
		<td>
			<a class="btn btn-primary rounded-pill px-3" role="button"  href="./article-publisher?&id=<?= $oArticle->GetId() ?>" title="Edit">Publish</a>
		</td>
		<td>
			<button id="delete" onclick="javscript: return confirm('Are you sure you wish to delete article: <?= $oArticle->GetTitle(); ?>?');" name="art_<?= $oArticle->GetId(); ?>" class="btn btn-primary rounded-pill px-3" type="submit" value="delete">delete</button>
		</td>		
	</tr>
<? 
	}
?>
<?
} else {
	print "<tr><td colspan=5>There are 0 articles found.</tr>";
}
?>
</tbody>
</table>

</form>

<script>

$(document).ready(function() {

    $('#report').DataTable({
    	"pageLength": 100,
    	"bSort" : false
    });

});

</script>
