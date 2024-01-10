
<table cellspacing="2" cellpadding="4" border="0" class="table table-striped">
<tr>
	<th>&nbsp;</th>
	<th>Title</th>
	<th>Created</th>
	<th>Published To</th>
	<th>&nbsp;</th>
	<th>Select</th>
</tr>
<? 
$i = 1;

$aArticle = $this->Get('ARTICLE_ARRAY');

if ((is_array($aArticle)) && (count($aArticle) >= 1)) {
	foreach($aArticle as $oArticle) { 
	?>
	<? $class = ($class == "hi") ? "" : "hi"; ?>
	<tr class='<?= $class ?>'>
		<td width="20px" valign="top"><?= $i++ ?></td>
		<td width="80px" valign="top"><?= $oArticle->GetTitle() ?></td>
		<td width="80px" valign="top"><?= $oArticle->GetCreatedDate() ?></td>
		<td width="260px" valign="top"><?= $oArticle->GetMappingLabel() ?></td>
		<td width="20px"><input type="submit" onclick="javascript: go('./article.php?&id=<?= $oArticle->GetId() ?>'); return false;" name="art_<?= $oArticle->GetId() ?>" value="view" /></td>
		<td width="20px"><input type="checkbox" name="art_<?= $oArticle->GetId() ?>" value="true" /></td>
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

</table>
