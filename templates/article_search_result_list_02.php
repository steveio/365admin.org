
<table cellspacing="2" cellpadding="4" border="0" width="400px">
<tr>
	<th>&nbsp;</th>
	<th>Title</th>
	<th align="right">Select</th>
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
		<td width="80px" valign="top"><a href='/article_edit.php?id=<?= $oArticle->GetId(); ?>' title='edit article (opens in new window)' target='_new'><?= $oArticle->GetTitle() ?></a></td>
		<td width="20px" align="right"><input type="checkbox" name="art_<?= $oArticle->GetId() ?>" value="true" /></td>
	</tr>
<? 
	}
?>
	<tr class="hi">
		<td colspan="10" align="right" width="800px">
		Remove Selected :
		<input type="submit" name="remove_article" value="Remove" />
		</td>
	</tr>
<?
} else {
	print "<tr><td colspan=5>There are 0 articles found.</tr>";
}
?>

</table>
