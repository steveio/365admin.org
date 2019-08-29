<!-- BEGIN Page Content Container -->
<div class="page_content content-wrap clear">
<div class="row pad-tbl clear">


<div class="col five pad">

	
<div class="row five">
	<h1>Add / Edit Logo</h1>
	<p>Upload your organisation's logo to be displayed on your profiles.</p>
</div>


<!--BEGIN Existing Files -->
<div class="row pad" >

<div class="row five">	
	<h2>Current Logo: </h2>
	<? 
	$oImage = $oProfile->GetImage(0,LOGO_IMAGE);
	if (is_object($oImage)) {
		$existing_logo = TRUE;
	?>

	<div id="img_<?= $oImage->GetId() ?>" style="float: left; padding-right: 6px;">
		<table cellpadding="2" cellspacing="2" border="0">
			<tr>
				<th><p class="p_small">Small Size</p></th>
				<th><p class="p_small">Original Size</p></th>				
			</tr>
			<tr>
				<td><?= $oImage->GetHtml("_sm",$oProfile->GetTitle(),'',$outputSize = FALSE) ?></td>
				<td><?= $oImage->GetHtml("",$oProfile->GetTitle()) ?></td>
			</tr>
		</table>

		<a class="more-link p_small" title="Remove Image" href="javascript: void(null);" onclick="javascript: RemoveImage('<?= $_CONFIG['url'] ?>','COMPANY',<?= $oProfile->GetId() ?>,<?= $oImage->GetId() ?>)">Click here to delete existing logo</a>
	</div>

	
	<?
	} else {
	?>
	<p class="p_small">No logo uploaded.</p>
	<? } ?>
		
</div>

</div>
<!--END Existing Files -->



<!--BEGIN Upload from File -->
<div class="row five pad">


	<div class="pad-tb">
		<?
			$action = ($existing_logo) ? "Replace" : "Upload";
		?>	
		<h2><?= $action; ?> Logo</h2>
		<p2><?= $action; ?> logo from a file stored on your computer.</p2>
	</div>

	<input type="hidden" name="mode" value="misc" />
	<input type="hidden" name="action" value="upload" />
	<input type="hidden" name="upload" value="1" />
	
	<input type="hidden" name="MAX_FILE_SIZE" value="<?= IMAGE_MAX_UPLOAD_SIZE; ?>" />
	<table>
	<tbody id="logo-files-root">
		<tr><td><input type="file" name="logo[]" size="30"></td></tr>
	</table>
	<input type="submit" name="do_logo_upload" value="<?= $action; ?> Logo">
	<p class="p_small">
		allowed extensions are: <strong>JPG, JPEG PNG, GIF</strong>; max size per file: <strong>5mb</strong>; 
		<br />permitted dimensions (in pixels): width: <?= LOGO__DIMENSIONS_MINWIDTH ?>px to <?= LOGO__DIMENSIONS_MAXWIDTH ?>px, height: <?= LOGO__DIMENSIONS_MINHEIGHT ?>px to <?= LOGO__DIMENSIONS_MAXHEIGHT ?>px</strong>
	</p>
		
</div>
<!-- END Upload from FILE -->



</div>

</div>
</div>
<!-- END Page Content Container -->
