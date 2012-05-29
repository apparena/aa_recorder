<?php
/**
 * Additional Functions for the Lottery App
 * @author Sebastian Buckpesch
 * @version 0.2
 * @copyright  Copyright (c) 2011 iConsultants UG (http://www.iconsultants.eu)
 * @license    
 * @package iCon
 * @uses AA_Fb_User
 */
class iCon_Lottery {

	private $particiantList;
	private $winner;
	private $db;
	private $tracking;
	private $session;
	private $aa_app_id;
	protected $aa_inst_id=false;

	function __construct($aa_inst_id,$aa_app_id = 0){
		if ($aa_app_id == 0) {
			// Load config
			try {
				require_once dirname(__FILE__).'/../../config.php';
			} catch (Exception $e) {
				error_log($e);
			}						
		}
		$this->aa_inst_id=$aa_inst_id;
		$this->aa_app_id=$aa_app_id;
		// Start App-Arena Session
		$this->session = new Zend_Session_Namespace('aa_session_' . $this->aa_inst_id);
		$this->tracking = new iCon_Tracking();
		// Make globals available
		//global $global;
		//$this->db = $global->db;
		$this->db = getDb();
	}

	/**
	 * Registers current users participation in database 
	 * @param String $username username or userid of facebook user whose data should be delivered
	 * @param String $aa_inst_id App instance id
	 * @param timestamp $date date for which the user should be registered
	 * @return Successfully registered in DB
	 */
	public function registerParticipant($user_id, $aa_inst_id, $date=0){

		if (!$this->isUserParticipating($user_id, $aa_inst_id)) {				
			// Register new participant, give him one ticket for participating
			$sql = "INSERT INTO `app_participation` 
					SET `fb_user_id`='$user_id', `aa_inst_id`='$aa_inst_id', `ip`='" . $this->getClientIp() . "', `timestamp`='" . date('Y-m-d H:i:s', time()) . "'";
			$res = $this->db->query($sql);
			return true;
		}
		return false;
	}

	/**
	 * Returns if the user is already participating in the Lottery or not
	 * @param String $user_id Facebook user id
	 * @param int $aa_inst_id instance id of the application
	 * @param timestamp $min_date optional lower boundary for date
	 * @param timestamp $max_date optional upper boundary for date
	 * @return boolean Returns if the user is participating in the current Lottery
	 */
	public function isUserParticipating($user_id=0, $aa_inst_id=0, $min_date=0, $max_date=0) {
		// Check if user id and aa_inst_id are available
		if ($user_id == false || intval($aa_inst_id) == 0)
				return false;
		
		// Check if a round reset was taking place
		$round_reset_timestamp = $this->getRoundResetTimestamp($aa_inst_id);
		if (!$round_reset_timestamp){
			$sql = "SELECT * FROM `app_participation` WHERE `fb_user_id`='$user_id' 
					AND `aa_inst_id`='$aa_inst_id'";
			// Set date range in sql-query
      if ($min_date <> 0)
      {
         $min_date=format_datetime($min_date);
         $sql .= "AND `app_participation`.`timestamp`>'$min_date' ";
      }
      if ($max_date <> 0)
      {
         $max_date=format_datetime($max_date,"Y-m-d 59:59:59");
         $sql .= "AND `app_participation`.`timestamp`<='$max_date'";
      }
		} else {
			$sql = "SELECT * FROM `app_participation` WHERE `fb_user_id`='$user_id'
								AND `aa_inst_id`='$aa_inst_id'";
			// Set date range in sql-query
      if ($min_date <> 0)
      {
         $min_date=format_datetime($min_date);
         $sql .= "AND `app_participation`.`timestamp`>'$min_date' ";
      }
      else 
      {
         $sql .= "AND `app_participation`.`timestamp`>'$round_reset_timestamp' ";
      }

      if ($max_date <> 0)
      {
         $max_date=format_datetime($max_date,"Y-m-d 59:59:59");
         $sql .= "AND `app_participation`.`timestamp`<='$max_date'";
      }
		}
		$row = $this->db->fetchOne($sql);
		return $row;
	}

