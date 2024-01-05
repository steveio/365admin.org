<?php
/*
 * @params -
 * register_url
 * password_url
 * login_url
 * error_msg
 * aError
 */

$aError = $this->Get('aError');
foreach($aError as $k => $v) {
	if (!in_array($k,array('CREDENTIAL_PASSWD','CREDENTIAL_UNAME'))) {
		$error_msg .= $v."<br />";
	}
}
?>

<div class="container">
<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">


<form name="LoginForm" action="/<?= ROUTE_LOGIN; ?>" method="post">

<input type="hidden" name="FORWARD_URI" value="<?= $this->Get('FORWARD_URI'); ?>" />

<div class="col">

  <div class="col">
     <h1>Welcome to the <?= $this->Get('BRAND_NAME'); ?> Admin Area. </h1>
  </div>

  <p>To apply for a new listing for your  organisation <a href='/<?= ROUTE_NEW ?>' title='Register for an account'>click here</a>.</p>

  <p>To get in touch or for more information on advertising options please <a href="/contact" title="Contact Us">contact us.</a></p>

  <p>Or to log in please enter your username and password below.</p>

  <p>If you have forgotten your login details <a href="/<?= ROUTE_PASSWD ?>" title='Forgot Password Link'>click here</a>.</p>

  </div>
  <div class="col">

	<div class="">

		<h2>Login</h2>

		<? if (isset($aError) && count($aError) >= 1) { ?>
		<div id="msgtext" class="alert alert-warning" role="alert">
		<?= AppError::GetErrorHtml($aError);  ?>
		</div>
		<? } ?>


		<div class="row formgroup my-2">
			<span  class="label_col"><label for="username" style="<?= isset($aError['CREDENTIAL_UNAME']) ? "color:red;" : ""; ?>">Username: </label></span>
			<span class="input_col">
				<input title="username" type="text" class="form-control" name="uname" id="username" maxlength="119" value="<?= isset($_POST['uname']) ? $_POST['uname'] : ""; ?>" />
				<?php if (isset($aError['CREDENTIAL_UNAME'])) { ?>
					<br /><span class="error red"><?= $aError['CREDENTIAL_UNAME']; ?></span>
				<?php } ?>
			</span>
		</div>

		<div class="row formgroup my-2">
			<span class="label_col"><label for="password" style="<?= isset($aError['CREDENTIAL_PASSWD']) ? "color:red;" : ""; ?>">Password: </label></span>
			<span class="input_col">
				<input title="password" type="password" class="form-control" name="pass" id="pass" maxlength="20" value="<?= isset($_POST['pass']) ? $_POST['pass'] : ""; ?>" />
				<?php if (isset($aError['CREDENTIAL_PASSWD'])) { ?>
					<br /><span class="error red"><?= $aError['CREDENTIAL_PASSWD']; ?></span>
				<?php } ?>

			</span>
		</div>

		<div class="row my-3">
			<span  class="label_col">&nbsp;</span>
			<span class="input_col">
				<button class="btn btn-primary rounded-pill px-3" type="submit" name="login">Login</button>
			</span>
		</div>


	</div>
	<div class="col four pad3-t">
		<p2>Unauthorised access and/or misuse of the system is an offence under the Computer Misuse Act of 1990.<p2/>
	</div>

</div>

</div>
</div>


</form>
