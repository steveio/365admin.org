<?php 
$oSalesEnquiry = $this->Get('oSalesEnquiry');

?>

<form enctype="multipart/form-data" name="enquir_form" id="enquiry_form" action="#" method="POST">

<input type="hidden" name="enq_submitted" value="TRUE" />
<input type="hidden" name="id" value="<?= base64_encode($oSalesEnquiry->GetId()) ?>" />

<div class="fieldsetWrapper">

                <h1>Contact Us</h1>
                <div class="pad">

                <? if (strlen($oSalesEnquiry->GetErrorMessage()) > 1) { ?>
                        <div><p class="red"><?= $oSalesEnquiry->GetErrorMessage(); ?></p></div>
                <? } ?>

                <div class="row">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('enq_name')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><span class="<?= $sErrorCss; ?>">Your Name<span class='red'>*</span></span></span>
                        <span class="input_col">
                                <input type="text" name="enq_name" class="textbox250" value="<?= $oSalesEnquiry->GetName(); ?>"  />
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('enq_name')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('enq_name'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>

               <div class="row">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('enq_comp_name')) > 1) ? "red" : ""; ?>                             
                        <span class="label_col"><span class="<?= $sErrorCss; ?>">Organisation Name<span class='red'>*</span></span></span>
                        <span class="input_col">
                                <input type="text" name="enq_comp_name" class="textbox250" value="<?= $oSalesEnquiry->GetCompanyName(); ?>"  />               
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('enq_comp_name')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('enq_comp_name'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>

                <div class="row">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('enq_email')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><span class="<?= $sErrorCss; ?>">Contact Email<span class='red'>*</span></span></span>
                        <span class="input_col">
                                <input type="text" name="enq_email" class="textbox250" value="<?= $oSalesEnquiry->GetEmail(); ?>"  />
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('enq_email')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('enq_email'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>

                <div class="row">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('enq_tel')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><span class="<?= $sErrorCss; ?>">Contact Telephone No</span></span>
                        <span class="input_col">
                                <input type="text" name="enq_tel" class="textbox250" value="<?= $oSalesEnquiry->GetTel(); ?>"  />
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('enq_tel')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('enq_tel'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>

               <div class="row">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('enq_details')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><span class="<?= $sErrorCss; ?>">Enquiry<span class='red'>*</span></span></span>
                        <span class="input_col">
                                <textarea name="enq_details" class="textarea250" ><?= $oSalesEnquiry->GetEnquiry(); ?></textarea>
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('enq_details')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('enq_details'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>

                <div class="row">
                        <?php $sErrorCss = (strlen($oSalesEnquiry->GetValidationErrorById('security_q')) > 1) ? "red" : ""; ?>
                        <span class="label_col"><label for="name"><span class="<?= $sErrorCss; ?>">Security Question<span class='red'>*</span></span></label></span>
                        <span class="input_col">
                                <label for="name"><?= $oSalesEnquiry->GetSecurityQuestion()->GetQuestion(); ?></label><br />
                                <input type="text" name="security_q" class="textbox250" value="<?= $_REQUEST['security_q']; ?>"  />
                                <input type="hidden" name="security_qid" value="<?= $oSalesEnquiry->GetSecurityQuestion()->GetId(); ?>"  />
                        <?php if (strlen($oSalesEnquiry->GetValidationErrorById('security_q')) > 1) { ?>
                                <br /><span class="error red"><?= $oSalesEnquiry->GetValidationErrorById('security_q'); ?></span>
                        <?php } ?>
                        </span>
                        <span class='q_help'></span>
                </div>


                <div class="row">
                        <span class="label_col">&nbsp;</span>
                        <span class="input_col">
                                <input type="submit" name="submit_enquiry" id="submit" value="Submit" />
                        </span>
                </div>


                </div>

</div>

</form>
                