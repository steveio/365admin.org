<div class="col four-sm pad-b">
	<h2>Password Reminder</h2>

        <p>If you have forgotten your login details please enter your email address below.</p>
        <p>Alternatively <a href='/contact' title='contact us'>contact us</a> for further assistance.</p>

        <div id="login">
                <form action="" method="post">

                <div class="col two-sm border pad clear">

                        <div id="msgtext" style="color: red; font-size: 10px;">
                                <?= $sMsgHTML; ?>
                        </div>

                        <div class="row">
                                <span  class="label_col">
                                        <label for="email" style="<?= strlen($aError['EMAIL']) > 1 ? "color:red;" : ""; ?>">Email Address: </label>
                                </span>
                                <span class="input_col">
                                        <input title="Submit" type="text" class="" style='width: 200px;' name="email" id="email" maxlength="90" value="<?= isset($_POST['email']) ? $_POST['email'] : ""; ?>" />
                                </span>
                        </div>

						<div class="row">
                        	<span class="label_col">&nbsp;</span>
                        	<span class="input_col">
                        	<input type="submit" title="submit" name="submit" value="submit" class="" /> 
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
