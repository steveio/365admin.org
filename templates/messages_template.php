
<div class="container">
<div class="align-items-center justify-content-center">

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
		            
		        case MESSAGE_TYPE_FATAL_ERROR :
		            $type = "danger";
		            break;

		        case MESSAGE_TYPE_404_NOTFOUND:
		        case MESSAGE_TYPE_WARNING :
		        case MESSAGE_TYPE_VALIDATION_ERROR :
		        case MESSAGE_TYPE_ERROR : 
		            $type = "warning";
		            break;
		    }

		    $str = "<div class=\"alert alert-".$type."\" role=\"alert\">";
		    $str .= $oMessage->GetMsg();
		    $str .= "</div>";
			
		} elseif (is_string($oMessage)) {
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