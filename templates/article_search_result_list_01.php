
<table cellspacing="2" cellpadding="4" border="0" class="table table-striped">
<thead>
<tr>
	<th>&nbsp;</th>
	<th>Title</th>
	<th>Created</th>
	<th>Published To</th>
	<th>&nbsp;</th>
	<th>Select</th>
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
		<td><?= $i++ ?></td>
		<td><?= $oArticle->GetTitle() ?></td>
		<td><?= $oArticle->GetCreatedDate() ?></td>
		<td><?= $oArticle->GetSectionUri() ?></td>
		<td><input class="btn btn-primary rounded-pill px-3" type="submit" onclick="javascript: window.open("<?= $oArticle->GetUrl(); ?>")" name="view_article" value="View" /></td>		
		<td><input class="form-check-input" type="checkbox" name="art_<?= $oArticle->GetId() ?>" value="true" /></td>
	</tr>
<? 
	}
?>
	<tr class="hi">
		<td colspan="10" align="right" width="800px">
		Attach Selected :
		<input class="btn btn-primary rounded-pill px-3" type="submit" name="attach_article" value="Attach" />
		</td>
	</tr>
<?
} else {
	print "<tr><td colspan=5>There are 0 articles found.</tr>";
}
?>
</tbody>
</table>
