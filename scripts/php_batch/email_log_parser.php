<?php 

/**
 * Postfix Mail Log Parser 
 * 
 * 
 * 
 * 
 */

require_once("/www/vhosts/365admin.org/htdocs/conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/file.class.php");
require_once(BASE_PATH."/classes/enquiry.class.php");


$db = new db($dsn,$debug = false);


ProcessMailLogDeliveryStatus($argv[1]);



function ProcessMailLogDeliveryStatus($iEnquiryId)
    {
        global $db,$_CONFIG;

        $oEnquiry = new Enquiry();
        $to_email_enq = $oEnquiry->GetToEmailByEnquiryId($iEnquiryId);

	//print_r("to_email_enq: ".$to_email_enq."\n");

        $sCmd = "cat /var/log/maillog | grep status ";
        
        $aOut = array();
        
        exec($sCmd,$aOut);
      
        if (count($aOut) < 1) return false;
        
        // extract log msg for enquiry        
        for($i=count($aOut);$i>0;$i--)
        {
        
            $log_msg = $aOut[$i];
            $aBits = explode(" ",$log_msg);

            foreach($aBits as $log_field)
            {
                if (preg_match("/^to=/", $log_field))
                {
                    $str = substr($log_field, 0, -2);
                    $to_email_log = substr($str, 4);                    
                }
                if (preg_match("/^status=/", $log_field))
                {
                    $status_str = $log_field;
                    $status_str = substr($status_str, 7); // eg  status=bounced
                }
                
                $log_msg = substr($log_msg, 0, 380);

            }
            
            if ($to_email_enq == $to_email_log)
            {
        
         //       print_r("to_email_log: ".$to_email_log."\n");
         //       print_r("status: ".$status_str."\n");

		$log_msg = pg_escape_string($log_msg);
 
                $sql = "INSERT INTO enquiry_delivery (enquiry_id,to_email,status,log_msg) VALUES (".$iEnquiryId.",'".$to_email_log."','".$status_str."','".$log_msg."')";
                
           //     print_r($sql."\n");
               
                $db->query($sql);

		if ($db->getAffectedRows() == 1)
		{
	                return true;
		} 

		return false;
            }
    
        }        
    }


?>
