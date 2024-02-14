<div id="review" class="row">
	<ul class="nav nav-tabs">
	  <li class="nav-item"><a class="nav-link <?= ($this->Get('HAS_REVIEW')) ? "active" : ""; ?>" id="review-display-lnk" data-toggle="tab" href="#review-display">Reviews</a></li>
	  <li class="nav-item"><a class="nav-link <?= (!$this->Get('HAS_REVIEW')) ? "active" : ""; ?>" id="review-add-lnk" data-toggle="tab" href="#review-add">Add a review</a></li>
	</ul>
	<div class="tab-content">
		<div id="review-display" class="">
			<div class="row-fluid" ><?php
				$iLimit = 5;
				$aReview = is_array($this->Get('REVIEWS')) ? $this->Get('REVIEWS') : array();
				for($i=0;$i<5;$i++) { 
				    $oReview = isset($aReview[$i]) ? $aReview[$i] : null; 
				    if (!is_object($oReview)) continue; ?> 
					<div class="col span12" style="margin-bottom: 20px;">
						<div class="span12">
							<h3><?= html_entity_decode($oReview->GetTitle()); ?></h3>
							<div id="rateYo-<?= $oReview->GetId(); ?>" style="margin-bottom: 10px;"></div>
							<p><?= nl2br(html_entity_decode($oReview->GetReview())); ?></p>
						</div>
						<div class="span12" style="font-size: 0.8em;">
							By: <?= $oReview->GetName(); ?><br /> 
							Nationality: <?= html_entity_decode($oReview->GetNationality()); ?><br /> 
							Age: <?= $oReview->GetAge(); ?><br />
						</div>
					</div><?php 
				}
				if (count($aReview) == 0) { ?>
					<div class="row my-3">
					<p>There are no reviews, click 'Add a review' to submit one </p>
					</div>
				<?php 
				} ?>
			</div><?php
			if (count($aReview) >=5)
				{ ?>
    		<div class="my-2">
    			<a href="#" class="btn btn-primary rounded-pill px-3" id="review-viewall">View All Reviews</a>
    		</div>
    		<div id="review-more" class="row" style="display: none;"><?php
    			for($i=5;$i<count($aReview);$i++) { 
    			    $oReview = isset($aReview[$i]) ? $aReview[$i] : null; 
    			    if (!is_object($oReview)) continue; ?> 
    				<div class="col-4 my-2">
    					<div class="col-12">
    						<h3><?= html_entity_decode($oReview->GetTitle()); ?></h3>
    						<div id="rateYo-<?= $oReview->GetId(); ?>" style="margin-bottom: 10px;"></div>
    						<p><?= nl2br(html_entity_decode($oReview->GetReview())); ?></p>
    					</div>
    					<div class="col-12" style="font-size: 0.8em;">
    						By: <?= $oReview->GetName(); ?><br /> 
    						Nationality: <?= html_entity_decode($oReview->GetNationality()); ?><br /> 
    						Age: <?= $oReview->GetAge(); ?><br />
    					</div>
    				</div><?php 
    			 } ?>
    		</div><?php
    		} ?>			

            <script>
            	$(document).ready(function(){ 
            	
            		<?php
            		    $aReview = is_array($this->Get('REVIEWS')) ? $this->Get('REVIEWS') : array();
            			foreach($aReview as $oReview) { ?>
            				$("#rateYo-<?= $oReview->GetId(); ?>").rateYo({
            					 rating: <?= $oReview->GetRating(); ?>,
            					 fullStar: true,
            					 readOnly: true
            				}); <?php 
               			}
            			if ($this->Get('HASREVIEWRATING')) { ?>
            			    $("#review-overallrating").rateYo({
            				     rating: <?= $this->Get('REVIEWRATING') ?>,
            					 fullStar: true,
            					 readOnly: true
            				}); <?php 
            			} ?>            	
            	});
            </script>
		</div>
		
    	 <div id="review-add" class="col-12 tab-panel my-3" style="display: none;">
    
    		<p>Have you booked <?= $this->Get('LINK_NAME'); ?>? Please share your experience and submit your review.</p>
    		
    		<div id="review-error" class="col-12 alert alert-warning" style="display: none;"></div>
    		<div id="review-msg" class="col-12 alert alert-success" style="display: none;"></div>
    
    		<div id="review-add-form">
    			<form enctype="multipart/form-data" id="review-form" name="review-form" action="#" method="POST" class="form">
    		
    			<input type="hidden" id="review-link-id" name="review-link_id" value="<?= $this->Get('LINK_ID'); ?>" class="form-control" />
    			<input type="hidden" id="review-link-to" name="review-link_to" value="<?= $this->Get('LINK_TO'); ?>" class="form-control" />

    		  	<div class="row form-group my-2">    		
        		  	<div class="col-6">
        				<label for="review-name">Name:</label>
        				<input type="text" id="review-name" name="review-name"  maxlength="45" class="form-control" />
        			</div>
        		
        		  	<div class="col-6">
        				<label for="review-email">Email:</label>
        				<input type="text" id="review-email" name="review-email"  maxlength="50" class="form-control" />
        			</div>
				</div>

    		  	<div class="row form-group my-2">
    		
        		  	<div class="col-6">
        				<label for="review-nationality">Nationality:</label>
        				<input type="text" id="review-nationality" name="review-nationality"  maxlength="32" class="form-control" />
        			</div>
    			
        		  	<div class="col-3">
        				<label for="review-age">Age:</label>
        				<select id="review-age" name="review-age" class="form-select">
        					<option value="NULL"></option>
        					<?php for ($i=14;$i<100;$i++) { ?>
        						<option value="<?= $i; ?>"><?= $i ?></option>
        					<?php } ?>
        				</select>
        			</div>
    		
        		  	<div class="col-3">
        				<label for="review-gender">Gender:</label>
        				<select id="review-gender" name="review-gender" class="form-select">
        					<option value="NULL"></option>
        					<option value="M">Male</option>
        					<option value="F">Female</option>
        				</select>
        			</div>
				</div>
				
    		  	<div class="row form-group my-2">
    				<label for="review-title">Review Title:</label>
    				<input type="text" id="review-title" name="review-title" maxlength="128" class="form-control" />
    			</div>
    	
    		  	<div class="form-group my-2">
    				<label for="review-review">Review:</label>
    				<textarea id="review-review" name="review-review" class="form-control" rows="3" /><?= stripslashes($_POST['review-review']); ?></textarea>
    			</div>
    		
    		  	<div class="form-group my-2">
    				<label for="review-rating">Rating:</label>
    				<div id="rateYo"></div>
    			</div>
    			
    			<div class="form-group span12" style="margin-top: 20px;">
    				<button class="btn btn-primary rounded-pill px-3" id="review-btn" type="submit" value="submit" name="review-submit">Submit</button>
    			</div>

    			</form>
    		</div>	
		</div>
	</div>
</div>
