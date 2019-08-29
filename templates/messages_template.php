<div id="user_msg" class="four left-align">
<? 
$aMessages = $this->Get('USER_MSG');
if (count($aMessages) >= 1) { 
	foreach($aMessages as $oMessage) {
		print $oMessage->Render();
	}
}
?>
</div>