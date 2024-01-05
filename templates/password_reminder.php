<div class="container">
<div class="align-items-center justify-content-center">


<div class="row">
	<h2>Password Reminder</h2>

  <p>If you have forgotten your login details please enter your email address below.</p>
  <p>Alternatively <a href='/contact' title='contact us'>contact us</a> for further assistance.</p>

  <div id="login">
          <form action="" method="post">

          <div class="">

                  <div class="row formgroup my-2">
                          <span  class="label_col">
                                  <label for="email" style="<?= strlen($aError['EMAIL']) > 1 ? "color:red;" : ""; ?>">Email Address: </label>
                          </span>
                          <span class="input_col">
                                  <input type="text" class="form-control" name="email" id="email" maxlength="90" value="<?= isset($_POST['email']) ? $_POST['email'] : ""; ?>" />
                          </span>
                  </div>

									<div class="row formgroup my-2">
                  	<span class="label_col">&nbsp;</span>
                  	<span class="input_col">
                  		<button class="btn btn-primary rounded-pill px-3" type="submit" name="submit">Submit</button>
                  	</span>
                  </div>
          </div>

          </form>
  </div>
  <div class="row">
          <p>Unauthorised access and/or misuse of the system is an offence under the Computer Misuse Act of 1990.</p>
          <p2>Any use must be in accordance with the remote access information security policy. Any actions taken in breach of this policy may result in legal action being taken.</p2>
  </div>
 </div>
