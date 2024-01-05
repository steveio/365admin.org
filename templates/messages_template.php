
<div class="container">
<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">

<?
$aMessage = $this->Get('UI_MSG');


if (is_array($aMessage) && count($aMessage) >= 1) {
	foreach($aMessage as $oMessage) {
		if (is_object($oMessage))
		{
		    $type = '';
		    switch($oMessage->GetType())
		    {
		        case MESSAGE_TYPE_SUCCESS :
                    $type = "success";		            
		            break;
		        case MESSAGE_TYPE_ERROR : 
		            $type = "warning";
		            break;
		    }

		    
		    $str = "<div class=\"alert alert-".$type."\" role=\"alert\">";
		    $str .= $oMessage->GetIconHtml();
		    $str .= $oMessage->GetMsg();
		    $str .= "</div>";
			
		} else {
		    $str = "<div class=\"alert alert-".$type."\" role=\"alert\">";
		    $str .= $oMessage;
		    $str .= "</div>";

		}

		print $str;
	}
}
?>
</div>
</div>
