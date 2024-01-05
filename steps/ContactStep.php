<?php


class ContactStep extends GenericStep {

	public function __construct( ){

		parent::__construct();

	}

	public function Process() {

		global $oHeader, $oFooter, $oAuth, $oSession;

		$oSalesEnquiry = new SalesEnquiry();

		if ($oAuth->oUser->isValidUser) {
			$oSalesEnquiry->SetName($oAuth->oUser->name);
			$oSalesEnquiry->SetCompanyName($oAuth->oUser->company);
			$oSalesEnquiry->SetEmail($oAuth->oUser->email);
		}

		$sDisplay = "FORM";

		$aQuestions = array(
	                          1 => array('id' => 1,
                                         'question' => 'Please type the word <b>human</b>',
                                         'answers' => array('human')
                                        )
                           );


		if ($_POST['enq_submitted'] == "TRUE") {

				global $oBrand, $_CONFIG, $oSession;

				// these are required to send emails in SalesEnquiry->Notify()
				$_CONFIG['root_path'] = $oBrand->GetWebsitePath();
				$_CONFIG['template_home'] = "/templates";
				$_CONFIG['site_title'] = $oBrand->GetSiteTitle();


        $oSalesEnquiry->SetWebsiteId($oBrand->GetSiteId());
        $oSalesEnquiry->SetIpAddress(IPAddress::GetVisitorIP());
        $oSalesEnquiry->SetFromArray($_POST);

        $aQuestion = $aQuestions[$_REQUEST['security_qid']];
        $oSecurityQuestion = new SecurityQuestion($aQuestion['id'],$aQuestion['question'],$aQuestion['answers']);
        $oSecurityQuestion->SetResponse($_REQUEST['security_q']);
        $oSecurityQuestion->Verify();
        $oSalesEnquiry->SetSecurityQuestion($oSecurityQuestion);

				$aMsg = array();

        if ($oSalesEnquiry->Process()) {
					$aMsg[] = "<img src='/images/icon_green_tick.png' />";
      		$aMsg[] = "<h1>Thanks for your Enquiry</h1>";
					$aMsg[] = "<p>One of our team will contact you shortly.</p>";

					$oConfirmation = new Template();
					$oConfirmation->Set('UI_MSG',$aMsg);

					$oConfirmation->LoadTemplate("messages_template.php");

					print $oHeader->Render();
					print $oConfirmation->Render();
					print $oFooter->Render();

					die();
	      }

		} else {
		        $oSalesEnquiry->GetNextId();

		        // select a new security question
		        shuffle($aQuestions);
		        $aQuestion = array_shift($aQuestions);
		        $oSecurityQuestion = new SecurityQuestion($aQuestion['id'],$aQuestion['question'],$aQuestion['answers']);
		        $oSalesEnquiry->SetSecurityQuestion($oSecurityQuestion);

		}

		$oContactForm = new Template();
		$oContactForm->Set('oSalesEnquiry',$oSalesEnquiry);
		$oContactForm->LoadTemplate("sales_enquiry_form_template.php");

		print $oHeader->Render();
		print $oContactForm->Render();
		print $oFooter->Render();

	}


}

?>
