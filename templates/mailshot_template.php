

<h1>Mailshot Manager</h1>


<form enctype="multipart/form-data" name="" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">


<div class="row pad five">

<!-- 
	<div class="pad-tb">	
		<h2>Upload Company CSV</h2>
		<p2>Upload a CSV file containing one row per company ID</p2>
	</div>

	<script type="text/javascript">
		var maxFiles = 1;
		var gFiles = 0;
*		function addFile() {
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
	</script>

		
		<input type="hidden" name="mode" value="misc" />
		<input type="hidden" name="action" value="upload" />
		<input type="hidden" name="upload" value="1" />
		
		<input type="hidden" name="MAX_FILE_SIZE" value="<?= IMAGE_MAX_UPLOAD_SIZE ?>" />
		<table>
		<tbody id="files-root" style="border: 0;">
			<tr><td><input type="file" name="file[]" size="30"></td></tr>
		</table>
		<input type="submit" name="do_file_upload" value="Upload CSV">
		
</div>

 -->

</form>