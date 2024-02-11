<?php
$oSalesEnquiry = $this->Get('oSalesEnquiry');

$strUrl = $this->Get('URL');
?>

<form enctype="multipart/form-data" name="enquir_form" id="enquiry_form" action="#" method="POST">

<input type="hidden" name="enq_submitted" value="TRUE" />
<input type="hidden" name="id" value="<?= base64_encode($oSalesEnquiry->GetId()) ?>" />

<div class="container">
  <div class="align-items-center justify-content-center">


          <div class="row formgroup my-2">

            <h1>Contact Us</h1>

              <? if ($oSalesEnquiry->GetValidationError() !== null && count($oSalesEnquiry->GetValidationError()) >= 1) { ?>
          		<div id="msgtext" class="alert alert-warning" role="alert">
          		<?= AppError::GetErrorHtml($oSalesEnquiry->GetValidationError());  ?>
          		</div>
          		<? } ?>
				
				<p>To add your organisation to our site or to update an existing listing please <a href="<?= $strUrl; ?>">click here</a>.</p>
				<p>For all other enquiries please contact us via the enquiry form below.</p>

                <div class="formgroup row my-2">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('enq_name')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><span class="<?= $sErrorCss; ?>">Your Name<span class='red'>*</span></span></span>
                        <span class="input_col">
                                <input type="text" name="enq_name" class="form-control" value="<?= $oSalesEnquiry->GetName(); ?>"  />
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('enq_name')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('enq_name'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>

               <div class="row formgroup my-2">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('enq_comp_name')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><span class="<?= $sErrorCss; ?>">Organisation Name<span class='red'>*</span></span></span>
                        <span class="input_col">
                                <input type="text" name="enq_comp_name" class="form-control" value="<?= $oSalesEnquiry->GetCompanyName(); ?>"  />
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('enq_comp_name')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('enq_comp_name'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>

                <div class="row formgroup my-2">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('enq_email')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><span class="<?= $sErrorCss; ?>">Contact Email<span class='red'>*</span></span></span>
                        <span class="input_col">
                                <input type="text" name="enq_email" class="form-control" value="<?= $oSalesEnquiry->GetEmail(); ?>"  />
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('enq_email')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('enq_email'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>

                <div class="row formgroup my-2">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('enq_tel')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><span class="<?= $sErrorCss; ?>">Contact Telephone No</span></span>
                        <span class="input_col">
                                <input type="text" name="enq_tel" class="form-control" value="<?= $oSalesEnquiry->GetTel(); ?>"  />
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('enq_tel')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('enq_tel'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>

               <div class="row formgroup my-2">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('enq_details')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><span class="<?= $sErrorCss; ?>">Enquiry<span class='red'>*</span></span></span>
                        <span class="input_col">
                                <textarea name="enq_details" class="form-control" ><?= $oSalesEnquiry->GetEnquiry(); ?></textarea>
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('enq_details')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('enq_details'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>

                <div class="row formgroup my-2">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('security_q')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><label for="name"><span class="<?= $sErrorCss; ?>">Security Question<span class='red'>*</span></span></label></span>
                        <span class="input_col">
                                <label for="name"><?= $oSalesEnquiry->GetSecurityQuestion()->GetQuestion(); ?></label><br />
                                <input type="text" name="security_q" class="form-control" value="<?= $_REQUEST['security_q']; ?>"  />
                                <input type="hidden" name="security_qid" value="<?= $oSalesEnquiry->GetSecurityQuestion()->GetId(); ?>"  />
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('security_q')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('security_q'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>


                <div class="row formgroup my-2">
                        <span class="label_col">&nbsp;</span>
                        <span class="input_col">
                            <button class="btn btn-primary rounded-pill px-3" type="submit" name="login">Submit</button>
                        </span>
                </div>


                </div>

</div>
</div>
</form>
