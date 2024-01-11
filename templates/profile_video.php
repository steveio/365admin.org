<div class="container">
<div class="align-items-center justify-content-center">
<div class="row">
	
<div class="row">
	<h1>YouTube Video</h1>
	<p>Attach a YouTube video to this profile.</p>
</div>

<!-- BEGIN YouTube  -->
<div class="row my-2">
	
	<div class="row">
		<span class="label_col"><label class="f_label" for="img_url4" class="f_label">YouTube Video <p class="p_small">(copy &amp; paste embeddable player code)</p></span></label></span>
		<span class="input_col">
			<textarea class="form-control" id="video_src" name="video" style="width: 360px; height: 200px;"><?= $oProfile->GetVideo(); ?></textarea>
		</span>
	</div>

	<div class="row">
		<span class="label_col">&nbsp;</span>
		<span class="input_col">
			<input class="btn btn-primary rounded-pill px-3" id="save_video_btn" type="submit" title="add / edit placement" name="save_video_btn" value="Submit" class="sub_col_but" />
		</span>
	</div>
	
	<div class="row my-2">
	<? if (strlen($oProfile->GetVideo()) >= 1) { ?>	
		<h2>Existing Video</h2>
		<?= $oProfile->GetVideo(); ?>
	<? } else { ?>
		<p class="p_small">There is no current video attached to this profile.</p>
	<? } ?>
	</div>
	
	<div class="row my-2">
		<p>To get the YouTube embed code ( see http://www.youtube.com/sharing or <a href="http://www.oneworld365.org/images/youtube_embedcode.png" title="YouTube howto screenshot" target="_new">view screenshot</a> ) -</p>
		<ul>
			<li>1.  View Video on YouTube</li>
			<li>2.  Click "share"</li>
			<li>3.  Click "embed"</li>
			<li>4.  Copy embed code eg. <br />&lt;iframe width="560" height="315" src="http://www.youtube.com/embed/nqNx6U8MhDo" frameborder="0" allowfullscreen&gt;&lt;/iframe&gt;</li>
		</ul>
	</div>

</div>
<!--  END YouTube -->

</div>
</div>
</div>
