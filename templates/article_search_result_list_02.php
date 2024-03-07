<form enctype="multipart/form-data" id="recent_activity" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">

<table cellspacing="2" cellpadding="4" border="0" width="" class="table table-striped">
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
	<th scope="col">Select</th>

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
		<td width="20px" align="right"><input type="checkbox" name="art_<?= $oArticle->GetId() ?>" value="true" /></td>
	</tr>
<? 
	}
?>
	<tr class="hi">
		<td colspan="10" align="right" width="800px">
		Remove Selected :
		<input  class="btn btn-primary rounded-pill px-3" type="submit" name="remove_article" value="Remove" />
		</td>
	</tr>
<?
} else {
	print "<tr><td colspan=5>There are 0 articles found.</tr>";
}
?>
</tbody>
</table>
