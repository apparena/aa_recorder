<?php
class iCon_Newsletter {
	
	/**
	 * Send a newsletter registration confirmation email to the user. This email contains a confirmation link,
	 * the user can click to confirm the registration.
	 * @param String $rec_email Email-address of the receiver
	 * @param String $rec_name Name of the newsletter receiver
	 * @param String $send_email Email-address of the sender
	 * @param String $send_name Name of the newsletter sender
	 * @param String $title Title of the newsletter (e.g. iConsultants Social Media Newsletter)
	 * @param String $body HTML email body. Please use {{confirmation_link}} to place your confirmation link into your body text.
	 */
	function sendConfirmationEmail($rec_email, $rec_name, $send_email, $send_name, $title, $bodyHtml, $aa_inst_id="",$fb_user_id) {
		// Create confirmation link
		
		$path = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]);
		$confirmationURL = $path . "/confirm_newsletter_registration.php";
    $decode = base64_encode($rec_email . ";" . $rec_name.";".$fb_user_id);
		$confirmationURL = $confirmationURL . '?aa_inst_id=' . $aa_inst_id . '&id=' .$decode;
		$confirmationLink = "<a href='" . $confirmationURL . "' title='" . __t("Confirm newsletter registration") 
							. "'>" . __t("Confirm newsletter registration") . "</a>";
		
		// Replace variables in Email-text
		$bodyHtml = str_replace("{{confirmation_link}}", $confirmationLink, $bodyHtml);
		$bodyHtml = str_replace("{{name}}", $rec_name, $bodyHtml);
		
		// Setup Zend SMTP server
		$config = array('ssl'=>'tls','username' => 'wp1130074-appmailer', 'password' => 'yJamM958erOgR3IE','port'=>587,'auth'=>'login');
		$transport = new Zend_Mail_Transport_Smtp('wp143.webpack.hosteurope.de', $config);
		
		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyHtml($bodyHtml);
		$mail->setFrom($send_email, $send_name);
		$mail->addTo($rec_email, $rec_name);
		$mail->setSubject($title);
		
		try{
			$mail->send($transport);

      //update app_participation
      $lottery = new iCon_Lottery($aa_inst_id,getConfig('aa_app_id'));
      $id=$lottery->isUserParticipating($fb_user_id, $aa_inst_id) ;

      if($id == false)
      {
         //insert app_participation,should not bu false!
         $msg=__t("participation record not exist");
         return $msg;
      }
      else
      {
         $table=new Table_Participation();
         $table->load($id);
         $table->newsletter_registration=1;
         $table->save();
      }

      return true;

		}catch(Exception $e)
		{
			//send mail failed
      return $e->getMessage();
		}
	}
	
	
	/**
	 * Save a new double opt in newsletter subscription to the database including ip and timestamp
	 * @param String $data base64 encoded email-address and name of the newsletter subscriber (email;name)
	 */
	function registerNewSubscription($data, $aa_inst_id){
		//global $global;
		//$db = $global->db;
    $db=getDb();
		
		// Decode and assign data to variables
		$data = explode(';', base64_decode($data));
		$email = $data[0];
		$name = $data[1];

		// Update table app_participation, columns timestam, ip, newsletter_registration
		// Get fb_user_id from email-address
		$sql = "SELECT `fb_user_id` FROM `user_data` WHERE `email`='" . $email . "'";
		$fb_user_id = $db->fetchOne($sql);
		// Get client IP
		if ( isset($_SERVER["REMOTE_ADDR"]))
			$client_ip = $_SERVER["REMOTE_ADDR"];
		else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			$client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if ( isset($_SERVER["HTTP_CLIENT_IP"]))
			$client_ip = $_SERVER["HTTP_CLIENT_IP"];
		// Set newsletter subscription in DB
		$sql = "UPDATE `app_participation` SET `ip`='" . $client_ip . "', `newsletter_registration`=1 
				WHERE `aa_inst_id`='$aa_inst_id' AND `fb_user_id`='$fb_user_id'";
		$res = $db->query($sql);
	}

  /**
  * after user click the confirm link in email,
  * the config page update user's confirm state
  *  
  */
  public static function updateConfirm($aa_inst_id,$fb_user_id)
  {
     //update app_participation
     $lottery = new iCon_Lottery($aa_inst_id,getConfig('aa_app_id'));
     $id=$lottery->isUserParticipating($fb_user_id, $aa_inst_id) ;

     if($id != false)
     {
        $table=new Table_Participation();
        $table->load($id);
        $table->newsletter_doubleoptin =1;
        $table->save();
     }

  }
	
}
