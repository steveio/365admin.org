<h1>Registration / Your Details <?= $this->Get('STEP_NO'); ?></h1>

<?
$response = $this->Get('VALIDATION_ERRORS'); 
?>

<div class="col four clear">

	<div id="msgtext" class="four left-align pad3-b" style="color: red;">
	<?= AppError::GetErrorHtml($response['msg']);  ?>
	</div>
	
	<p><span class="red">(* indicates mandatory field)</span></p>
	
	<form enctype="multipart/form-data" name="" id="" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">
	
	<div class="row">
		<span class="label_col"><label for="name" style="<?= strlen($response['msg']['name']) > 1 ? "color:red;" : ""; ?>">Your Name</label><span class="red"> *</span></span>
		<span class="input_col"><input type="text" id="name" name="name" style="width: 240px" maxlength="40" value="<?= $_POST['name']; ?>" /></span>
	</div> 
	
	<div class="row">
		<span class="label_col"><label for="name" style="<?= strlen($response['msg']['role']) > 1 ? "color:red;" : ""; ?>">Position / Role</label><span class="red"> *</span></span>
		<span class="input_col"><input type="text" id="role" name="role" style="width: 240px" maxlength="40" value="<?= $_POST['role']; ?>" /></span>
	</div> 
	
	<div class="row">
		<span class="label_col"><label for="email" style="<?= strlen($response['msg']['email']) > 1 ? "color:red;" : ""; ?>">Email Address</label><span class="red"> *</span></span>
		<span class="input_col"><input type="text" id="email" name="email" style="width: 240px" maxlength="40" value="<?= $_POST['email']; ?>" /></span>
	</div> 

	<div class="row">
		<span class="label_col"><label for="password" style="<?= strlen($response['msg']['password']) > 1 ? "color:red;" : ""; ?>">Password</label><span class="red"> *</span></span>
		<span class="input_col"><input type="password" id="password" name="password" style="width: 240px" maxlength="20" value="<?= $_POST['password']; ?>" /></span>
	</div> 

	<div class="row">
		<span class="label_col"><label for="password_confirm" style="<?= strlen($response['msg']['password_confirm']) > 1 ? "color:red;" : ""; ?>">Password Confirm</label><span class="red"> *</span></span>
		<span class="input_col"><input type="password" id="password_confirm" name="password_confirm" style="width: 240px" maxlength="20" value="<?= $_POST['password_confirm']; ?>" /></span>
	</div> 
	
	<div class="row">
		<span class="label_col"><label for="tel" style="<?= strlen($response['msg']['tel']) > 1 ? "color:red;" : ""; ?>">Contact Tel</label><span class="red"> *</span></span>
		<span class="input_col"><input type="text" id="tel" name="tel" style="width: 240px" maxlength="40" value="<?= $_POST['tel']; ?>" /></span>
	</div> 
	
	<div class="row">
		<span class="label_col"><label for="country" style="<?= strlen($response['msg']['country_applicant']) > 1 ? "color:red;" : ""; ?>">Country</label><span class="red"> *</span></span>
		<span class="input_col"><?= $this->Get('COUNTRY_LIST'); ?></span>
	</div> 

	<div class="row">
		<span class="label_col"><label for="comments" style="<?= strlen($response['msg']['comments']) > 1 ? "color:red;" : ""; ?>">Comments :</label></span>
		<span class="input_col"><textarea id="comments" name="comments" maxlength="1999" style="width: 400px; height: 120px;" /><?= stripslashes($_POST['comments']); ?></textarea></span>
	</div> 
	
	<div class="row">
		<span class="label_col">&nbsp;</span>
		<span class="input_col"><input type="submit" name="submit" id="submit" value="Submit" />
		</span>
	</div>
	
	
	</form>
</div>