	/**
	 * Get a random Winner from the participantList
	 * @param Array <String> $partipantList user_id, first_name, last_name, email
	 * @param int App-Arena Instance ID
	 * @param int Nr of winners which will be returned
	 * @return Array <multitype:, multitype:number unknown >
	 */
	public function getWinner($participantList, $aa_inst_id, $nr_of_winners = 1) {
		$session = new Zend_Session_Namespace('aa_session_' . $this->aa_inst_id);
		$winnerList = array(); // list of all participants incl. nr of their tickets
		$winners = array(); // actual winners

		$i = 0;

    //get like page's uids
    $uids=array();
    foreach ($participantList as $participant){
       $uids[]=$participant['fb_user_id'];
    }

    // Get number of tickets
    $tickets = $this->getNrOfTickets($uids, $aa_inst_id, 0, array());

    $winner_max=count($participantList); //max amount of winners
    $nr_of_winners=min($nr_of_winners,$winner_max); //reset nr of winners

    foreach ($participantList as $participant){
       $uid=$participant['fb_user_id'];

       if(isset($tickets[$uid]))
       {
          for ($j = 0; $j < $tickets[$uid]; $j++) {
             //$winnerList[] = array($i, $participant['fb_user_id'], $participant['first_name'], $participant['last_name'], $participant['email'],$tickets[$uid]);
             $winnerList[] = array(
                'fb_user_id'=>$participant['fb_user_id'], 
                'name'=>$participant['name'], 
                'first_name'=>$participant['first_name'], 
                'last_name'=>$participant['last_name'], 
                'email'=>$participant['email'],
                'tickets'=>$tickets[$uid],
             );
          }
       }
    }

    $amount_users=count($winnerList);

    srand (time());

    $winners = array();
    //while ( count($winners) < $nr_of_winners ) {
    for($i=1;$i<=$nr_of_winners;++$i){

       //$i = mt_rand(0, $amount_users-1);

       $key=array_rand($winnerList);
       $winner=$winnerList[$key];

       //$winner=$winnerList[$i];
       $email=$winner['email'];

       if(!isset($winners[$email]))
       {
          $winners[$email]=$winner;
       }

       //unset all winner which is this email, so the next rand pick will not pick the same user
       foreach($winnerList as $k=>$winner)
       {
          if($winner['email'] == $email )
          {
             unset($winnerList[$k]);
          }
       }

       //foreach($

       /*
       if ( !in_array($x, $winnerList) ) {
          $winnerNumbers[$j] = $x;
          $j+=1;
       }		
       */
    }

    // Create a randon number for all winners
    /*
    $j = 0;
    $winnerNumbers = array();
    while ( count($winnerNumbers) < $nr_of_winners ) {
       $x = mt_rand(0, $i-1);
       if ( !in_array($x, $winnerList) ) {
          $winnerNumbers[$j] = $x;
          $j+=1;
       }		
    }
    foreach ($winnerNumbers as $winnerIndex) {
       if(isset($winnerList[$winnerIndex] ) )
       {
          $winners[] = $winnerList[$winnerIndex];
       }
    }
    */
    return $winners;
 }


	/**
	 * Returns the IP of the client 
	 * @return String client ip
	 */
	private function getClientIp(){
		// Get client ip address
		if ( isset($_SERVER["REMOTE_ADDR"]))
		    $client_ip = $_SERVER["REMOTE_ADDR"];
		else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		    $client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if ( isset($_SERVER["HTTP_CLIENT_IP"]))
		    $client_ip = $_SERVER["HTTP_CLIENT_IP"];

		return $client_ip;
	}


