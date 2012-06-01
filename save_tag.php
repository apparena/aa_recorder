<?
	include_once( 'init_session.php' );
	
	$connection = mysql_connect( $database_host, $database_user, $database_pass );
	
	if ( !$connection ) {
	
		die( 'sql connection failed: ' . mysql_error() );
		 
	}
	$fb_user_id = 0;
	
	if( isset( $_POST[ 'fb_user_id' ] ) ) {
	
		$fb_user_id = $_POST[ 'fb_user_id' ];
	
	} else {
	
		die( "invalid session! exiting..." );
		exit( -1 );
	
	}
	
	$fb_user_name = "";
	$fb_user_name = $_POST[ 'fb_user_name' ];
	

	
	// Get client ip address
	if ( isset($_SERVER["REMOTE_ADDR"]))
		$client_ip = $_SERVER["REMOTE_ADDR"];
	
	else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		$client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	
	else if ( isset($_SERVER["HTTP_CLIENT_IP"]))
		$client_ip = $_SERVER["HTTP_CLIENT_IP"];
	
	$db =getDb();
	
	$app_start_date= app_start_date();

  //delete exists  records

	// check if the user already has tagged the image
	$select = "SELECT * FROM `tags` WHERE `fb_user_id` = '" . $fb_user_id . "' AND `aa_inst_id` = $aa_inst_id  and timestamp > '$app_start_date'";

  $rows=$db->fetchAll($select);

  //delete sound files
  foreach($rows as $row)
  {
     $sound_path=$row['sound_path'];
     @unlink($sound_path);
  }

  //delete records
	$sql = "delete  FROM `tags` WHERE `fb_user_id` = '" . $fb_user_id . "' AND `aa_inst_id` = $aa_inst_id  and timestamp > '$app_start_date'";
  $db->query($sql);

	
  //insert new record
	// the user didnt tag the image yet, so save his tag
  $saveSql = "INSERT INTO `tags` 
  SET `aa_inst_id` = " . $aa_inst_id . ", 
  `fb_user_id` = '" . $fb_user_id . "', 
  `fb_user_name` = '" . utf8_encode( $fb_user_name ) . "', 
  `ip_address` = '" . $client_ip . "'";

  $saveResult = $db->query( $saveSql );
	
	
	echo "true";
	
?>
