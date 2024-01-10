<h2>Recent Activity</h2>

<form enctype="multipart/form-data" id="recent_activity" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">

<table  id="report" class="display" cellspacing="2" cellpadding="0" border="0" width="" class="table table-striped">
<thead>
<tr>
	<th scope="col">Type</th>
	<th scope="col">Title</th>
	<th scope="col">Url</th>
	<th scope="col">Last Updated</th>
	<th scope="col">Edit</th>
	<th scope="col">Delete</th>

</tr>
</thead>
<tbody>
<? 
$i = 1;

$aResult = $this->Get('RECENT_ACTIVITY_ARRAY');

if ((is_array($aResult)) && (count($aResult) >= 1)) {
    foreach($aResult as $oResult) {
	?>
	<tr class='<?= $class ?>'>
		<td valign="top"><?= $oResult->type; ?></td>
		<td valign="top"><?= $oResult->title; ?></td>
		<?php 
		if ($oResult->type == "ARTICLE")
		{
            if (strlen($oResult->url) < 1)
            {
                $oResult->url = "article.php?&id=".$oResult->id;
                $oResult->link = "article.php?&id=".$oResult->id;
            } else {
                $oResult->link = $this->Get('WEBSITE_URL').$oResult->url;
            }
                $oResult->edit_link = "/article-editor?&id=".$oResult->id;
		} else {
		    $oResult->link = $this->Get('WEBSITE_URL').$oResult->url;
		    $oResult->edit_link = $oResult->url."/edit";
		}
		?>
		<td valign="top"><a href="<?= $oResult->link; ?>" target="_new"><?= $oResult->url; ?></a></td>
		<td valign="top"><?= $oResult->last_updated; ?></td>
		<td>
			<a class="btn btn-primary rounded-pill px-3" target="_new" role="button"  href="<?= $oResult->edit_link ?>" title="Edit">Edit</a>
		</td>
		<td>
		<?php if ($oResult->type == "ARTICLE") { ?>
			<button id="delete" onclick="javscript: return confirm('Are you sure you wish to delete article: <?= $oResult->title ?>?');" name="art_<?= $oResult->id ?>" class="btn btn-primary rounded-pill px-3" type="submit" value="delete">delete</button>
		<?php } ?>			
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

</form>

<script>

$(document).ready(function() {

    $('#report').DataTable({
    	"pageLength": 100,
    	"bSort" : true
    });

});

</script>
