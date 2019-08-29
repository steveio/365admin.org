
<!--  BEGIN advertise signup -->
<div class="page_content content-wrap clear">
<div class="row pad-tbl clear">



<? if ($sDisplay == "PAYPAL_PROCESS") { 
	include("./paypal_process.php");
} ?>




<? if ($sDisplay == "SIGNUP_FORM") { ?>


	<div class="col four-sm clear"> 

	<h1><?= ucfirst($_CONFIG['site_title']) ?> Advertising</h1>

	<? if ($mode == "EXISTING") { ?>
				
		<p>If you are a representative of <?= stripslashes($oCompany->title) ?> you can apply to edit / upgrade your profile.</p> 
		
	<? } ?>
	
	<?
		switch($_CONFIG['site_id']) {

			case "2" : 
				include("./templates/advertise_ad_panel_seasonal.php");
				break;
			
			default : 
				include("./templates/advertise_ad_panel.php");
				break;
			
		}
	
	
	?>
	
	
	</div>
	
	<div class="col four-sm clear">

	<div id="msgtext" style="color: red; font-size: 10px;">
	<?= AppError::GetErrorHtml($response['msg']);  ?>
	</div>

	<div class="col four-sm clear">
	<h1>Your Details</h1>
	</div>
	
	<p><span class="red">(* indicates mandatory field)</span></p>
	
	<form enctype="multipart/form-data" name="" id="" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">
	<input type="hidden" name="company_id" value="<?= $oCompany->id ?>" />
	<input type="hidden" name="m" value="<?= $mode ?>" />
	<input type="hidden" name="prod_type" value="0" />

	
	<div class="row800">
		<span class="label_col"><label for="name" style="<?= strlen($response['msg']['name']) > 1 ? "color:red;" : ""; ?>">Your Name</label><span class="red"> *</span></span>
		<span class="input_col"><input type="text" id="name" name="name" style="width: 160px" maxlength="40" value="<?= $_POST['name']; ?>" /></span>
	</div> 
	
	<div class="row800">
		<span class="label_col"><label for="name" style="<?= strlen($response['msg']['role']) > 1 ? "color:red;" : ""; ?>">Position / Role</label><span class="red"> *</span></span>
		<span class="input_col"><input type="text" id="role" name="role" style="width: 160px" maxlength="40" value="<?= $_POST['role']; ?>" /></span>
	</div> 
	
	<div class="row800">
		<span class="label_col"><label for="email" style="<?= strlen($response['msg']['email']) > 1 ? "color:red;" : ""; ?>">Email Address</label><span class="red"> *</span></span>
		<span class="input_col"><input type="text" id="email" name="email" style="width: 160px" maxlength="40" value="<?= $_POST['email']; ?>" /></span>
	</div> 

	<div class="row800">
		<span class="label_col"><label for="password" style="<?= strlen($response['msg']['password']) > 1 ? "color:red;" : ""; ?>">Password</label><span class="red"> *</span></span>
		<span class="input_col"><input type="password" id="password" name="password" style="width: 160px" maxlength="20" value="<?= $_POST['password']; ?>" /></span>
	</div> 

	<div class="row800">
		<span class="label_col"><label for="password_confirm" style="<?= strlen($response['msg']['password_confirm']) > 1 ? "color:red;" : ""; ?>">Password Confirm</label><span class="red"> *</span></span>
		<span class="input_col"><input type="password" id="password_confirm" name="password_confirm" style="width: 160px" maxlength="20" value="<?= $_POST['password_confirm']; ?>" /></span>
	</div> 
	
	<div class="row800">
		<span class="label_col"><label for="tel" style="<?= strlen($response['msg']['tel']) > 1 ? "color:red;" : ""; ?>">Contact Tel</label><span class="red"> *</span></span>
		<span class="input_col"><input type="text" id="tel" name="tel" style="width: 160px" maxlength="40" value="<?= $_POST['tel']; ?>" /></span>
	</div> 
	
	<div class="row800">
		<span class="label_col"><label for="country" style="<?= strlen($response['msg']['country_applicant']) > 1 ? "color:red;" : ""; ?>">Country</label><span class="red"> *</span></span>
		<span class="input_col"><?= $sCountryListHTML ?></span>
	</div> 

	<div class="row800">
		<span class="label_col"><label for="comments" style="<?= strlen($response['msg']['comments']) > 1 ? "color:red;" : ""; ?>">Comments :</label></span>
		<span class="input_col"><textarea id="comments" name="comments" maxlength="1999" style="width: 300px; height: 60px;" /><?= stripslashes($_POST['comments']); ?></textarea></span>
	</div> 
	

	<div class="col four-sm clear">
		<div class="col four-sm clear">
		<h1>Advertising Options</h1>
		</div>
				
		<span class="label_col"><label for="listing_type" style="<?= strlen($response['msg']['listing_type']) > 1 ? "color:red;" : ""; ?>">Listing Type :</label><span class="red"> *</span></span>
		<span class="input_col">
			<select name="listing_type">
				<option value="null"></option>
				<? 
				foreach($aListingOption as $key => $value) {
					$selected = (in_array($key,$_POST)) ? "selected" : ""; 
					$label = "&pound;".$value['price'];
					if ($key == "FREE") $label = "";
				?>
					<option value="<?= $key ?>" <?= $selected ?>><?= $value['label'] ?> <?= $label ?></option>
				<? } ?>
			</select>
		
		</span>
	</div>




	<? if ($mode == "NEW") { ?>

	<div class="col four-sm clear">
		<h1>Organisation Info</h1>
		
		<span class="label_col"><label for="title" style="<?= strlen($response['msg']['title']) > 1 ? "color:red;" : ""; ?>">Title<span class="red"> *</span></label></span>
		<span class="input_col"><input type="text" id="title" maxlength="99" style="width: 240px;"  name="title" value="<?= $_POST['title']; ?>" /></span>
	</div>
	
	<div class="row800">
		<span class="label_col"><label for="desc_short" style="<?= strlen($response['msg']['desc_short']) > 1 ? "color:red;" : ""; ?>">Short Description<span class="red"> *</span><br/>(300 chars or less)</label></span>
		<span class="input_col"><textarea id="desc_short" name="desc_short" style="width: 300px; height: 60px;" /><?= stripslashes($_POST['desc_short']); ?></textarea></span>
	</div> 

	<div class="row800">
		<span class="label_col"><label for="desc_long" style="<?= strlen($response['msg']['desc_long']) > 1 ? "color:red;" : ""; ?>">Full Description<span class="red"> *</span></label></span>
		<span class="input_col"><textarea id="desc_long" name="desc_long" style="width: 300px; height: 60px;" /><?= stripslashes($_POST['desc_long']); ?></textarea></span>
	</div> 

	
	<div class="row800">
		<span class="label_col"><label for="url" style="<?= strlen($response['msg']['url']) > 1 ? "color:red;" : ""; ?>">Website Url <span class="red">*</span></label></span>
		<span class="input_col"><input type="text" id="url" name="url" style="width: 240px;" maxlength="255" class="text_input" value="<?= $_POST['url']; ?>" /></span>
	</div> 
	
	<div class="row800">
		<span class="label_col"><label for="email" style="<?= strlen($response['msg']['comp_email']) > 1 ? "color:red;" : ""; ?>">Booking / Enquiry Email <span class="red">*</span></label></span>
		<span class="input_col"><input type="text" id="comp_email" style="width: 240px;" maxlength="59" name="comp_email" class="text_input" value="<?= $_POST['comp_email']; ?>" /></span>
	</div> 

	<div class="row800">
		<span class="label_col"><label for="email" style="<?= strlen($response['msg']['logo_url']) > 1 ? "color:red;" : ""; ?>">Logo Url</label></span>
		<span class="input_col"><input type="text" id="logo_url" class="text_input" maxlength="255" name="logo_url" value="<?= $_POST['logo_url'] ?>" />
		<span class='p_small'><i>(width less than 300px .gif/.jpg/.png)</i></span>
		</span>
	</div> 
	
	<div class="row800">
		<span class="label_col"><label class="l2" for="img_url1" class="f_label">Photo Url #1</label></span>
		<span class="input_col"><input type="text" id="img_url1" class="text_input" name="img_url1" maxlength="255" value="<?= $_POST['img_url1'] ?>" />
		<span class='p_small'><i>(.gif/.jpg/.png)</i></span>
		</span>
	</div>

	<div class="row800">
		<span class="label_col"><label class="l2" for="img_url2" class="f_label">Photo Url #2</label></span>
		<span class="input_col"><input type="text" id="img_url2" class="text_input" name="img_url2" maxlength="255" value="<?= $_POST['img_url2'] ?>" />
		<span class='p_small'><i>(.gif/.jpg/.png)</i></span>
		</span>
	</div>
	


	<div id="category_mgr" style="display: block;">
	
	<div style="width: 100px;">
		<span class="label_col"><h1 style="<?= strlen($response['msg']['category']) > 1 ? "color:red;" : ""; ?>">Categories<span class="red"> *</span></h1></span>
		<span class="input_col"><div class="select_list"><?= $d['CATEGORY_LIST'] ?></div></span>
	</div>
	
	<div  style="width: 100px;">
		<span class="label_col"><h1 style="<?= strlen($response['msg']['activity']) > 1 ? "color:red;" : ""; ?>">Activities<span class="red"> *</span></h1></span>
		<span class="input_col"><div class="select_list" ><?= $d['ACTIVITY_LIST'] ?></div></span>
	</div> 
	
	<div  style="width: 100px;">
		<span class="label_col"><h1 style="<?= strlen($response['msg']['country']) > 1 ? "color:red;" : ""; ?>">Countries<span class="red"> *</span></h1></span>
		<span class="input_col"><div class="select_list"><?= $d['COUNTRY_LIST'] ?></div></span>
	</div> 
	
	</div> <!-- category_mgr -->

	
	<? } // end new company ?>

	<div class="col four-sm clear pad-t">
		<p>Once we have approved your request login details will be sent to you by email.</p>
	</div>
	
	
	<div class="row800">
		<span class="label_col">&nbsp;</span>
		<span class="input_col"><input type="submit" name="submit" id="submit" value="Submit" />
		</span>
	</div>

	</div>
<? } ?>

</form>


<? if ($sDisplay == "THANKYOU_MSG") { ?>


	<div class="col four-sm clear">
	
	<h1>Order Confirmation</h1>

	<? if(isset($_REQUEST['payment_status'])) { ?>
		<p>Thankyou for your order :</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b><?= $_REQUEST['item_name'] ?> &pound;<?= $_REQUEST['mc_gross'] ?></b></p>
		<p>An account will be setup and we  will notify you by email with your login details.</p>
		<p>Please email <?= $_CONFIG['admin_email'] ?> if you require any further assistance.</p> 
	<? } else { ?>
		<p>Your request for an account has been sent for approval.  Once reviewed we will notify you by email with instructions detailing how to login and update your profile.</p>
	<? } ?>

	<p>Thanks,</p>
	
	<p><?= $_CONFIG['site_title'] ?></p>

	</div>

<? } ?>



</div>
</div>
<!--  END advertise signup -->