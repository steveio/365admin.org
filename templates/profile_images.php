<div class="container">
<div class="align-items-center justify-content-center">
<div class="row">

	
<div class="row">
	<h1>Upload / Edit Images</h1>
</div>

<div class="row">

	<h2>Existing Images</h2>

	<?
	$aImage = $oProfile->GetImageByType();	
	if (is_array($aImage) && count($aImage) >= 1) {
		foreach($aImage as $oImage) {
			$type = ($oProfile->GetGeneralType() == PROFILE_COMPANY) ? "COMPANY" : "PLACEMENT";
			?>
			<div id="img_<?= $oImage->GetId() ?>" style="float: left; padding-right: 6px;">
			<a class="p_small" title="Remove Image" href="javascript: void(null);" onclick="javascript: RemoveImage('<?= $type; ?>',<?= $oProfile->GetId(); ?>,<?= $oImage->GetId() ?>)">[REMOVE]</a>
			<br />
			<?= $oImage->GetHtml("_s","") ?>
			</div>
			<?
		}
	} else {
	?>
	<p class="p_small">There are no images attached to this profile.</p>
	<?	
	}
	?>

</div>





<div class="row my-3">

	<div>	
		<h2>Upload Images</h2>
		<p2>Upload images from files stored on your computer.</p2>
	</div>

	<? if (is_array($oArticle->aImage) && count($oArticle->aImage) >= 1) { ?>
		<div class="row" style="width: 400px;">
		<h2>Existing Images :</h2>
		
		<div class="row my-2">
		<?
		if (count($oArticle->aImage) >= 1) {
			foreach($oArticle->aImage as $oImage) {
				?>
				<div id="img_<?= $oImage->GetId() ?>" style="float: left; padding-right: 6px;">
					<a class="p_small" title="Remove Image" href="javascript: void(null);" onclick="javascript: RemoveImage('<?= $_CONFIG['url'] ?>',<?= $oProfile->GetId() ?>,<?= $oImage->GetId() ?>)">[REMOVE]</a>
					<br />
					<?= $oImage->GetHtml("_s",$oProfile->GetTitle()) ?>
				</div>
				<?
			}
		}
		?>
		</div>
		</div>
	<? } ?>		
	<!-- MULTIPLE FILE UPLOAD -->
	<script type="text/javascript">
	<!--
		var maxFiles = 4;
		var gFiles = 0;
		function addFile() {
			if (gFiles < (maxFiles -1)) {
				var tr = document.createElement('tr');
				tr.setAttribute('id', 'file-' + gFiles);
				var td = document.createElement('td');
				td.innerHTML = '<input type="file" size="30" name="file[]"><span onclick="removeFile(\'file-' + gFiles + '\')" style="cursor:pointer;"> x Remove</span>';
				tr.appendChild(td);
				document.getElementById('files-root').appendChild(tr);
				gFiles++;
			} else {
				alert('Error: You can only upload '+maxFiles+' images per profile.');
			}
		}
		function removeFile(aId) {
			var obj = document.getElementById(aId);
			obj.parentNode.removeChild(obj);
			gFiles--;
		}
	-->
	</script>

	<div class="row my-2">
		<span onclick="addFile()" style="cursor:pointer;cursor:hand;">Add More +</span>

		<input type="hidden" name="mode" value="misc" />
		<input type="hidden" name="action" value="upload" />
		<input type="hidden" name="upload" value="1" />
		
		<input type="hidden" name="MAX_FILE_SIZE" value="<?= IMAGE_MAX_UPLOAD_SIZE ?>" />
		<table>
		<tbody id="files-root" style="border: 0;">
			<tr><td><input class="form-control" type="file" name="file[]" size="30"></td></tr>
		</table>
		
    	<div class="col-3 my-3">	
    		<input class="btn btn-primary rounded-pill px-3"  type="submit"  name="do_file_upload" value="Upload Image">
    		<p style="font-size:8pt;">allowed extensions are: <strong>JPG, JPEG PNG, GIF</strong>; max size per file: <strong>5mb</strong>; max number of files per upload <strong><?php echo $max_uploads; ?></strong></p>
    	</div>
	</div>
</div>
<!-- END Upload from FILE -->

</div>
</div>
</div>
