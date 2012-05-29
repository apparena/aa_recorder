<?php
/**
 * Manages all User Tracking. Class depends highly on a certain database structure
 * @author Sebastian Buckpesch (s.buckpesch@iconsultants.eu)
 * @version 0.1
 * @copyright  Copyright (c) 2011 iConsultants UG (http://www.iconsultants.eu)
 * @license    http://www.iconsultants.eu/license/     LGPL
 */
class iCon_Tracking {
	
	private $db;
	private $value;
	private $ic_fb_data;
	private $fb_api;
	
	/**
	 * Initializes
	 */
	function __construct() {
		// Make globals available
		//global $global;
		//$this->db = $global->db;
		$this->db = getDb();
	}
	
	/**
	 * Add Referral to database
	 * @param String $user_id Facebook User Id of the referrer
	 * @param String $referred_user_id Facebook User Id of referred user
	 * @param int $aa_inst_id Application instance id
	 * @param Array $optionArray possible: Timestamp dateRef timestamp of referral
	 * @return boolean
	 */
	public function addReferral($fb_user_id, $referred_user_id, $aa_inst_id, $optionArray = array()){
    //check parameters
    if($fb_user_id == false || $referred_user_id == false || $aa_inst_id == false)
      return false;

		$isDateSensitive = false;
		if (is_array($optionArray) && isset($optionArray['dateRef']) && $optionArray['dateRef'] != ""){
			$isDateSensitive = true;
			$ref_date = date("Y-m-d", $optionArray['dateRef']);
		}
		// User can't refer itself
		if ($fb_user_id == $referred_user_id)
			return false;
			
		// check if user has been already referred by anyone
		$sql = "SELECT * FROM `app_tracking` WHERE `ref_fb_user_id`='$referred_user_id' AND `aa_inst_id`='" . $aa_inst_id . "'";
		if ($isDateSensitive)
			$sql .= " AND SUBSTRING(`timestamp_tracking`,1,10)='" . $ref_date . "'";
		$referrals = $this->db->fetchAll($sql);
		if (count($referrals) == 0) {
			// User not referred (today) --> save referral to the database
			$sql = "INSERT INTO `app_tracking` SET `aa_inst_id`='" . $aa_inst_id . "',
					`fb_user_id`='$fb_user_id', `ref_fb_user_id`='$referred_user_id'";
			if ($isDateSensitive)
				$sql .= ", `ref_date`='" . date("Y-m-d H:i:s", $optionArray['dateRef']) . "'";
			$res = $this->db->query($sql);
			
			
			$sql = "UPDATE `app_participation` SET `tickets` = `tickets` + 1  WHERE `fb_user_id` = '" .  $fb_user_id . "' AND `aa_inst_id` = '" . $aa_inst_id . "'";
			
			$res = $this->db->query( $sql );
			
			
			return true;
		}
		
		
		
		return false;
	}
	
	
	/**
	 * Returns all referrals of a user (at one date)
	 * @param String $user_id Facebook user id of Referrer
	 * @param int $aa_inst_id Application instance id
	 * @param timestamp $ref_date optional date of referrals
	 */
	public function getReferrals($fb_user_id, $aa_inst_id, $ref_date=0) {
     //$round_reset_timestamp = $this->getRoundResetTimestamp($aa_inst_id);

		// check if user has been already referred by anyone
		$sql = "SELECT ref_fb_user_id, ref_date, timestamp_tracking, first_name, last_name ,user_data.name,user_data.fb_user_id as fb_user_id  FROM `app_tracking`
				INNER JOIN `user_data`
				ON (`app_tracking`.`ref_fb_user_id` = `user_data`.`fb_user_id`)
				WHERE `app_tracking`.`fb_user_id`='$fb_user_id' 
				AND `aa_inst_id`='" . $aa_inst_id . "'";

		if ($ref_date != false)
			$sql .= " AND SUBSTRING(`timestamp_tracking`,1,10)='" . date("Y-m-d", $ref_date) . "'";
		return $this->db->fetchAll($sql);
	}
	
	/**
	 * Returns nr of referrals of a user (at one date)
	 * @param String $user_id Facebook user id of Referrer
	 * @param int $aa_inst_id Application instance id
	 * @param timestamp $ref_date optional date of referrals
	 * @return int Nr of referrals of the user
	 */
  public function getNrOfReferrals($fb_user_ids, $aa_inst_id, $ref_date=0) 
  {
		if(!is_array($fb_user_ids))
      $fb_user_ids=array($fb_user_ids);

		$select=$this->db->select();

		$select->from("app_tracking",array("fb_user_id","count(fb_user_id) as referrals") );
		$select->where("aa_inst_id=?",$aa_inst_id);
		$select->where("fb_user_id in (?)",$fb_user_ids);

		if ($ref_date != false)
		$select->where("SUBSTRING(`timestamp_tracking`,1,10)=?",date("Y-m-d", $ref_date));

		$select->group("fb_user_id");

		$rows= $this->db->fetchAll($select);

		$result=array();
		if($rows != false)
		{
			foreach($rows as $row)
			{
				$result[$row['fb_user_id']]=$row['referrals'];
			}

		}

		//if fb user id do not in the result,
	    //add this fb user id ,and tickets_nr= 0
	    foreach($fb_user_ids as $fb_user_id)
	    {
	      if(!isset($result[$fb_user_id]))
	        $result[$fb_user_id]=0;
	    }
	
	
	    return $result;
	}

}
