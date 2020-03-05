
<table cellspacing="2" cellpadding="4" border="0" width="800px">
<tr>
	<th>&nbsp;</th>
	<th>Title</th>
	<th>Created</th>
	<th>Published To</th>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
	<th>&nbsp;</th>

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
		<td width="260px" valign="top"><a href="<?= $oArticle->GetUrl() ?>"><?= $oArticle->GetRelativeUrl() ?></td>
		<td width="20px">
			<!-- <input type="submit" onclick="javascript: window.location = './article?&id=<?= $oArticle->GetId() ?>'; return false;" name="art_<?= $oArticle->GetId() ?>" value="view" /> -->
			<a href="<?= $oArticle->GetUrl() ?>" title="View">View</a>
		</td>		
		<td width="20px">
			<!-- <input type="submit" onclick="javascript: window.location = './article-editor?&id=<?= $oArticle->GetId() ?>'; return false;" name="art_<?= $oArticle->GetId() ?>" value="edit" /> -->
			<a href="./article-editor?&id=<?= $oArticle->GetId() ?>" title="Edit">Edit</a>
		</td>
		<td width="20px">
			<!-- <input type="submit" onclick="javascript: window.location = './article-publisher?&id=<?= $oArticle->GetId() ?>'; return false;" name="art_<?= $oArticle->GetId() ?>" value="publish" /> -->
			<a href="./article-publisher?&id=<?= $oArticle->GetId() ?>" title="Edit">Publish</a>
		</td>
		<td width="20px">
			<input type="submit" onclick="javscript: return confirm('Are you sure you wish to delete this article?');" name="art_<?= $oArticle->GetId() ?>" value="delete" />
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

</table>