	private function yesterdayDate($date) {
		$yesterday = date('d/m/y', mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
	}


	/**
	 * Returns a participant List in commited date range of a certain app instance.
	 * @param int $aa_inst_id App Arena instance id
	 * @param $additionalColumns columns to add for the query
	 * @param timestamp $min_date lower boundary for date filter of participants
	 * @param timestamp $max_date upper boundary for date filter of participants
	 * @return Array Participant list as array
	 */
	public function getParticipantList($aa_inst_id, $additionalColumns = "", $min_date=0, $max_date=0) {
		$participantList= Array();
		//$sql = "SELECT `user_id`, `first_name`, `last_name`, `email`, `gender`, `timestamp`, `ip`, `newsletter_registration`, `tickets`  
		$sql = "SELECT `user_data`.`fb_user_id`, `first_name`, `last_name`, `email`, `gender`" . $additionalColumns . " 
				FROM `user_data` INNER JOIN `app_participation` 
				ON `user_data`.`fb_user_id`=`app_participation`.`fb_user_id` 
				WHERE `app_participation`.`aa_inst_id`='$aa_inst_id' ";
        if ($min_date <> 0)
        {
           $min_date=format_datetime($min_date);
           $sql .= "AND `app_participation`.`timestamp`>'$min_date' ";
        }
        if ($max_date <> 0)
        {
         $max_date=format_datetime($max_date,"Y-m-d 59:59:59");
           $sql .= "AND `app_participation`.`timestamp`<='$max_date'";
        }
		return $this->db->fetchAll($sql);
	}
	
	
	/**
	 * Get the winners of this contest. Only users with correct answer(s) will be returned.
	 * @param $aa_inst_id AA instance id
	 * @param $additionalColumns columns to add for the query
	 * @param $min_date lower boundary for date filter of participants
	 * @param $max_date upper boundary for date filter of participants
	 * @return Array winner list as an array
	 */
	public function getWinnerList($aa_inst_id, $additionalColumns = "", $min_date=0, $max_date=0) {
		$participantList= Array();
		//$sql = "SELECT `user_id`, `first_name`, `last_name`, `email`, `gender`, `timestamp`, `ip`, `newsletter_registration`, `tickets`  
		$sql = "SELECT `user_data`.`fb_user_id`, `first_name`, `last_name`, `email`, `gender`" . $additionalColumns . " 
				FROM `user_data` INNER JOIN `app_participation` 
				ON `user_data`.`fb_user_id`=`app_participation`.`fb_user_id` 
				WHERE `app_participation`.`aa_inst_id`='$aa_inst_id' 
				AND `app_participation`.`answers_correct`=1 ";
        if ($min_date <> 0)
        {
           $min_date=format_datetime($min_date);
           $sql .= "AND `app_participation`.`timestamp`>'$min_date' ";
        }

        if ($max_date <> 0)
        {
           $max_date=format_datetime($max_date,"Y-m-d 59:59:59");
           $sql .= "AND `app_participation`.`timestamp`<='$max_date' ";
        }
			
		$sql .= "ORDER BY `app_participation`.`tickets` DESC";
		
		return $this->db->fetchAll($sql);
	}
	
	
	/**
	 * Returns the timestamp of the last instance id reset. A reset is done, when the next lottery round starts
	 * @param int $aa_inst_id
	 * @return int returns a Standard unix timestamp
	 */
	public function getRoundResetTimestamp($aa_inst_id) {
		$sql = "SELECT `config_value` 
				FROM `app_config` 
				WHERE aa_inst_id=" . $aa_inst_id . 
				" AND `config_key`='round_reset_timestamp'
				LIMIT 1";
		return $this->db->fetchOne($sql);
	}
	

	/**
	 * Returns number of tickets of the commited user.
	 * @param String $fb_user_id Facebook user id
	 * @param int $aa_inst_id App Arena Instance Id
	 * @param timestamp $date optional nr of tickets for this certain date
	 * @param array $sponsor Optional array with 'page_id' and 'weight' of the sponsor and 'page_like' --> list of all userids which like the sponsor
	 * @return array  format:  array(UID1=>TICKET_NR, UID2=>TICKET_NR,...)
	 */
	public function getNrOfTickets($fb_user_ids, $aa_inst_id, $date=0, $sponsor=array()) {

		if($fb_user_ids == false)
			return array();

		//get like fan page 's fb user ids
		if( isset($sponsor['page_like']) )
			$like_uids=$sponsor['page_like'];
		else
			$like_uids=array();

		$select=$this->db->select();
		$select->from("app_participation","*");

		if(!is_array($fb_user_ids))
			$select->where("fb_user_id=?",$fb_user_ids);

		$select->where("aa_inst_id=?",$aa_inst_id);
		if($date  != false)
			$select->where("SUBSTRING(`timestamp`,1,10)=?", date("Y-m-d", $date));

		$tickets = $this->db->fetchAll($select);
		if ($tickets == false)
			return array();

		// Get number of referred users
		$result=array();  //result: the  return  value

		$ref_tickets = $this->tracking->getNrOfReferrals($fb_user_ids, $aa_inst_id, $date);


		foreach($tickets as $ticket)
		{
			$fb_user_id=$ticket['fb_user_id'];

			if(isset( $ref_tickets[$fb_user_id] ) )
			$ref_ticket = $ref_tickets[$fb_user_id];
			else
			$ref_ticket = 0;

			// Calculate the total nr of tickets
			try {
				if (isset($sponsor['page_id']) && isset($sponsor['weight'])
				&& in_array($fb_user_id,$like_uids)  )

				$formular = "($ref_ticket+1)" . $sponsor['weight'];
				else $formular = "($ref_ticket+1)";
				$formula = "\$number = ".$formular.";";
				eval($formula);
			} catch (Exception $e) {
				error_log($e);
				$number = $ref_ticket + 1;
			}

			$result[$fb_user_id]=$number;
		}

		return $result;
	}

	/**
	 * Returns if the user likes the sponsors fanpage or not
	 * @param String $fb_user_id Facebook User id
	 * @param String $sponsor_page_id Fanpage Id of the sponsor
	 */
	public function isSponsorLiked($fb_user_id, $sponsor_page_id) {
		$session = new Zend_Session_Namespace('aa_session_' . $this->aa_inst_id);
		$obj_user = new AA_Fb_User($session->fb_api['signed_request'], $session->instance['fb_app_secret'], 
								$session->instance['fb_app_id']);
		// Get User likes
		try {
			$user_likes = $obj_user->getLikes();
			if (is_array($user_likes)){
				foreach ($user_likes as $fb_page) {
					if ($fb_page['id'] == $sponsor_page_id)
						return true;
				}
			}
		} catch (Exception $e) {
			error_log($e);
			return false;
		}

		return false;
	}

  /**
   * Returns all facebook user ids of users which like the fanpage (The user id list has to be comitted)
   * @param Array  $uids List of user ids, which will be checked if they are fan of the fanpage
   * @param Integer $fb_page_id The fanpage id
   * @return Array Facebook User Ids which like the commited fanpage   array(UID1,UID2,UID3,...)
   */
	public function getFanpageLikes($fb_user_ids,$fb_page_id)
	{

    if($fb_page_id == false)
    {
      return array();
    }

		if($fb_user_ids == false)
      return array();

		if(!is_array($fb_user_ids))
      $fb_user_ids=array($fb_user_ids);

		if(is_array($fb_user_ids) && $fb_user_ids == array())
		{
			return array();
		}

		$fb_user_ids="('".implode("','",$fb_user_ids)."')";

		$facebook= new Facebook(array(
      'appId' => $this->session->instance['fb_app_id'],
      'secret' => $this->session->instance['fb_app_secret'],		  
		));

		// Get User likes
		try {
			$fql="SELECT uid FROM page_fan WHERE uid in  $fb_user_ids and page_id='$fb_page_id'";

			$data = $facebook->api(array(
        'method' => 'fql.query',
        'query' =>$fql,
      ));

      //handle data
      $result=array();
      foreach($data as $v)
      {
        $result[]=$v['uid']; 
      }

    } catch (Exception $e) {
      error_log($e);

      echo $e->getMessage();

      echo "<hr/>";
      echo "Error, please flush the facebook fan page and try again";

      exit();
    }						
  
    return $result;
  }

/**
	 * Registers current admin after Data Export in database
	 * @param String $username username or userid of facebook user whose data should be delivered
	 * @param String $aa_inst_id App instance id
	 * @param timestamp $date date for which the user should be registered
	 * @return Successfully registered in DB
	 */
	public function registerAdmin($user_id, $aa_inst_id, $action){
			// Register new Admin Export
			$sql = "INSERT INTO `admin_log` SET `fb_user_id`='$user_id', `aa_inst_id`='$aa_inst_id',
				`ip`='" . $this->getClientIp() . "', `action`='$action'";
			$res = $this->db->query($sql);
			return true;
		}
	
	
	/**
	 * Set the reset round flag for an instance. Like this all participants can vote again.
	 * @param int $aa_inst_id
	 * @param String Y-m-d H:i:s Mysql timestamp
	 */
	public function resetRound($aa_inst_id, $timestamp=""){
		if ($timestamp == "")
			$timestamp = date('Y-m-d H:i:s', time());
		// Check if round reset recordset already exists in DB
		$sql = "SELECT `config_value`
						FROM `app_config` 
						WHERE aa_inst_id=" . $aa_inst_id . 
						" AND `config_key`='round_reset_timestamp'
						LIMIT 1";
		if ($this->db->fetchOne($sql)){
			$sql = "UPDATE `app_config`
					SET `config_value` = '$timestamp'
					WHERE `config_key` = 'round_reset_timestamp' 
					AND `aa_inst_id` = '$aa_inst_id'
					LIMIT 1;";
		} else {
			$sql = "INSERT INTO `app_config`
					       (`aa_inst_id`,
					        `config_key`,
					        `config_value`)
					VALUES ('$aa_inst_id',
					        'round_reset_timestamp',
					        '$timestamp');";
		}
		return $this->db->query($sql);
	}
		
		
		
		
	/**
	 * Checks if another user has referred this user and give the referrer
	 * the desired amount of tickets configured in AA.
	 * @param $user_id the fb user id of the user who might have been invited by a friend
	 * @param $aa_inst_id the AA instance id of the app
	 * @param $extra_tickets the number of extra tickets a referrer gets if the invited friend registers (configured in AA)
	 * @return true if another user invited this user to the app, false if he was not invited
	 */
	public function checkReferrals( $user_id, $aa_inst_id, $extra_tickets ) {
		
		$hasBeenInvited = false;
		$sql = "SELECT * FROM `app_tracking` WHERE `referred_user_id` = '" . $user_id . "' AND `aa_inst_id` = '" . $aa_inst_id . "'";
		$result = $this->db->fetchAll( $sql );
		
		if( $result ) {
			
			$hasBeenInvited = true;
			
			// do a loop, in case the user has been invited by more than one friend
			foreach( $result as $trace ) {
				
				$referrer_id = $trace[ 'fb_user_id' ];
				
				// get the referrer
				$sql = "SELECT * FROM `app_participation` WHERE `fb_user_id` = '" . $referrer_id . "' AND `aa_inst_id` = '" . $aa_inst_id  . "'";
				$user = $this->db->fetchOne( $sql );
				// add the amount of extra tickets for him as configured in AA
				$sql = "UPDATE `app_participation` SET `tickets` = '" . ( $user[ 'tickets' ] +
        $this->session->config[ 'referral_mode' ] ) . "' WHERE `fb_user_id` = '" . $user_id . "' AND `aa_inst_id` = '" . $aa_inst_id . "'";
				$res = $this->db->query( $sql );
				
			}
			
		}
		
		return $hasBeenInvited;
		
	}
	
	

}

?>
