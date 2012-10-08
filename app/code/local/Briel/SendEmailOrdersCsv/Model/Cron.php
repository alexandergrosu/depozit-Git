<?php

class Briel_SendEmailOrdersCsv_Model_Cron
{
	public function csvEmail()
	{
		$current_date = date("Y-m-d");
		
		$fromEmail = "contact@laptop-direct.ro"; // sender email address
	    $fromName = "Laptop Direct"; // sender name
	    //$toEmail = "onel.velica@briel.ro"; // recipient email address
	    //$toName = "Onel Velica"; // recipient name
	    $toEmail = "alex.grosu@briel.ro"; // recipient email address
	    $toName = "Alex"; // recipient name
	    $body = ""; // body text
	    $subject = "Export Comenzi $current_date"; // subject text
	     
	    $mail = new Zend_Mail();       
	    $mail->setBodyText($body);
	    $mail->setFrom($fromEmail, $fromName);
	    $mail->addTo($toEmail, $toName);
	    $mail->setSubject($subject);
		
		$file_name = Mage::getBaseDir('export').'/'.'order_export_'.date("Y-m-d").'.csv'; //file path
    	$fileContents = file_get_contents($file_name);
    	$file = $mail->createAttachment($fileContents);
    	$file->filename = "order_export_$current_date.csv";
			
	    $mail->send();
	}
}