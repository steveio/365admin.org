<div class="profile-image-container col-12">
    <?php  
    if (is_array($oProfile->GetAllImages()) && count($oProfile->GetAllImages()) >= 1) 
    {
        $arrImage = $oProfile->GetAllImages();
	    $iImgCount = count($arrImage);
        $iDisplay = 3; ?>
        <div class="row">
        <?php
        for($i=0; $i<=$iDisplay; $i++) 
        {
            $oImage = isset($arrImage[$i]) ? $arrImage[$i] : null;
            if (is_null($oImage)) continue; ?>
           <div class="col-lg-6 col-md-12 mb-4 mb-lg-0"><?
            if (strlen($oImage->GetHtml("_lf","")) > 1) { ?>
                <?= $oImage->GetHtml("_lf",""); ?><?php
            } else { ?>
                <?= $oImage->GetHtml("_mf",""); ?><?php
            } ?>
		   </div> <?
        } ?>
		</div>
		<div class="row">
        <?php
        if (count($arrImage) > $iDisplay +1) { ?>
        
			<div id="image-viewall-lnk" class="float-end">
				<a href="#" id="image-viewall" class="btn btn-primary rounded-pill px-3">View All Images</a>
			</div>

            <div id="image-all" class="col-12 hide"><?php
            for($i=$iDisplay+1; $i<count($arrImage); $i++) 
            {
                $oImage = isset($arrImage[$i]) ? $arrImage[$i] : null;
                if (is_null($oImage)) continue; ?>
               <div class="col-lg-6 col-md-6 mb-4 mb-lg-0" style="float: left;"><?
                if (strlen($oImage->GetHtml("_lf","")) > 1) { ?>
                    <?= $oImage->GetHtml("_lf",""); ?><?php
                } else { ?>
                    <?= $oImage->GetHtml("_mf",""); ?><?php
                } ?>
			   </div> <?
            } ?>
            </div><?php 
        } ?>
        </div><?
    } ?>
    <script>
	$(document).ready(function(){ 
		$('#image-viewall').click(function(e) {
		   e.preventDefault();
	       $('#image-all').show();
	       $('#image-viewall-lnk').hide();
	       return false;
		});
	}); 
    </script>
</div>
