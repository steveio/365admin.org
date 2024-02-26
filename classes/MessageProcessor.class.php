<?php



class MessageProcessor
{
    
    public function __construct() {}
    
    public function GetMessagePanel()
    {
        global $oSession;
    
        $oMessagesPanel = new Template();
        
        // no session or session expired (note this only works in contexts/domains where session is mandatory
        if (!is_object($oSession)) {
            
            $oMessage = new Message(MESSAGE_TYPE_ERROR, 'SESSION_EXPIRED_MSG', "Sorry, no valid session or session expired.  To continue <a href='/".ROUTE_LOGIN."'>click here</a> to login...");
            $oMessagesPanel->Set('UI_MSG', array($oMessage));

        // message(s) in _SESSION
        } elseif (is_object($oSession) && count($oSession->GetMessage()) >= 1) { 
    
            $oMessagesPanel->Set('UI_MSG', $oSession->GetMessage());            
            $oSession->UnsetMessage();
            
        // messages in MVC controller scope (in _SESSION)
        } elseif (is_object($oSession->GetMVCController()) && is_object($oSession->GetMVCController()->GetCurrentRoute()) && count($oSession->GetMVCController()->GetCurrentRoute()->GetMessage()) >= 1) { 

            $oMessagesPanel->Set('UI_MSG', $oSession->GetMVCController()->GetCurrentRoute()->GetMessage());
            $oSession->UnsetMessage();

        }
        
        $oMessagesPanel->LoadTemplate("messages_template.php");
        
        return $oMessagesPanel;
    }
}
